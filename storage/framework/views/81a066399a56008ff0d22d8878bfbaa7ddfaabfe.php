<?php 

use App\Models\BookIssue;

?>
<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('admin.create.book_issue')); ?>">
    <?php echo csrf_field(); ?> 
	<div class="form-row">

		<div class="fpb-7">
	        <label for="issue_date" class="eForm-label"><?php echo e(get_phrase('Issue date')); ?></label>
	        <input type="text" class="form-control eForm-control inputDate" id="issue_date" name = "issue_date" value="<?php echo e(date('m/d/Y')); ?>" required>
	    </div>

	    <div class="fpb-7">
	        <label for="class_id" class="eForm-label"><?php echo e(get_phrase('Class')); ?></label>
	        <select name="class_id" id="class_id_on_modal" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseStudentOnCreate(this.value)">
                <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($class['id']); ?>"><?php echo e($class['name']); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
	    </div>

	    <div class="fpb-7">
	        <label for="student_id" class="eForm-label"> <?php echo e(get_phrase('Student')); ?></label>
	        <select name="student_id" id="student_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value=""><?php echo e(get_phrase('Select a student')); ?></option>
            </select>
	    </div>

	    <div class="fpb-7">
	        <label for="book_id" class="eForm-label"> <?php echo e(get_phrase('Book')); ?></label>
	        <select name="book_id" id="book_id_on_modal" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value=""><?php echo e(get_phrase('Select book')); ?></option>
                <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	                	<?php $number_of_issued_book = BookIssue::get()->where('book_id', $book['id'])->where('status', 0); ?>
	      						<?php if($book['copies'] - count($number_of_issued_book) > 0): ?>
                    	<option value="<?php echo e($book['id']); ?>"><?php echo e($book['name']); ?></option>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
	    </div>

	    <div class="fpb-7 pt-2">
		    <button class="btn-form" type="submit"><?php echo e(get_phrase('Save')); ?></button>
		</div>
	</div>
</form>

<script type="text/javascript">

  "use strict";

	
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

	$(function () {
      $('.inputDate').daterangepicker(
        {
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format("YYYY"), 10),
        },
        function (start, end, label) {
          var years = moment().diff(start, "years");
        }
      );
    });

	
  $(document).ready(function () {
    $(".eChoice-multiple-with-remove").select2();
  });

</script><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/book_issue/create.blade.php ENDPATH**/ ?>