<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Royal Canin'); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon.ico')); ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <!-- Custom Fonts (Assuming you have D-DIN-PRO) -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('fonts/font.css')); ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <!-- TOP BANNER -->
    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Header -->
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <main class="main pb-3">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH /Users/sahilrihadh/Development/royalcanin/webinar-solution/resources/views/layouts/master.blade.php ENDPATH**/ ?>