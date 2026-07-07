<?php

namespace App\Livewire;

use App\Models\ClientApp;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $canViewDashboard = $user->can('dashboard_view');

        $data = [
            'user' => $user,
            'canViewDashboard' => $canViewDashboard,
        ];

        if ($canViewDashboard) {
            if ($user->hasRole('super-admin')) {
                $apps = ClientApp::where('is_active', true)->with('oauthClient')->get();
            } else {
                $apps = $user->clientApps()->where('is_active', true)->with('oauthClient')->get();
            }

            $activeTokenClientIds = $user->tokens()
                ->where('revoked', false)
                ->pluck('client_id')
                ->unique()
                ->toArray();

            $stats = [];
            if ($user->can('users_view')) {
                $stats['totalUsers'] = User::count();
                $stats['activeUsers'] = User::where('is_active', true)->count();
            }
            if ($user->can('client_apps_view')) {
                $stats['totalApps'] = ClientApp::count();
                $stats['activeApps'] = ClientApp::where('is_active', true)->count();
            }

            $data['apps'] = $apps;
            $data['activeTokenClientIds'] = $activeTokenClientIds;
            $data['stats'] = $stats;
        }

        return view('livewire.dashboard', $data);
    }
}
