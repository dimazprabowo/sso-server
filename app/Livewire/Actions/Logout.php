<?php

namespace App\Livewire\Actions;

use App\Services\SsoAuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    public function __invoke(): void
    {
        $user = Auth::user();

        if ($user) {
            app(SsoAuthService::class)->logout($user);
        }

        Session::invalidate();
        Session::regenerateToken();
    }
}
