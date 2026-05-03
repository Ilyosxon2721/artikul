<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => null, 'metaDescription' => null]));

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

foreach (array_filter((['title' => null, 'metaDescription' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e($title ? $title.' — Artikul' : 'Artikul — '.__('app.tagline')); ?></title>
    <meta name="description" content="<?php echo e($metaDescription ?? __('app.tagline')); ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($title ?? 'Artikul'); ?>">
    <meta property="og:description" content="<?php echo e($metaDescription ?? __('app.tagline')); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:site_name" content="Artikul">

    <link rel="canonical" href="<?php echo e(url()->current()); ?>">

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
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
<?php /**PATH /home/user/artikul/resources/views/components/layouts/public.blade.php ENDPATH**/ ?>