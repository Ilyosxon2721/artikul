<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e($title ?? __('app.tagline')); ?> — Artikul</title>
    <meta name="description" content="<?php echo e($meta_description ?? __('app.tagline')); ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($title ?? 'Artikul'); ?>">
    <meta property="og:description" content="<?php echo e($meta_description ?? __('app.tagline')); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:site_name" content="Artikul">

    <link rel="canonical" href="<?php echo e(url()->current()); ?>">

    <script type="application/ld+json">
    
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Artikul",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/tasks') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    
    </script>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased bg-white text-gray-900">
    <header class="border-b border-gray-100 bg-white sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="<?php echo e(route('home')); ?>" class="text-xl font-bold text-blue-700 tracking-tight">Artikul</a>

            <nav class="hidden md:flex items-center gap-6 text-sm text-gray-600">
                <a href="<?php echo e(route('tasks.index')); ?>" class="hover:text-gray-900"><?php echo e(__('app.nav.tasks')); ?></a>
                <a href="<?php echo e(route('sellers.index')); ?>" class="hover:text-gray-900"><?php echo e(__('app.nav.sellers')); ?></a>
                <a href="<?php echo e(url('/how-it-works')); ?>" class="hover:text-gray-900"><?php echo e(__('app.nav.how_it_works')); ?></a>
            </nav>

            <div class="flex items-center gap-3 text-sm">
                <div class="hidden md:flex items-center gap-1 text-xs">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = config('app.available_locales', ['ru', 'uz', 'en']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('locale.switch', $code)); ?>" class="px-1.5 py-0.5 rounded uppercase <?php echo e(app()->getLocale() === $code ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100'); ?>"><?php echo e($code); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="text-gray-700 hover:text-gray-900"><?php echo e(__('app.nav.dashboard')); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('login')); ?>" class="text-gray-700 hover:text-gray-900"><?php echo e(__('app.nav.login')); ?></a>
                    <a href="<?php echo e(route('register')); ?>" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg"><?php echo e(__('app.nav.register')); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </header>

    <?php echo e($slot); ?>


    <footer class="border-t border-gray-100 bg-gray-50 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 md:grid-cols-4 gap-8 text-sm">
            <div>
                <div class="text-lg font-bold text-blue-700">Artikul</div>
                <p class="text-gray-500 mt-2"><?php echo e(__('app.tagline')); ?></p>
                <p class="text-xs text-gray-400 mt-3">© <?php echo e(date('Y')); ?> Artikul</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2"><?php echo e(__('app.footer.platform')); ?></h3>
                <ul class="space-y-1 text-gray-500">
                    <li><a href="<?php echo e(route('tasks.index')); ?>" class="hover:text-gray-800"><?php echo e(__('app.nav.tasks')); ?></a></li>
                    <li><a href="<?php echo e(route('sellers.index')); ?>" class="hover:text-gray-800"><?php echo e(__('app.nav.sellers')); ?></a></li>
                    <li><a href="<?php echo e(url('/how-it-works')); ?>" class="hover:text-gray-800"><?php echo e(__('app.nav.how_it_works')); ?></a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2"><?php echo e(__('app.footer.company')); ?></h3>
                <ul class="space-y-1 text-gray-500">
                    <li><a href="<?php echo e(url('/about')); ?>" class="hover:text-gray-800"><?php echo e(__('app.footer.about')); ?></a></li>
                    <li><a href="<?php echo e(url('/terms')); ?>" class="hover:text-gray-800"><?php echo e(__('app.footer.terms')); ?></a></li>
                    <li><a href="<?php echo e(url('/privacy')); ?>" class="hover:text-gray-800"><?php echo e(__('app.footer.privacy')); ?></a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2"><?php echo e(__('app.footer.contacts')); ?></h3>
                <ul class="space-y-1 text-gray-500">
                    <li><a href="mailto:hello@artikul.uz" class="hover:text-gray-800">hello@artikul.uz</a></li>
                    <li><a href="https://t.me/artikul_uz" class="hover:text-gray-800">Telegram</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
<?php /**PATH /home/user/artikul/resources/views/layouts/public.blade.php ENDPATH**/ ?>