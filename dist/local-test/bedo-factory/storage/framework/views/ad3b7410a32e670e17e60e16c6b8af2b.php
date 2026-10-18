<!-- SIDEBAR NAVIGATION -->
<aside class="w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col shrink-0 shadow-sm transition-colors">
    <nav class="flex-1 p-4 space-y-2">
        <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($page['command'])): ?>
                <?php if (isset($component)) { $__componentOriginaldc9b879cd8508d10a6f3e56c5a51ba1f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc9b879cd8508d10a6f3e56c5a51ba1f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-command','data' => ['page' => $page]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-command'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($page)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc9b879cd8508d10a6f3e56c5a51ba1f)): ?>
<?php $attributes = $__attributesOriginaldc9b879cd8508d10a6f3e56c5a51ba1f; ?>
<?php unset($__attributesOriginaldc9b879cd8508d10a6f3e56c5a51ba1f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc9b879cd8508d10a6f3e56c5a51ba1f)): ?>
<?php $component = $__componentOriginaldc9b879cd8508d10a6f3e56c5a51ba1f; ?>
<?php unset($__componentOriginaldc9b879cd8508d10a6f3e56c5a51ba1f); ?>
<?php endif; ?>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginalc295f12dca9d42f28a259237a5724830 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc295f12dca9d42f28a259237a5724830 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-link','data' => ['page' => $page,'active' => request()->routeIs($page['route'])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($page),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs($page['route']))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $attributes = $__attributesOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__attributesOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $component = $__componentOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__componentOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    <!-- VERSION -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-800 flex justify-center">
        <span class="text-xs bg-amber-950 text-amber-100 dark:bg-amber-500/15 dark:text-amber-300 px-2.5 py-1 rounded-md font-mono font-bold tracking-wide">VERSION <?php echo e($version); ?></span>
    </div>
</aside>
<?php /**PATH C:\mohamed Saber\my-laravel-app\dist\local-test\bedo-factory\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>