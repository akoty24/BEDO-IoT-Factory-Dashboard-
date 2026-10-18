<?php $__env->startSection('title', 'Manual Mode'); ?>

<?php $__env->startSection('content'); ?>
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
        <!-- Steps & Outputs Column -->
        <div class="xl:col-span-5 space-y-5">
            <div class="bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wider mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Sequences</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (isset($component)) { $__componentOriginal8cb502d3f923af42b4e6bb1017e285af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cb502d3f923af42b4e6bb1017e285af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.command-button','data' => ['step' => $step,'class' => 'shadow-2xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('command-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['step' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($step),'class' => 'shadow-2xs']); ?><?php echo e($label); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cb502d3f923af42b4e6bb1017e285af)): ?>
<?php $attributes = $__attributesOriginal8cb502d3f923af42b4e6bb1017e285af; ?>
<?php unset($__attributesOriginal8cb502d3f923af42b4e6bb1017e285af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cb502d3f923af42b4e6bb1017e285af)): ?>
<?php $component = $__componentOriginal8cb502d3f923af42b4e6bb1017e285af; ?>
<?php unset($__componentOriginal8cb502d3f923af42b4e6bb1017e285af); ?>
<?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wider">Actuators</h3>
                    <!-- factory/cmd/pwm on|off, state from factory/state.pwm -->
                   
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    <?php $__currentLoopData = $outputs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (isset($component)) { $__componentOriginal8cb502d3f923af42b4e6bb1017e285af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cb502d3f923af42b4e6bb1017e285af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.command-button','data' => ['output' => $name,'highlighted' => $name === 'light_red']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('command-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['output' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'highlighted' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name === 'light_red')]); ?><?php echo e($label); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cb502d3f923af42b4e6bb1017e285af)): ?>
<?php $attributes = $__attributesOriginal8cb502d3f923af42b4e6bb1017e285af; ?>
<?php unset($__attributesOriginal8cb502d3f923af42b4e6bb1017e285af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cb502d3f923af42b4e6bb1017e285af)): ?>
<?php $component = $__componentOriginal8cb502d3f923af42b4e6bb1017e285af; ?>
<?php unset($__componentOriginal8cb502d3f923af42b4e6bb1017e285af); ?>
<?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <?php if (isset($component)) { $__componentOriginalfe870a5cbe00ad648c3ba304916c61b1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe870a5cbe00ad648c3ba304916c61b1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sensors-panel','data' => ['sensors' => $sensors,'class' => 'xl:col-span-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sensors-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['sensors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sensors),'class' => 'xl:col-span-7']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe870a5cbe00ad648c3ba304916c61b1)): ?>
<?php $attributes = $__attributesOriginalfe870a5cbe00ad648c3ba304916c61b1; ?>
<?php unset($__attributesOriginalfe870a5cbe00ad648c3ba304916c61b1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe870a5cbe00ad648c3ba304916c61b1)): ?>
<?php $component = $__componentOriginalfe870a5cbe00ad648c3ba304916c61b1; ?>
<?php unset($__componentOriginalfe870a5cbe00ad648c3ba304916c61b1); ?>
<?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.factory', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\mohamed Saber\my-laravel-app\dist\local-test\bedo-factory\resources\views\factory\manual.blade.php ENDPATH**/ ?>