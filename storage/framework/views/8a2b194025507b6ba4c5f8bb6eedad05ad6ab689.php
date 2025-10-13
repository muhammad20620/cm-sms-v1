<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('admin.create.fee_manager', ['value' => 'mass'])); ?>">
	<?php echo csrf_field(); ?> 
	<div class="form-row">
		<div class="fpb-7">
			<label for="class_id_on_create" class="eForm-label"><?php echo e(get_phrase('Class')); ?></label>
			<select name="class_id" id="class_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove"  required onchange="classWiseSectionOnCreate(this.value)">
				<option value=""><?php echo e(get_phrase('Select a class')); ?></option>
				<?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			  		<option value="<?php echo e($class['id']); ?>"><?php echo e($class['name']); ?></option>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</select>
		</div>

		<div class="fpb-7">
            <label for="section_id_on_create" class="eForm-label"><?php echo e(get_phrase('Section')); ?></label>
            <select name="section_id" id = "section_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value=""><?php echo e(get_phrase('Select section')); ?></option>
            </select>
        </div>

		<div class="fpb-7">
			<label for="title" class="eForm-label"><?php echo e(get_phrase('Invoice title')); ?></label>
			<input type="text" class="form-control eForm-control" id="title" name="title" required>
		</div>

		<div class="fpb-7">
			<label for="total_amount" class="eForm-label"><?php echo e(get_phrase('Total amount').'('.school_currency().')'); ?></label>
			<input type="number" class="form-control eForm-control" id="total_amount" name="total_amount" required>
		</div>

		<div class="fpb-7">
			<label for="paid_amount" class="eForm-label"><?php echo e(get_phrase('Paid amount').'('.school_currency().')'); ?></label>
			<input type="number" class="form-control eForm-control" id="paid_amount" name="paid_amount" required>
		</div>

		<div class="fpb-7">
			<label for="status" class="eForm-label"><?php echo e(get_phrase('Status')); ?></label>
			<select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove" required >
				<option value=""><?php echo e(get_phrase('Select a status')); ?></option>
				<option value="paid"><?php echo e(get_phrase('Paid')); ?></option>
				<option value="unpaid"><?php echo e(get_phrase('Unpaid')); ?></option>
			</select>
		</div>

		<div class="fpb-7">
			<label for="payment_method" class="eForm-label"><?php echo e(get_phrase('Payment method')); ?></label>
			<select name="payment_method" id="payment_method" class="form-select eForm-select eChoice-multiple-with-remove">
				<option value=""><?php echo e(get_phrase('Select a payment method')); ?></option>
				<option value="cash"><?php echo e(get_phrase('Cash')); ?></option>
				<option value="paypal"><?php echo e(get_phrase('Paypal')); ?></option>
				<option value="paytm"><?php echo e(get_phrase('Paytm')); ?></option>
				<option value="razorpay"><?php echo e(get_phrase('Razorpay')); ?></option>
				<option value="stripe"><?php echo e(get_phrase('Stripe')); ?></option>
				<option value="flutterwave"><?php echo e(get_phrase('Flutterwave')); ?></option>
			</select>
		</div>

	</div>
	<div class="fpb-7 pt-2">
		<button class="btn-form" type="submit"><?php echo e(get_phrase('Create invoice')); ?></button>
	</div>
</form>

<script type="text/javascript">

  "use strict";

	
	function classWiseSectionOnCreate(classId) {
		let url = "<?php echo e(route('admin.class_wise_sections', ['id' => ":classId"])); ?>";
    	url = url.replace(":classId", classId);
		$.ajax({
			url: url,
			success: function(response){
	            $('#section_id_on_create').html(response);
	        }
		});
	}

  $(document).ready(function () {
    $(".eChoice-multiple-with-remove").select2();
  });

</script><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/student_fee_manager/mass.blade.php ENDPATH**/ ?>