<!-- TOP HEADER -->
<header class="bg-[#f59e0b] dark:bg-amber-600 text-gray-900 px-6 py-3 grid grid-cols-3 items-center shrink-0 shadow-md border-b border-amber-600 dark:border-amber-700 transition-colors">
    <div class="flex items-center">
        <div class="bg-white rounded-lg px-3 py-1 shadow-sm">
            <img src="<?php echo e(asset('images/bedo-logo.webp')); ?>" alt="BEDO - Innovating Education" class="h-9 w-auto">
        </div>
    </div>

    <h1 class="text-lg font-extrabold tracking-wider uppercase text-gray-950 text-center whitespace-nowrap"><?php echo e($appName); ?></h1>

    <div class="flex items-center justify-end space-x-6 text-sm font-semibold">
        <!-- System Health: line status from factory/state + factory/online -->
        <div class="flex items-center space-x-2">
            <span class="text-gray-950 font-bold whitespace-nowrap">System Health:</span>
            <span class="w-3 h-3 rounded-full shrink-0" :class="machineStateStyle.dot"></span>
            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded shadow-sm whitespace-nowrap" :class="machineStateStyle.badge" x-text="machineStateLabel" aria-live="polite"></span>
        </div>

        <!-- REMOTE button on the trainer -->
        <div class="flex items-center space-x-2" x-show="remoteOn !== null" x-cloak>
            <span class="text-gray-950 font-bold whitespace-nowrap">Remote:</span>
            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded shadow-sm whitespace-nowrap"
                  :class="remoteOn ? 'bg-emerald-950 text-emerald-300' : 'bg-red-950 text-red-300'"
                  x-text="remoteOn ? 'ON' : 'OFF'"></span>
        </div>

        <!-- Network / MQTT Status -->
        <!-- <div class="flex items-center space-x-2">
            <span class="text-gray-950 font-bold whitespace-nowrap">MQTT Status:</span>
            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded shadow-sm whitespace-nowrap" :class="brokerStatus === 'Connected' ? 'bg-emerald-950 text-emerald-300' : 'bg-red-950 text-red-300'" x-text="brokerStatus"></span>
        </div> -->

        <!-- Real-time Clock -->
        <div class="font-mono bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100 px-3 py-1 rounded-lg border border-amber-600/40 dark:border-gray-700 shadow-inner font-bold text-sm" x-text="currentTime"></div>

        <!-- Light / Dark mode toggle -->
        <button type="button" @click="toggleDarkMode()"
                :aria-pressed="darkMode.toString()"
                :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                aria-label="Toggle dark mode"
                class="relative inline-flex h-8 w-14 shrink-0 items-center rounded-full border border-amber-700/40 bg-white/70 dark:bg-gray-900 dark:border-gray-700 shadow-inner transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900">
            <span class="absolute left-1.5 text-xs" aria-hidden="true">☀️</span>
            <span class="absolute right-1.5 text-xs" aria-hidden="true">🌙</span>
            <span class="relative z-10 inline-block h-6 w-6 rounded-full bg-amber-500 dark:bg-amber-400 shadow transition-transform duration-200"
                  :class="darkMode ? 'translate-x-7' : 'translate-x-1'"></span>
        </button>
    </div>
</header>
<?php /**PATH C:\mohamed Saber\my-laravel-app\dist\local-test\bedo-factory\resources\views\partials\header.blade.php ENDPATH**/ ?>