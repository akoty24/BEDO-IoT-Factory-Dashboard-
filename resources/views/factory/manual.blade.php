@extends('layouts.factory')

@section('title', 'Manual Mode')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 sm:gap-5">
        <!-- Steps & Outputs Column -->
        <div class="xl:col-span-5 space-y-3 sm:space-y-5">
            <div class="bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wider mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Sequences</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    @foreach ($steps as $step => $label)
                        <x-command-button :step="$step" class="shadow-2xs">{{ $label }}</x-command-button>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
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

        <x-sensors-panel :sensors="$sensors" class="xl:col-span-7" />
    </div>
@endsection
