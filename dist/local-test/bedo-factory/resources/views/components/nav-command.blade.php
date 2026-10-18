@props(['page'])

@php
    // Highlighted (filled) when the machine reports the matching state.
    [$activeClasses, $idleClasses] = match ($page['variant']) {
        'green' => ['bg-green-600 text-white font-bold shadow-md', 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-500/10'],
        'red' => ['bg-red-600 text-white font-bold shadow-md', 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10'],
        default => ['bg-amber-500 text-white font-bold shadow-md', 'text-gray-700 hover:bg-amber-50 hover:text-amber-700 dark:text-gray-300 dark:hover:bg-amber-500/10 dark:hover:text-amber-400'],
    };
    $command = $page['command'];
@endphp

<button type="button"
        @click="sendMachineCommand(@js($command))"
        {{-- STOP is an emergency stop: it is never disabled. START needs REMOTE ON and no pending command. --}}
        :disabled="@js($command !== 'stop') && (pendingCommand !== null || !canControl)"
        :aria-busy="(pendingCommand === @js($command)).toString()"
        :class="isMachineCommandActive(@js($command)) ? @js($activeClasses) : @js($idleClasses)"
        class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-200 text-sm font-semibold disabled:opacity-60 disabled:cursor-not-allowed">
    <span class="text-base">{{ $page['icon'] }}</span>
    <span class="flex-1 text-left">{{ $page['label'] }}</span>
    <span x-show="pendingCommand === @js($command)" x-cloak class="w-4 h-4 rounded-full border-2 border-current border-t-transparent animate-spin" aria-hidden="true"></span>
</button>
