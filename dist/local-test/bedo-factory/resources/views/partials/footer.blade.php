<!-- DARK INDUSTRIAL FOOTER — live from factory/counts, factory/encoder, factory/message -->
<footer class="bg-[#0f172a] border-t border-slate-800 text-white px-6 py-3.5 shrink-0 shadow-lg">
    <div class="grid grid-cols-2 sm:grid-cols-7 gap-3 font-mono text-center mb-2.5">
        @foreach ($counters as $key => $counter)
            <div class="bg-slate-900/90 p-2.5 rounded-xl border border-slate-800">
                <span class="text-gray-400 block text-[10px] uppercase tracking-wider">{{ $counter['label'] }}</span>
                <span class="text-base font-extrabold {{ $counter['color'] }}" x-text="counts?.[@js($key)] ?? '—'">—</span>
            </div>
        @endforeach

        <div class="bg-slate-900/90 p-2.5 rounded-xl border border-slate-800 flex flex-col justify-center">
            <span class="text-gray-400 block text-[10px] uppercase tracking-wider">Encoder Status</span>
            <span class="text-xs text-amber-400 font-bold" x-text="encoderText">—</span>
        </div>
    </div>

    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between gap-4 text-xs text-gray-400 font-sans px-1">
        <span class="font-bold text-gray-200 truncate" x-text="message || 'IoT Factory System'" :title="message">IoT Factory System</span>
        <span class="font-bold text-gray-200 shrink-0">Copyright © {{ date('Y') }} BEDO Innovating Education</span>
    </div>
</footer>
