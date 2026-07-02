@props([
    'target' => null,
    'label' => 'Batal',
    'loadingText' => 'Memuat...',
    'icon' => false,
    'variant' => 'default',
])

@php
    $variants = [
        'default' => 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700',
        'bordered' => 'text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600',
        'muted' => 'text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600',
    ];

    $variantClass = $variants[$variant] ?? $variants['default'];

    $baseClass = $icon
        ? 'p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition disabled:opacity-50'
        : "inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed {$variantClass}";
@endphp

@if($target)
    <button
        x-data="{ loading: false }"
        @click="loading = true"
        :disabled="loading"
        {{ $attributes->merge(['type' => 'button', 'class' => $baseClass]) }}
    >
        @if($icon)
            <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <svg x-show="loading" x-cloak class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        @else
            <span x-show="!loading">{{ $label }}</span>
            <svg x-show="loading" x-cloak class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            @if($loadingText)
                <span x-show="loading" x-cloak>{{ $loadingText }}</span>
            @endif
        @endif
    </button>
@else
    <button
        {{ $attributes->merge(['type' => 'button', 'class' => $baseClass]) }}
    >
        @if($icon)
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        @else
            {{ $label }}
        @endif
    </button>
@endif
