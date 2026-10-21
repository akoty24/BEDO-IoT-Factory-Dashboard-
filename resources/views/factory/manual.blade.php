@extends('layouts.factory')

@section('title', 'Manual Mode')

@php
    // Phone / tablet (below lg): the three panels become tabs. lg and up: all panels visible.
    $tabs = [
        'sequences' => 'Sequences',
        'actuators' => 'Actuators',
        'sensors' => 'Sensors',
    ];
@endphp

@section('content')
    <div x-data="{ tab: 'sequences' }" class="space-y-3">

        <!-- Tab bar (phone / tablet only) -->
        <div role="tablist" aria-label="Manual mode sections"
             class="lg:hidden sticky -top-3 sm:-top-4 z-20 -mx-3 sm:-mx-4 px-3 sm:px-4 py-2 bg-gray-100/95 dark:bg-gray-950/95 backdrop-blur">
            <div class="grid grid-cols-3 gap-1 p-1 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                @foreach ($tabs as $key => $label)
                    <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                            @click="tab = @js($key)"
                            :aria-selected="(tab === @js($key)).toString()"
                            :class="tab === @js($key)
                                ? 'bg-amber-500 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'"
                            class="min-h-11 rounded-lg px-2 text-sm font-bold transition flex items-center justify-center gap-1.5">
                        <span>{{ $label }}</span>
                        @if ($key === 'actuators')
                            <span x-show="activeOutputsCount" x-cloak x-text="activeOutputsCount"
                                  class="min-w-5 rounded-full bg-emerald-500 px-1.5 text-[11px] leading-5 text-white"
                                  :title="activeOutputsCount + ' outputs ON'"></span>
                        @elseif ($key === 'sensors')
                            <span x-show="activeSensorsCount" x-cloak x-text="activeSensorsCount"
                                  class="min-w-5 rounded-full bg-emerald-500 px-1.5 text-[11px] leading-5 text-white"
                                  :title="activeSensorsCount + ' sensors active'"></span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 sm:gap-5">
            <!-- Steps & Outputs Column -->
            <div class="xl:col-span-5 space-y-3 sm:space-y-5" :class="{ 'max-lg:hidden': tab === 'sensors' }">
                <div id="panel-sequences" role="tabpanel" aria-labelledby="tab-sequences"
                     :class="{ 'max-lg:hidden': tab !== 'sequences' }"
                     class="bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wider mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Sequences</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @foreach ($steps as $step => $label)
                            <x-command-button :step="$step" class="shadow-2xs">{{ $label }}</x-command-button>
                        @endforeach
                    </div>
                </div>

                <div id="panel-actuators" role="tabpanel" aria-labelledby="tab-actuators"
                     :class="{ 'max-lg:hidden': tab !== 'actuators' }"
                     class="bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wider">Actuators</h3>
                        <!-- factory/cmd/pwm on|off, state from factory/state.pwm -->

                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @foreach ($outputs as $name => $label)
                            <x-command-button :output="$name" :highlighted="$name === 'light_red'">{{ $label }}</x-command-button>
                        @endforeach
                    </div>
                </div>
            </div>

            <x-sensors-panel :sensors="$sensors" id="panel-sensors" role="tabpanel" aria-labelledby="tab-sensors"
                             class="xl:col-span-7" ::class="{ 'max-lg:hidden': tab !== 'sensors' }" />
        </div>
    </div>
@endsection
