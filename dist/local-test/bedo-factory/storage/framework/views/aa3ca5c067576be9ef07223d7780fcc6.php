<!-- DARK INDUSTRIAL FOOTER — live from factory/counts, factory/encoder, factory/message -->
<footer class="bg-[#0f172a] border-t border-slate-800 text-white px-6 py-3.5 shrink-0 shadow-lg">
    <div class="grid grid-cols-2 sm:grid-cols-7 gap-3 font-mono text-center mb-2.5">
        <?php $__currentLoopData = $counters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $counter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-slate-900/90 p-2.5 rounded-xl border border-slate-800">
                <span class="text-gray-400 block text-[10px] uppercase tracking-wider"><?php echo e($counter['label']); ?></span>
                <span class="text-base font-extrabold <?php echo e($counter['color']); ?>" x-text="counts?.[<?php echo \Illuminate\Support\Js::from($key)->toHtml() ?>] ?? '—'">—</span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="bg-slate-900/90 p-2.5 rounded-xl border border-slate-800 flex flex-col justify-center">
            <span class="text-gray-400 block text-[10px] uppercase tracking-wider">Encoder Status</span>
            <span class="text-xs text-amber-400 font-bold" x-text="encoderText">—</span>
        </div>
    </div>

    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between gap-4 text-xs text-gray-400 font-sans px-1">
        <span class="font-bold text-gray-200 truncate" x-text="message || 'IoT Factory System'" :title="message">IoT Factory System</span>
        <span class="font-bold text-gray-200 shrink-0">Copyright © <?php echo e(date('Y')); ?> BEDO Innovating Education</span>
    </div>
</footer>
<?php /**PATH C:\mohamed Saber\my-laravel-app\dist\local-test\bedo-factory\resources\views/partials/footer.blade.php ENDPATH**/ ?>