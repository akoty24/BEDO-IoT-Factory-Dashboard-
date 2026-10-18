@props(['sensors'])

<!-- Sensors Matrix (Read-only Feedback Display) -->
<div {{ $attributes->merge(['class' => 'bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-4 shadow-sm flex flex-col justify-between']) }}>
    <div>
        <div class="flex items-center justify-between mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wider">Sensors (Live Feedback Status)</h3>
            <span class="text-xs font-mono font-bold"
                  :class="connected && trainerOnline ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400'"
                  x-text="connected && trainerOnline ? '● Live Feed Active' : '○ No Live Feed'">○ No Live Feed</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-5 xl:grid-cols-6 gap-2.5 text-center">
            @foreach ($sensors as $sensor)
                <div class="bg-gray-50 border border-gray-200 dark:bg-gray-800/60 dark:border-gray-700 p-2 rounded-xl flex flex-col items-center justify-center shadow-2xs hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div :class="isActive(@js($sensor['id'])) ? 'bg-emerald-500 glow-green' : 'bg-gray-300 dark:bg-gray-600'" class="w-4 h-4 rounded-full mb-1.5 transition-all duration-300"></div>
                    <span class="text-[10px] text-gray-700 dark:text-gray-300 leading-tight font-medium">{{ $sensor['name'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
