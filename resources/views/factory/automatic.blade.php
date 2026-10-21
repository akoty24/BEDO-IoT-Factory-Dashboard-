@extends('layouts.factory')

@section('title', 'Automatic Mode')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 sm:gap-5">
        <div class="xl:col-span-5">
            <div class="bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-4 sm:p-6 shadow-sm h-full flex flex-col gap-4">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Automatic Sequence Control</h3>

                <!-- factory/cmd/auto start | feed,  factory/cmd/stop -->
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" @click="autoCommand('start')" :disabled="!canControl"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 min-h-12 rounded-xl text-sm shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                        ▶ Start
                    </button>
                    {{-- Feed Part hidden for now. To bring it back: uncomment and set the grid above to grid-cols-3.
                    <button type="button" @click="autoCommand('feed')" :disabled="!canControl"
                            class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 min-h-12 rounded-xl text-sm shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                        ⬇ Feed Part
                    </button>
                    --}}
                    <button type="button" @click="sendMachineCommand('stop')"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 min-h-12 rounded-xl text-sm shadow-sm transition">
                        ■ Stop
                    </button>
                </div>

                <!-- Live line state (factory/state) -->
                <dl class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl p-3">
                        <dt class="text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Mode</dt>
                        <dd class="font-mono font-bold text-gray-800 dark:text-gray-100" x-text="display(machine?.mode)">—</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl p-3">
                        <dt class="text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Step</dt>
                        <dd class="font-mono font-bold text-gray-800 dark:text-gray-100" x-text="display(machine?.step)">—</dd>
                    </div>
                </dl>

                <!-- What the line is doing now (factory/message) -->
                <div x-show="message" x-cloak
                     class="p-4 rounded-xl bg-amber-50 border border-amber-200 dark:bg-amber-500/10 dark:border-amber-500/30 text-xs text-amber-900 dark:text-amber-200 font-mono leading-relaxed"
                     x-text="message"></div>
            </div>
        </div>

        <x-sensors-panel :sensors="$sensors" class="xl:col-span-7" />
    </div>
@endsection
