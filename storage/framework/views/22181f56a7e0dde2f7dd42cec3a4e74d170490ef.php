<?php
use App\Models\GlobalSettings;
use App\Models\Subscription;

$active_payment_methods=GlobalSettings::where('key','paypal')
                ->orWhere('key','stripe')
                ->orWhere('key','razorpay')
                ->orWhere('key','paytm')
                // ->orWhere('key','paystack')
                ->orWhere('key','offline')
                ->get();


$subscription = Subscription::latest()->first();

    if (!empty($subscription)) {
        $subascribe_active = $subscription->active;

        $packagesPrice = $selected_package['price'] ;

        $subscriptionsPrice = $subscription->paid_amount;

        $upgradePrice = $packagesPrice - $subscriptionsPrice;
    }   
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo e(get_phrase('Payment').' | '.get_settings('system_title')); ?></title>
    <!-- all the meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- all the css files -->
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/logo.png')); ?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/vendors/bootstrap-5.1.3/css/bootstrap.min.css')); ?>">

    <!--Custom css-->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/swiper-bundle.min.css')); ?>">

    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/custom.css')); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/style.css')); ?>" />
    <!-- Datepicker css -->

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/daterangepicker.css')); ?>" />
    <!-- Select2 css -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/select2.min.css')); ?>" />

    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/vendors/bootstrap-icons-1.8.1/bootstrap-icons.css')); ?>">

