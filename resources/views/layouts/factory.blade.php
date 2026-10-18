<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Control Panel') - BEDO IoT Factory System</title>
    <!-- Apply the saved theme before first paint (no white flash in dark mode) -->
    <script>
        (function () {
            let theme = null;
            try { theme = localStorage.getItem('theme'); } catch (e) {}
            const dark = theme ? theme === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    <!-- Tailwind, Alpine, mqtt.js and the Inter font, bundled by Vite (no internet needed) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 dark:bg-gray-950 dark:text-gray-200 transition-colors h-dvh overflow-hidden flex flex-col justify-between"
      x-data="factoryDashboard(@js($clientConfig))" @keydown.escape.window="sidebarOpen = false">

    @include('partials.header')

    <!-- MAIN APP CONTENT & SIDEBAR -->
    <div class="relative flex flex-1 overflow-hidden">

        <!-- Phone / tablet: dark backdrop behind the open sidebar -->
        <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black/50 lg:hidden" aria-hidden="true"></div>

        @include('partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 min-w-0 overflow-y-auto p-3 sm:p-4 bg-gray-100 dark:bg-gray-950 flex flex-col justify-between">
            <div class="space-y-4">
                <!-- Active alarm (factory/alarm) -->
                <div x-show="alarmActive" x-cloak role="alert"
                     class="flex flex-wrap sm:flex-nowrap items-start gap-3 sm:gap-4 rounded-2xl border border-red-300 bg-red-50 dark:bg-red-950/60 dark:border-red-800 p-4">
                    <span class="text-2xl leading-none" aria-hidden="true">🚨</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-red-700 dark:text-red-300" x-text="alarm?.title || 'Alarm'"></p>
                        <p class="text-sm text-red-700/90 dark:text-red-200/90" x-show="alarm?.detail" x-text="alarm?.detail"></p>
                        <p class="text-xs font-mono text-red-600/80 dark:text-red-300/70 mt-1">
                            <span x-show="alarm?.part" x-text="'Part ' + alarm?.part"></span>
                            <span x-show="alarm?.time" x-text="alarm?.time"></span>
                        </p>
                    </div>
                    <button type="button" @click="acknowledgeAlarm()"
                            class="w-full sm:w-auto shrink-0 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm shadow-sm">
                        Stop &amp; Acknowledge
                    </button>
                </div>

                <!-- Commands that move the machine need REMOTE ON on the trainer -->
                <div x-show="remoteOn === false" x-cloak role="status"
                     class="rounded-2xl border border-amber-300 bg-amber-50 dark:bg-amber-500/10 dark:border-amber-500/40 px-4 py-3 text-sm font-semibold text-amber-900 dark:text-amber-200">
                    🔒 Remote control is OFF – press <span class="font-mono">REMOTE</span> on the trainer to send commands. STOP always works.
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    @include('partials.footer')

    <!-- Notices (trainer replies, broker offline, no reply …) -->
    <div x-show="notice" x-cloak x-transition.opacity role="status" aria-live="polite"
         class="fixed bottom-20 inset-x-3 sm:inset-x-auto sm:bottom-28 sm:right-6 z-50 sm:max-w-sm px-4 py-3 rounded-xl shadow-lg text-sm font-semibold"
         :class="{ 'bg-red-600 text-white': notice?.type === 'error', 'bg-emerald-600 text-white': notice?.type === 'success', 'bg-gray-900 text-white': notice?.type === 'info' }">
        <span x-text="notice?.message"></span>
    </div>

</body>
</html>
