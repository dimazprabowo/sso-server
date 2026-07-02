<?php

namespace App\Http\Middleware;

use App\Models\ClientApp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAppAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $clientId = $request->input('client_id');

        if (! $clientId) {
            return $next($request);
        }

        $clientApp = ClientApp::where('oauth_client_id', $clientId)->first();

        if (! $clientApp) {
            return $next($request);
        }

        if (! $clientApp->is_active) {
            return $this->denyAccess($request, $clientApp, 'Aplikasi ini sedang tidak aktif.', 'app_disabled');
        }

        if (! $user->hasAppAccessByOAuthClientId($clientId)) {
            return $this->denyAccess($request, $clientApp, 'Anda tidak memiliki akses ke aplikasi ini. Hubungi administrator SSO.', 'access_denied');
        }

        return $next($request);
    }

    private function denyAccess(Request $request, ClientApp $clientApp, string $message, string $error): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $error,
                'message' => $message,
            ], 403);
        }

        // For OAuth flow: redirect back to client app with error params
        $redirectUri = $clientApp->redirect_uri;

        if ($redirectUri) {
            $separator = str_contains($redirectUri, '?') ? '&' : '?';

            return redirect($redirectUri . $separator . http_build_query([
                'error' => $error,
                'error_description' => $message,
                'state' => $request->input('state'),
            ]));
        }

        abort(403, $message);
    }
}
