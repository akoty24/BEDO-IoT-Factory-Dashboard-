<?php $__env->startSection('title', 'Automatic Mode'); ?>

<?php $__env->startSection('content'); ?>
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
        <div class="xl:col-span-5">
            <div class="bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-6 shadow-sm h-full flex flex-col gap-4">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Automatic Sequence Control</h3>

                <!-- factory/cmd/auto start | feed,  factory/cmd/stop -->
                <div class="grid grid-cols-3 gap-2.5">
                    <button type="button" @click="autoCommand('start')" :disabled="!canControl"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl text-sm shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                        ▶ Start
                    </button>
                    <button type="button" @click="autoCommand('feed')" :disabled="!canControl"
                            class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-xl text-sm shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                        ⬇ Feed Part
                    </button>
                    <button type="button" @click="sendMachineCommand('stop')"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl text-sm shadow-sm transition">
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

<?php echo $__env->make('layouts.factory', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\mohamed Saber\my-laravel-app\dist\local-test\bedo-factory\resources\views/factory/automatic.blade.php ENDPATH**/ ?>