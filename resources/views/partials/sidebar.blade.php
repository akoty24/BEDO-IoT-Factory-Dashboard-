<!-- SIDEBAR NAVIGATION
     Desktop (lg+): always visible. Phone / tablet: drawer opened by the ☰ button. -->
<aside id="sidebar"
       {{-- Hidden by CSS below lg from the first paint (no flash before Alpine starts);
            the inline style slides it in when the ☰ button opens it. --}}
       class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] max-lg:-translate-x-full lg:static lg:z-auto lg:w-64 lg:max-w-none bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col shrink-0 shadow-xl lg:shadow-sm transition-transform duration-200 ease-out"
       :style="sidebarOpen ? 'translate: 0' : ''">

    <!-- Drawer title + close (phone / tablet only) -->
    <div class="lg:hidden flex items-center justify-between px-4 pt-4">
        <span class="text-sm font-extrabold tracking-wider uppercase text-gray-800 dark:text-gray-100">Menu</span>
        <button type="button" @click="sidebarOpen = false" aria-label="Close menu"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-2">
        @foreach ($pages as $page)
            @isset($page['command'])
                <x-nav-command :page="$page" />
            @else
                <x-nav-link :page="$page" :active="request()->routeIs($page['route'])" />
            @endisset
        @endforeach
    </nav>

    <!-- Clock (phones hide it in the header) + VERSION -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-800 flex items-center justify-center gap-3">
        <span class="lg:hidden font-mono font-bold text-sm text-gray-700 dark:text-gray-200" x-text="currentTime"></span>
        <span class="text-xs bg-amber-950 text-amber-100 dark:bg-amber-500/15 dark:text-amber-300 px-2.5 py-1 rounded-md font-mono font-bold tracking-wide">VERSION {{ $version }}</span>
    </div>
</aside>
