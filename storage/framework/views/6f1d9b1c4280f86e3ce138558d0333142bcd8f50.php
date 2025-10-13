<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('admin.create.book')); ?>">
    <?php echo csrf_field(); ?> 
	<div class="form-row">
		<div class="fpb-7">
            <label for="name" class="eForm-label"><?php echo e(get_phrase('Book name')); ?></label>
            <input type="text" class="form-control eForm-control" id="name" name = "name" required>
        </div>

        <div class="fpb-7">
            <label for="author" class="eForm-label"><?php echo e(get_phrase('Author')); ?></label>
            <input type="text" class="form-control eForm-control" id="author" name = "author" required>
        </div>

        <div class="fpb-7">
            <label for="copies" class="eForm-label"><?php echo e(get_phrase('Number of scopy')); ?></label>
            <input type="number" class="form-control eForm-control" id="copies" name = "copies" min="0" required>
        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit"><?php echo e(get_phrase('Save book')); ?></button>
        </div>
	</div>
</form><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/book/create.blade.php ENDPATH**/ ?>