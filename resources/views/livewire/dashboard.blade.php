@if ($canViewDashboard)
    @include('livewire.dashboard.dashboard-content')
@else
    @include('livewire.dashboard.user-info-only', ['user' => $user])
@endif
