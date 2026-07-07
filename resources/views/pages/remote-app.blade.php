<x-app-layout title="Kelola Remote App">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Kelola Remote App
        </h2>
    </x-slot>

    <livewire:remote-app-manager :app="$app" />
</x-app-layout>
