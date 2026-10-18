
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['step' => null, 'output' => null, 'highlighted' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['step' => null, 'output' => null, 'highlighted' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $click = $output !== null
        ? 'toggleOutput('.\Illuminate\Support\Js::from($output).')'
        : 'runStep('.\Illuminate\Support\Js::from($step).')';

    $classes = $highlighted
        ? 'bg-amber-100 border-amber-300 font-bold text-amber-900 hover:bg-amber-200 dark:bg-amber-500/20 dark:border-amber-500/50 dark:text-amber-200 dark:hover:bg-amber-500/30'
        : 'bg-gray-50 border-gray-200 font-semibold text-gray-800 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-amber-500/10 dark:hover:text-amber-400 dark:hover:border-amber-500/50';
?>

<button type="button" @click="<?php echo e($click); ?>" :disabled="!canControl"
    <?php if($output !== null): ?> :aria-pressed="isOutputOn(<?php echo \Illuminate\Support\Js::from($output)->toHtml() ?>).toString()" <?php endif; ?>
    <?php echo e($attributes->merge(['class' => "$classes border p-2.5 rounded-xl text-xs transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"])); ?>>
    <?php if($output !== null): ?>
        <span class="w-2.5 h-2.5 rounded-full shrink-0 transition-colors"
              :class="isOutputOn(<?php echo \Illuminate\Support\Js::from($output)->toHtml() ?>) ? 'bg-emerald-500 glow-green' : 'bg-gray-300 dark:bg-gray-600'"></span>
    <?php endif; ?>
    <span><?php echo e($slot); ?></span>
</button>
<?php /**PATH C:\mohamed Saber\my-laravel-app\dist\local-test\bedo-factory\resources\views/components/command-button.blade.php ENDPATH**/ ?>