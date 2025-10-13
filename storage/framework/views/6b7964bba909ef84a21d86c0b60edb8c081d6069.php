<!DOCTYPE html>
<html lang="en">

<head>

  <title><?php echo e(get_phrase('Login').' | '.get_settings('system_title')); ?></title>
  <!-- all the meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta content="" name="description" />
  <meta content="" name="author" />
  <!-- all the css files -->
  <link rel="shortcut icon" href="<?php echo e(asset('assets/uploads/logo/'.get_settings('favicon'))); ?>" />
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/vendors/bootstrap-5.1.3/css/bootstrap.min.css')); ?>">

  <!--Custom css-->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/main.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/style.css')); ?>">

  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/vendors/bootstrap-icons-1.8.1/bootstrap-icons.css')); ?>">

  <!--Toaster css-->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/toastr.min.css')); ?>"/>

</head>

<body>

    <div class="container-fluid h-100">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <?php echo $__env->make('external_plugin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!--Main Jquery-->
    <script src="<?php echo e(asset('assets/vendors/jquery/jquery-3.6.0.min.js')); ?>"></script>
    <!--Bootstrap bundle with popper-->
    <script src="<?php echo e(asset('assets/vendors/bootstrap-5.1.3/js/bootstrap.bundle.min.js')); ?>"></script>

    <!--Custom Script-->
    <script src="<?php echo e(asset('assets/js/script.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>

    <!--Toaster Script-->
    <script src="<?php echo e(asset('assets/js/toastr.min.js')); ?>"></script>

    <script>
        
        "use strict";

        <?php if(Session::has('message')): ?>
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
                toastr.success("<?php echo e(session('message')); ?>");
        <?php endif; ?>

        <?php if(Session::has('error')): ?>
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
                toastr.error("<?php echo e(session('error')); ?>");
        <?php endif; ?>

        <?php if(Session::has('info')): ?>
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
                toastr.info("<?php echo e(session('info')); ?>");
        <?php endif; ?>

        <?php if(Session::has('warning')): ?>
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
                toastr.warning("<?php echo e(session('warning')); ?>");
        <?php endif; ?>
    </script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/cm-sms-v1/resources/views/layouts/signin_page.blade.php ENDPATH**/ ?>