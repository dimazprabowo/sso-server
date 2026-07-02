<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\Gate;

trait HasMenuItems
{
    public function getMenuItems(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        $items = [];

        // Dashboard — always visible
        $items[] = [
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'home',
            'active' => request()->routeIs('dashboard'),
        ];

        // Admin section
        $adminChildren = [];

        if (Gate::allows('users_view')) {
            $adminChildren[] = [
                'name' => 'Kelola User',
                'route' => 'users.index',
                'icon' => 'users',
                'active' => request()->routeIs('users.*'),
            ];
        }

        if (Gate::allows('roles_view')) {
            $adminChildren[] = [
                'name' => 'Role & Permission',
                'route' => 'roles.index',
                'icon' => 'shield',
                'active' => request()->routeIs('roles.*'),
            ];
        }

        if (Gate::allows('client_apps_view')) {
            $adminChildren[] = [
                'name' => 'Client Apps',
                'route' => 'client-apps.index',
                'icon' => 'server',
                'active' => request()->routeIs('client-apps.*'),
            ];
        }

        if (Gate::allows('user_access_view')) {
            $adminChildren[] = [
                'name' => 'Akses Aplikasi',
                'route' => 'user-access.index',
                'icon' => 'link',
                'active' => request()->routeIs('user-access.*'),
            ];
        }

        if (!empty($adminChildren)) {
            $items[] = [
                'name' => 'Manajemen',
                'icon' => 'database',
                'children' => $adminChildren,
                'active' => collect($adminChildren)->contains('active', true),
            ];
        }

        // Settings
        // $items[] = [
        //     'name' => 'Profil',
        //     'route' => 'profile',
        //     'icon' => 'cog',
        //     'active' => request()->routeIs('profile'),
        // ];

        return $items;
    }
}
