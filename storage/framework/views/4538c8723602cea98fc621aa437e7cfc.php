<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => __('app.how.title')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('app.how.title'))]); ?>
    <article class="max-w-3xl mx-auto px-4 py-16">
        <h1 class="text-3xl font-semibold text-gray-900"><?php echo e(__('app.how.title')); ?></h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10">
            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4"><?php echo e(__('app.landing.for_buyers')); ?></h2>
                <ol class="space-y-3 text-sm text-gray-700 list-decimal list-inside">
                    <li><?php echo e(__('app.landing.buyer_step1')); ?></li>
                    <li><?php echo e(__('app.landing.buyer_step2')); ?></li>
                    <li><?php echo e(__('app.landing.buyer_step3')); ?></li>
                </ol>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4"><?php echo e(__('app.landing.for_sellers')); ?></h2>
                <ol class="space-y-3 text-sm text-gray-700 list-decimal list-inside">
                    <li><?php echo e(__('app.landing.seller_step1')); ?></li>
                    <li><?php echo e(__('app.landing.seller_step2')); ?></li>
                    <li><?php echo e(__('app.landing.seller_step3')); ?></li>
                </ol>
            </div>
        </div>
    </article>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd)): ?>
<?php $attributes = $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd; ?>
<?php unset($__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd)): ?>
<?php $component = $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd; ?>
<?php unset($__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd); ?>
<?php endif; ?>
<?php /**PATH /home/user/artikul/resources/views/landing/how-it-works.blade.php ENDPATH**/ ?>