<body>
    <?php  $off="";?>
    <div class="main_content paymentContent">
        <div class="paymentHeader d-flex justify-content-between align-items-center">
            <h5 class="title">
                <?php echo e(get_phrase('Make Payment')); ?>

            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="location.href='<?php echo e(redirect()->back()->getTargetUrl()); ?>'"></button>
        </div>

        <div class="paymentWrap d-flex align-items-start flex-wrap">
            <div class="paymentLeft">

                <p class="payment_tab_title pb-30">
                    <?php echo e(get_phrase('Payment Gateway')); ?>

                </p>
                <!-- Tab -->
                <div class="nav flex-md-column flex-row nav-pills payment_modalTab" id="v-pills-tab" role="tablist" aria-orientation="vertical">




                    <?php $__currentLoopData = $active_payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $method=json_decode($value['value'],true);?>
                    <?php if($method['status']==1 && addon_status('payment_gateways')==1): ?>

                    <div class="tabItem " id="v-pills-<?php echo e($value['key']); ?>-tab" data-bs-toggle="pill" data-bs-target="#v-pills-<?php echo e($value['key']); ?>" role="tab" aria-controls="v-pills-<?php echo e($value['key']); ?>" aria-selected="true">
                        <div class="payment_gateway_option d-flex align-items-center">
                            <div class="logo">
                                <?php
                                $image_logo="assets/images/".$value['key'].".png";
                                ?>
                                <img src="<?php echo e(asset($image_logo)); ?>" alt="" />
                            </div>
                            <div class="info">
                                <p class="card_no">
                                    <?php echo e($value['key']); ?>

                                    <?php if($value['key'] != 'offline'): ?>
                                        <!-- <span class="badge bg-success m-1" style=""><?php echo e(get_phrase('Addon')); ?></span> -->
                                    <?php endif; ?>
                                </p>

                            </div>
                        </div>
                    </div>

                    <?php endif; ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if(addon_status('payment_gateways')!=1): ?>
                    <?php $off=' show active'; ?>

                    <div
                    class="tabItem "
                    id="v-pills-offline-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#v-pills-offline"
                    role="tab"
                    aria-controls="v-pills-offline"
                    aria-selected="true"
                >
                    <div class="payment_gateway_option d-flex align-items-center">
                    <div class="logo">
                        <?php
                        $image_logo="assets/images/offline.png";
                        ?>
                        <img src="<?php echo e(asset($image_logo)); ?>"  alt="" />
                    </div>
                    <div class="info">
                        <p class="card_no"><?php echo e(get_phrase('Offline')); ?><span class="badge bg-success m-1" style=""><?php echo e(get_phrase('Addon')); ?></span></p>

                    </div>
                    </div>
                </div>



                    <?php endif; ?>
                    

                </div>
            </div>



            <div class="paymentRight">
                <p class="payment_tab_title pb-30">
                    <?php echo e(get_phrase('Invoice Summary')); ?>

                </p>
                <div class="payment_table">
                    <div class="table-responsive">
                        <table class="table eTable eTable-2">
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="dAdmin_info_name">
                                            <p><span><?php echo e('01'); ?></span></p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-100px">
                                            <p>
                                                <?php echo e($selected_package['name']); ?>

                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-150px text-end">
                                             
                                                <?php if(!empty($subscription)): ?> 
                                                <?php if( $subascribe_active == '1'): ?>
                                                <p>   <?php echo e($upgradePrice); ?></p> 
                                               
                                                <?php endif; ?>
                                                <?php else: ?>
                                                  <p><?php echo e($selected_package['price']." ".get_active_currency()); ?></p>  
                                                <?php endif; ?>
                                            
                                            
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-150px text-end">
                                            <p><span>
                                                    <?php echo e(get_phrase('Grand Total')); ?> :
                                                    <?php if(!empty($subscription)): ?> 
                                                    <?php if($subascribe_active == '1'): ?>
                                                    <?php echo e($upgradePrice); ?>

                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php echo e($selected_package['price']." ".get_active_currency()); ?>

                                                <?php endif; ?>
                                                </span></p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Content -->
                <div class="tab-content payment_modalTab_content" id="v-pills-tabContent">


                    <?php if(addon_status('payment_gateways')==1): ?>
                        <?php echo $__env->make('admin.subscription.paypal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->make('admin.subscription.stripe', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->make('admin.subscription.razorpay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->make('admin.subscription.paytm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        
                    <?php endif; ?>












                    <div class="tab-pane fade <?= $off; ?>" id="v-pills-offline" role="tabpanel" aria-labelledby="v-pills-offline-tab" tabindex="0">
                        <div class="off_payment_form">


                            <form action="<?php echo e(route('admin.admin_subscription_offline_payment', ['id' => $selected_package['id']])); ?>" class="offline-form form" method="post" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                
                                <hr class="border mb-4">

                                <input type="hidden" id="amount" class="form-control eForm-control" name="amount" value="<?php echo e($selected_package['price']); ?>" readonly>


                                <div class="payable_document">
                                    <label for="payableDocuemnt" class="eForm-label">
                                        <?php echo e(get_phrase('Document of your payment')); ?> (jpg, pdf, txt, png, docx)
                                    </label>
                                    <input type="file" class="form-control eForm-control-file" id="document_image" name="document_image" required>
                                </div>

                        </div>



                        <button type="submit" class="off_payment_btn">
                            <?php echo e(get_phrase('Submit payment document')); ?>

                        </button>


                        <div class="offline_payment_instruction alert alert-success mt-3">

                            <div class="instruction_img">
                                <p class="payment_tab_title mb-3 text-center">
                                    <?php echo e(get_phrase('Instruction')); ?>

                                </p>
                                <p class="mb-3"><?php echo e(get_settings('off_pay_ins_text')); ?></p>

                                <?php if(!empty(get_settings('off_pay_ins_file'))): ?>
                                    <?php
                                        $fileExtension = pathinfo(get_settings('off_pay_ins_file'), PATHINFO_EXTENSION);
                                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                    ?>

                                    <?php if(in_array(strtolower($fileExtension), $allowedExtensions)): ?>
                                        
                                        <span class="badge bg-success m-1">
                                            <a href="<?php echo e(asset('assets/uploads/offline_payment/'.get_settings('off_pay_ins_file'))); ?>" download><img src="<?php echo e(asset('assets/uploads/offline_payment/'.get_settings('off_pay_ins_file'))); ?>" width="539px" ></a>
                                        </span>
                                        
                                    <?php else: ?>
                                    <span class="badge bg-success m-1">
                                        <a style="color:#fff; font-size: 14px;" href="<?php echo e(asset('assets/uploads/offline_payment/'.get_settings('off_pay_ins_file'))); ?>" download>Download the instruction <i class="bi bi-file-earmark-richtext-fill"></i></a>
                                    </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                            </div>
                            
                            <p class="mt-3">
                                 <?php echo e(get_phrase('Admin will review your payment document and then approve the Payment.')); ?>

                            </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script src="<?php echo e(asset('assets/vendors/jquery/jquery-3.6.0.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/vendors/bootstrap-5.1.3/js/bootstrap.bundle.min.js')); ?>"></script>

        <script src="<?php echo e(asset('assets/js/swiper-bundle.min.js')); ?>"></script>

        <script type="text/javascript"></script>
            </body>

</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/subscription/payment_gateway.blade.php ENDPATH**/ ?>