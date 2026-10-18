<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['page']));

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

foreach (array_filter((['page']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Highlighted (filled) when the machine reports the matching state.
    [$activeClasses, $idleClasses] = match ($page['variant']) {
        'green' => ['bg-green-600 text-white font-bold shadow-md', 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-500/10'],
        'red' => ['bg-red-600 text-white font-bold shadow-md', 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10'],
        default => ['bg-amber-500 text-white font-bold shadow-md', 'text-gray-700 hover:bg-amber-50 hover:text-amber-700 dark:text-gray-300 dark:hover:bg-amber-500/10 dark:hover:text-amber-400'],
    };
    $command = $page['command'];
?>

<button type="button"
        @click="sendMachineCommand(<?php echo \Illuminate\Support\Js::from($command)->toHtml() ?>)"
        
        :disabled="<?php echo \Illuminate\Support\Js::from($command !== 'stop')->toHtml() ?> && (pendingCommand !== null || !canControl)"
        :aria-busy="(pendingCommand === <?php echo \Illuminate\Support\Js::from($command)->toHtml() ?>).toString()"
        :class="isMachineCommandActive(<?php echo \Illuminate\Support\Js::from($command)->toHtml() ?>) ? <?php echo \Illuminate\Support\Js::from($activeClasses)->toHtml() ?> : <?php echo \Illuminate\Support\Js::from($idleClasses)->toHtml() ?>"
        class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-200 text-sm font-semibold disabled:opacity-60 disabled:cursor-not-allowed">
    <span class="text-base"><?php echo e($page['icon']); ?></span>
    <span class="flex-1 text-left"><?php echo e($page['label']); ?></span>
    <span x-show="pendingCommand === <?php echo \Illuminate\Support\Js::from($command)->toHtml() ?>" x-cloak class="w-4 h-4 rounded-full border-2 border-current border-t-transparent animate-spin" aria-hidden="true"></span>
</button>
<?php /**PATH C:\mohamed Saber\my-laravel-app\dist\local-test\bedo-factory\resources\views/components/nav-command.blade.php ENDPATH**/ ?>