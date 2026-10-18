{{--
    A machine command button. Pass exactly one of:
      step="home"        → factory/cmd/step home
      output="conveyor"  → factory/cmd/output, toggles it; the LED shows factory/outputs
    Disabled while the machine can't take commands (broker down / REMOTE off).
--}}
@props(['step' => null, 'output' => null, 'highlighted' => false])

@php
    $click = $output !== null
        ? 'toggleOutput('.\Illuminate\Support\Js::from($output).')'
        : 'runStep('.\Illuminate\Support\Js::from($step).')';

    $classes = $highlighted
        ? 'bg-amber-100 border-amber-300 font-bold text-amber-900 hover:bg-amber-200 dark:bg-amber-500/20 dark:border-amber-500/50 dark:text-amber-200 dark:hover:bg-amber-500/30'
        : 'bg-gray-50 border-gray-200 font-semibold text-gray-800 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-amber-500/10 dark:hover:text-amber-400 dark:hover:border-amber-500/50';
@endphp

<button type="button" @click="{{ $click }}" :disabled="!canControl"
    @if ($output !== null) :aria-pressed="isOutputOn(@js($output)).toString()" @endif
    {{ $attributes->merge(['class' => "$classes border p-2.5 rounded-xl text-xs transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"]) }}>
    @if ($output !== null)
        <span class="w-2.5 h-2.5 rounded-full shrink-0 transition-colors"
              :class="isOutputOn(@js($output)) ? 'bg-emerald-500 glow-green' : 'bg-gray-300 dark:bg-gray-600'"></span>
    @endif
    <span>{{ $slot }}</span>
</button>
