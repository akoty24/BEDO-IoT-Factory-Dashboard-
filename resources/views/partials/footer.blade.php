<!-- DARK INDUSTRIAL FOOTER — live from factory/counts, factory/encoder, factory/message
     Phone: one compact row you swipe sideways. md+: grid. -->
<footer class="bg-[#0f172a] border-t border-slate-800 text-white px-3 py-2 sm:px-6 sm:py-3.5 shrink-0 shadow-lg pb-[max(0.5rem,env(safe-area-inset-bottom))]">
    <div class="flex md:grid md:grid-cols-7 gap-2 sm:gap-3 overflow-x-auto snap-x font-mono text-center mb-2 sm:mb-2.5 [scrollbar-width:none]">
        @foreach ($counters as $key => $counter)
            <div class="snap-start shrink-0 min-w-[6.5rem] md:min-w-0 bg-slate-900/90 px-2 py-1.5 sm:p-2.5 rounded-xl border border-slate-800">
                <span class="text-gray-400 block text-[10px] uppercase tracking-wider whitespace-nowrap md:whitespace-normal">{{ $counter['label'] }}</span>
                <span class="text-sm sm:text-base font-extrabold {{ $counter['color'] }}" x-text="counts?.[@js($key)] ?? '—'">—</span>
            </div>
        @endforeach

        <div class="snap-start shrink-0 min-w-[9rem] md:min-w-0 bg-slate-900/90 px-2 py-1.5 sm:p-2.5 rounded-xl border border-slate-800 flex flex-col justify-center">
            <span class="text-gray-400 block text-[10px] uppercase tracking-wider whitespace-nowrap">Encoder Status</span>
            <span class="text-xs text-amber-400 font-bold" x-text="encoderText">—</span>
        </div>
    </div>

    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between gap-4 text-xs text-gray-400 font-sans px-1">
        <span class="font-bold text-gray-200 truncate" x-text="message || 'IoT Factory System'" :title="message">IoT Factory System</span>
        <span class="hidden sm:inline font-bold text-gray-200 shrink-0">Copyright © {{ date('Y') }} BEDO Innovating Education</span>
    </div>
</footer>
