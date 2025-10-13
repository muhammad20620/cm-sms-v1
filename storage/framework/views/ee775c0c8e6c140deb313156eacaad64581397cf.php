<div class="eoff-form">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('admin.create.grade')); ?>">
        <?php echo csrf_field(); ?> 
        <div class="form-row">
            <div class="fpb-7">
                <label for="grade" class="eForm-label"><?php echo e(get_phrase('Grade')); ?></label>
                <input type="text" class="form-control eForm-control" id="grade" name = "grade" required>
            </div>

            <div class="fpb-7">
                <label for="grade_point" class="eForm-label"><?php echo e(get_phrase('Grade point')); ?></label>
                <input type="number" class="form-control eForm-control" id="grade_point" name = "grade_point" step=".01" min="0" placeholder="Provide grade point" aria-label="Provide grade point" required>
            </div>

            <div class="fpb-7">
                <label for="mark_from" class="eForm-label"><?php echo e(get_phrase('Mark From')); ?></label>
                <input type="number" class="form-control eForm-control" id="mark_from" name = "mark_from" min="0" placeholder="Mark from" aria-label="Mark from" required>
            </div>

            <div class="fpb-7">
                <label for="mark_upto" class="eForm-label"><?php echo e(get_phrase('Mark upto')); ?></label>
                <input type="number" class="form-control eForm-control" id="mark_upto" name = "mark_upto" min="0" placeholder="Mark upto" aria-label="Mark upto" required>
            </div>

            <div class="fpb-7 pt-2">
                <button class="btn-form" type="submit"><?php echo e(get_phrase('Create')); ?></button>
            </div>
        </div>
    </form>
</div><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/grade/add_grade.blade.php ENDPATH**/ ?>