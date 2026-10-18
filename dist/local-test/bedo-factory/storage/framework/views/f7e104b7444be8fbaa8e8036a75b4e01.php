<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['page', 'active' => false]));

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

foreach (array_filter((['page', 'active' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = match ($page['variant']) {
        'green' => $active ? 'bg-green-600 text-white font-bold shadow-md' : 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-500/10',
        'red' => $active ? 'bg-red-600 text-white font-bold shadow-md' : 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10',
        default => $active ? 'bg-amber-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-700 dark:text-gray-300 dark:hover:bg-amber-500/10 dark:hover:text-amber-400',
    };
?>

<a href="<?php echo e(route($page['route'])); ?>" <?php if($active): ?> aria-current="page" <?php endif; ?>
   class="<?php echo e($classes); ?> w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-200 text-sm font-semibold">
    <span class="text-base"><?php echo e($page['icon']); ?></span>
    <span><?php echo e($page['label']); ?></span>
</a>
<?php /**PATH C:\mohamed Saber\my-laravel-app\dist\local-test\bedo-factory\resources\views\components\nav-link.blade.php ENDPATH**/ ?>