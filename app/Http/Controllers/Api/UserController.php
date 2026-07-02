<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientApp;
use App\Services\SsoAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly SsoAuthService $authService,
    ) {}

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('roles:id,name', 'clientApps:id,name,slug,domain');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'email_verified_at' => $user->email_verified_at,
            'is_active' => $user->is_active,
            'sso_roles' => $user->roles->pluck('name'),
            'sso_permissions' => $user->getAllPermissions()->pluck('name'),
            'apps' => $user->clientApps->map(fn ($app) => [
                'id' => $app->id,
                'name' => $app->name,
                'slug' => $app->slug,
                'domain' => $app->domain,
            ]),
            'created_at' => $user->created_at,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();

        if ($token) {
            $token->refreshToken?->revoke();
            $token->revoke();
        }

        return response()->json(['message' => 'Successfully logged out.']);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $user = $request->user();

        $this->authService->logout($user);

        $postLogoutUris = ClientApp::where('is_active', true)
            ->whereNotNull('post_logout_redirect_uri')
            ->pluck('post_logout_redirect_uri');

        return response()->json([
            'message' => 'All tokens revoked. SSO session terminated.',
            'post_logout_redirect_uris' => $postLogoutUris,
        ]);
    }
}
