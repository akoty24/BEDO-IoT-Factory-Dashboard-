@props(['page', 'active' => false])

@php
    $classes = match ($page['variant']) {
        'green' => $active ? 'bg-green-600 text-white font-bold shadow-md' : 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-500/10',
        'red' => $active ? 'bg-red-600 text-white font-bold shadow-md' : 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10',
        default => $active ? 'bg-amber-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-700 dark:text-gray-300 dark:hover:bg-amber-500/10 dark:hover:text-amber-400',
    };
@endphp

<a href="{{ route($page['route']) }}" @if ($active) aria-current="page" @endif
   class="{{ $classes }} w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-200 text-sm font-semibold">
    <span class="text-base">{{ $page['icon'] }}</span>
    <span>{{ $page['label'] }}</span>
</a>
