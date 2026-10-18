<!-- SIDEBAR NAVIGATION -->
<aside class="w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col shrink-0 shadow-sm transition-colors">
    <nav class="flex-1 p-4 space-y-2">
        @foreach ($pages as $page)
            @isset($page['command'])
                <x-nav-command :page="$page" />
            @else
                <x-nav-link :page="$page" :active="request()->routeIs($page['route'])" />
            @endisset
        @endforeach
    </nav>

    <!-- VERSION -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-800 flex justify-center">
        <span class="text-xs bg-amber-950 text-amber-100 dark:bg-amber-500/15 dark:text-amber-300 px-2.5 py-1 rounded-md font-mono font-bold tracking-wide">VERSION {{ $version }}</span>
    </div>
</aside>
