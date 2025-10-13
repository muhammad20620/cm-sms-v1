<?php 

use App\Models\User;

?>

<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('admin.fee_manager.update', ['id' => $invoice_details->id])); ?>">
	<?php echo csrf_field(); ?> 
	<div class="form-row">
		<div class="fpb-7">
            <label for="class_id_on_create"  class="eForm-label"><?php echo e(get_phrase('Class')); ?></label>
            <select name="class_id" id="class_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove"  required onchange="classWiseStudentOnCreate(this.value)">
                <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($class['id']); ?>" <?php if ($class['id'] == $invoice_details['class_id']): ?> selected <?php endif; ?>><?php echo e($class['name']); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="fpb-7">
            <label for="student_id_on_create" class="eForm-label"><?php echo e(get_phrase('Select student')); ?></label>
            <div id = "student_content">
                <select name="student_id" id="student_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove" required >
                    <option value=""><?php echo e(get_phrase('Select a student')); ?></option>
                    <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    	<?php $student = User::find($enrollment->user_id); ?>
                        <option value="<?php echo e($enrollment['user_id']); ?>" <?php if ($invoice_details['student_id'] == $enrollment['user_id']): ?>selected<?php endif; ?>><?php echo e($student['name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="fpb-7">
            <label for="title"  class="eForm-label"><?php echo e(get_phrase('Invoice title')); ?></label>
            <input type="text" class="form-control eForm-control" id="title" name = "title" value="<?php echo e($invoice_details['title']); ?>" required>
        </div>

        <div class="fpb-7">
            <label for="total_amount"  class="eForm-label"><?php echo e(get_phrase('Total amount').'('.school_currency().')'); ?></label>
            <input type="text" class="form-control eForm-control" id="amount" name = "amount" value="<?php echo e($invoice_details['amount']); ?>" required>
        </div>
                            
        <div class="fpb-7">
			<label for="discounted_price" class="eForm-label"><?php echo e(get_phrase('Discount amount').'('.school_currency().')'); ?></label>
			<input type="number" class="form-control eForm-control" id="discounted_price" name = "discounted_price" onkeyup="calculateDiscountPercentage(this.value)" min="0"  value="<?php echo e($invoice_details['discounted_price']); ?>">

            <small class="text-muted discount_price_text"><?php echo get_phrase('This student has'); ?> <span id = "discounted_percentage" class="text-danger">0%</span> <?php echo get_phrase('discount'); ?></small>
		</div>

        <div class="fpb-7">
            <label for="paid_amount" class="eForm-label"><?php echo e(get_phrase('Paid amount').'('.school_currency().')'); ?></label>
            <input type="text" class="form-control eForm-control" id="paid_amount" name = "paid_amount" value="<?php echo e($invoice_details['paid_amount']); ?>" required>
        </div>

        <div class="fpb-7">
            <label for="status" class="eForm-label"><?php echo e(get_phrase('Status')); ?></label>
            <select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value=""><?php echo e(get_phrase('Select a status')); ?></option>
                <option value="paid" <?php if ($invoice_details['status'] == 'paid'): ?> selected <?php endif; ?>><?php echo e(get_phrase('Paid')); ?></option>
                <option value="unpaid" <?php if ($invoice_details['status'] == 'unpaid'): ?> selected <?php endif; ?>><?php echo e(get_phrase('Unpaid')); ?></option>
            </select>
        </div>

        <div class="fpb-7">
			<label for="payment_method" class="eForm-label"><?php echo e(get_phrase('Payment method')); ?></label>
			<select name="payment_method" id="payment_method" class="form-select eForm-select eChoice-multiple-with-remove">
				<option value=""><?php echo e(get_phrase('Select a payment method')); ?></option>
				<option value="cash" <?php if ($invoice_details['payment_method'] == 'cash'): ?> selected <?php endif; ?>><?php echo e(get_phrase('Cash')); ?></option>
				<option value="paypal" <?php if ($invoice_details['payment_method'] == 'paypal'): ?> selected <?php endif; ?>><?php echo e(get_phrase('Paypal')); ?></option>
				<option value="paytm" <?php if ($invoice_details['payment_method'] == 'paytm'): ?> selected <?php endif; ?>><?php echo e(get_phrase('Paytm')); ?></option>
				<option value="razorpay" <?php if ($invoice_details['payment_method'] == 'razorpay'): ?> selected <?php endif; ?>><?php echo e(get_phrase('Razorpay')); ?></option> 
				<option value="stripe" <?php if ($invoice_details['payment_method'] == 'stripe'): ?> selected <?php endif; ?>><?php echo e(get_phrase('Stripe')); ?></option> 
				<option value="flutterwave" <?php if ($invoice_details['payment_method'] == 'flutterwave'): ?> selected <?php endif; ?>><?php echo e(get_phrase('Flutterwave')); ?></option> 
			</select>
		</div>
		
	</div>

	<div class="form-group  col-md-12">
        <button class="btn-form" type="submit"><?php echo e(get_phrase('Update invoice')); ?></button>
    </div>

</form>

<script type="text/javascript">

  "use strict";

  jQuery(document).ready(function() {
		calculateDiscountPercentage($('#discounted_price').val());
	});
	

	function calculateDiscountPercentage(discounted_price) {
    if (discounted_price > 0) {
        var actualPrice = jQuery('#amount').val();
        if (actualPrice > 0) {
            var discountedPercentage = (discounted_price / actualPrice) * 100;
            if (discountedPercentage > 0) {
                jQuery('#discounted_percentage').text(discountedPercentage.toFixed(2) + '%');
            } else {
                jQuery('#discounted_percentage').text('0%');
            }
        }
    }
}
	
	function classWiseStudentOnCreate(classId) {
		let url = "<?php echo e(route('admin.class_wise_student', ['id' => ":classId"])); ?>";
    	url = url.replace(":classId", classId);
		$.ajax({
			url: url,
			success: function(response){
		  		$('#student_id_on_create').html(response);
			}
		});
	}

    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });

</script><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/student_fee_manager/edit.blade.php ENDPATH**/ ?>