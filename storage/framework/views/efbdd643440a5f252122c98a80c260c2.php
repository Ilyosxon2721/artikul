<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->avatar_url): ?>
                <img src="<?php echo e($user->avatar_url); ?>" alt="" class="w-20 h-20 rounded-full object-cover border" />
            <?php else: ?>
                <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-2xl font-semibold">
                    <?php echo e(mb_strtoupper(mb_substr($user->name, 0, 1))); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div>
                <h1 class="text-2xl font-semibold text-gray-800"><?php echo e($user->buyerProfile->company_name ?: $user->name); ?></h1>
                <p class="text-sm text-gray-500"><?php echo e($user->city); ?><?php echo e($user->country_code ? ', '.$user->country_code : ''); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->buyerProfile->rating_avg > 0): ?>
                    <p class="text-sm text-amber-600 mt-1">★ <?php echo e(number_format($user->buyerProfile->rating_avg, 1)); ?> (<?php echo e($user->buyerProfile->reviews_count); ?>)</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->buyerProfile->description): ?>
            <p class="mt-6 text-gray-700 whitespace-pre-line"><?php echo e($user->buyerProfile->description); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($user->buyerProfile->marketplace_codes)): ?>
            <div class="mt-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-2"><?php echo e(__('app.profile.fields.marketplaces')); ?></h2>
                <div class="flex flex-wrap gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->buyerProfile->marketplace_codes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs uppercase"><?php echo e($code); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-lg font-semibold text-gray-800"><?php echo e($user->buyerProfile->total_tasks_published); ?></div>
                <div class="text-xs text-gray-500"><?php echo e(__('app.profile.stats.tasks_published')); ?></div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-lg font-semibold text-gray-800"><?php echo e($user->buyerProfile->total_contracts_completed); ?></div>
                <div class="text-xs text-gray-500"><?php echo e(__('app.profile.stats.contracts_completed')); ?></div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-lg font-semibold text-gray-800"><?php echo e(number_format($user->buyerProfile->on_time_payment_rate, 0)); ?>%</div>
                <div class="text-xs text-gray-500"><?php echo e(__('app.profile.stats.on_time_payment')); ?></div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-lg font-semibold text-gray-800"><?php echo e($user->buyerProfile->skus_count ?: '—'); ?></div>
                <div class="text-xs text-gray-500"><?php echo e(__('app.profile.stats.skus')); ?></div>
            </div>
        </div>
    </div>
</div><?php /**PATH /home/user/artikul/resources/views/livewire/public/public-buyer-profile.blade.php ENDPATH**/ ?>