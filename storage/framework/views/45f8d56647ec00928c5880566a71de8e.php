<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->category): ?>
                        <span><?php echo e($task->category->name()); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->subcategory): ?>
                        <span>·</span>
                        <span><?php echo e($task->subcategory->name()); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span>·</span>
                    <span><?php echo e($task->published_at?->diffForHumans()); ?></span>
                </div>

                <h1 class="text-2xl font-semibold text-gray-800"><?php echo e($task->title); ?></h1>

                <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded"><?php echo e($task->type?->label()); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $marketplaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded uppercase"><?php echo e($mp->code); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->description): ?>
                    <div class="prose prose-sm max-w-none mt-6 text-gray-700 whitespace-pre-line"><?php echo e($task->description); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->attachments->count() > 0): ?>
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <h2 class="text-sm font-semibold text-gray-700 mb-2"><?php echo e(__('app.tasks.attachments')); ?></h2>
                        <ul class="space-y-1 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $task->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a href="/storage/<?php echo e($att->path); ?>" target="_blank" class="text-blue-600 hover:underline"><?php echo e($att->filename); ?></a>
                                    <span class="text-gray-400 text-xs">(<?php echo e(number_format($att->size_bytes / 1024, 0)); ?> KB)</span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <aside class="space-y-4">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->budget_type?->value === 'negotiable'): ?>
                    <div class="text-lg font-semibold text-gray-800"><?php echo e(__('app.tasks.budget_negotiable')); ?></div>
                <?php else: ?>
                    <div>
                        <div class="text-2xl font-semibold text-gray-800">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->budget_min): ?>
                                <?php echo e(number_format((float) $task->budget_min, 0, '.', ' ')); ?><?php echo e($task->budget_max ? ' – '.number_format((float) $task->budget_max, 0, '.', ' ') : ''); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="text-sm text-gray-500"><?php echo e($task->currency?->value); ?></span>
                        </div>
                        <div class="text-xs text-gray-500"><?php echo e($task->budget_type?->label()); ?></div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->deadline_at): ?>
                    <div class="text-sm text-gray-700"><?php echo e(__('app.tasks.deadline')); ?>: <strong><?php echo e($task->deadline_at->format('d.m.Y')); ?></strong></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->required_seller_level): ?>
                    <div class="text-sm text-gray-700"><?php echo e(__('app.tasks.fields.required_seller_level')); ?>: <strong><?php echo e($task->required_seller_level->label()); ?>+</strong></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->require_verified_seller): ?>
                    <div class="text-sm text-blue-700">✓ <?php echo e(__('app.tasks.verified_only')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="border-t border-gray-100 pt-3 grid grid-cols-2 text-center text-xs">
                    <div>
                        <div class="text-base font-semibold text-gray-800"><?php echo e($task->proposals_count); ?></div>
                        <div class="text-gray-500"><?php echo e(__('app.tasks.stats.proposals')); ?></div>
                    </div>
                    <div>
                        <div class="text-base font-semibold text-gray-800"><?php echo e($task->views_count); ?></div>
                        <div class="text-gray-500"><?php echo e(__('app.tasks.stats.views')); ?></div>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRespond): ?>
                    <button type="button" disabled class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg opacity-70 cursor-not-allowed" title="<?php echo e(__('app.tasks.respond_coming_soon')); ?>">
                        <?php echo e(__('app.tasks.actions.respond')); ?>

                    </button>
                <?php elseif($canEdit): ?>
                    <a href="<?php echo e(route('tasks.edit', $task->slug)); ?>" wire:navigate class="block text-center w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        <?php echo e(__('app.actions.edit')); ?>

                    </a>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="block text-center w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                            <?php echo e(__('app.tasks.login_to_respond')); ?>

                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-3"><?php echo e(__('app.tasks.about_buyer')); ?></h2>
                <div class="flex items-center gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->buyer->avatar_url): ?>
                        <img src="<?php echo e($task->buyer->avatar_url); ?>" alt="" class="w-10 h-10 rounded-full object-cover" />
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                            <?php echo e(mb_strtoupper(mb_substr($task->buyer->name, 0, 1))); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->buyer->username): ?>
                            <a href="<?php echo e(route('public.buyer', $task->buyer->username)); ?>" wire:navigate class="text-sm font-medium text-gray-800 hover:underline"><?php echo e($task->buyer->name); ?></a>
                        <?php else: ?>
                            <span class="text-sm font-medium text-gray-800"><?php echo e($task->buyer->name); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="text-xs text-gray-500">
                            <?php echo e($task->buyer->city); ?><?php echo e($task->buyer->country_code ? ', '.$task->buyer->country_code : ''); ?>

                        </div>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->buyer->buyerProfile?->total_tasks_published): ?>
                    <div class="text-xs text-gray-500 mt-3">
                        <?php echo e(trans_choice('app.tasks.buyer_history', $task->buyer->buyerProfile->total_tasks_published, ['count' => $task->buyer->buyerProfile->total_tasks_published])); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </aside>
    </div>
</div><?php /**PATH /home/user/artikul/resources/views/livewire/tasks/task-show.blade.php ENDPATH**/ ?>