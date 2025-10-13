
<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('admin.appraisal.storeQustion')); ?>">
    <?php echo csrf_field(); ?> 
    <div class="fpb-7">
        <label for="class_id" class="eForm-label"><?php echo e(get_phrase("Class")); ?></label>
        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required">
            <option value=""><?php echo e(get_phrase("Select a class")); ?></option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>


    <div class="fpb-7">
        <label for="teacher_id[]" class="eForm-label"><?php echo e(get_phrase('Teachers')); ?></label>
        <select name="teacher_id[]" id="teacher_id" class="form-select eForm-select eChoice-multiple-with-remove multiple_teacher" multiple required>
            <option value=""><?php echo e(get_phrase('Select Teachers')); ?></option>
            <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($teacher->id); ?>"><?php echo e($teacher->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="fpb-7">
        <label class="eForm-label"><?php echo e(get_phrase("Ans Type")); ?></label>
    
        <div class="form-check">
            <input class="form-check-input" type="radio" name="ans_type" id="mcq" value="mcq" required>
            <label class="form-check-label" for="mcq">
                <?php echo e(get_phrase("MCQ")); ?> <span class="apprisal_ans_type">(<?php echo e(get_phrase('Excellent')); ?>, <?php echo e(get_phrase('Good')); ?>, <?php echo e(get_phrase('Average')); ?>, <?php echo e(get_phrase('Poor')); ?>)</span> 
            </label>
        </div>
    
        <div class="form-check">
            <input class="form-check-input" type="radio" name="ans_type" id="rating" value="rating">
            <label class="form-check-label" for="rating">
                <?php echo e(get_phrase("Rating")); ?> <span class="apprisal_ans_type">(⭐⭐⭐⭐⭐)</span> 
            </label>
        </div>
    
        <div class="form-check">
            <input class="form-check-input" type="radio" name="ans_type" id="binary" value="binary">
            <label class="form-check-label" for="binary">
                <?php echo e(get_phrase("Binary")); ?> <span class="apprisal_ans_type">(✅ <?php echo e(get_phrase('Yes')); ?> / ❌ <?php echo e(get_phrase('No')); ?>)</span>
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="ans_type" id="text" value="text">
            <label class="form-check-label" for="Text">
                <?php echo e(get_phrase("Text")); ?>

            </label>
        </div>
    </div>
    
    <div class="fpb-7">
        <label for="title" class="eForm-label"><?php echo e(get_phrase('Title')); ?></label>
        <input type="text" class="form-control eForm-control" id="title" name = "title" required>
    </div>

    <div class="fpb-7">
        <label for="qustion" class="eForm-label"><?php echo e(get_phrase('Qustion')); ?></label>
        <div class="new_div">
            <div class="row">
                <div class="col-sm-9" id="inputContainer">
                    <input type="text" name="question[]" class="eForm-control form-control" placeholder="<?php echo e(get_phrase('Write Question')); ?>">
                </div>
                <div class="col-sm-3 p-0">
                    <button type="button" onclick="appendInput()" class="btn btn-icon feature_btn btn-success"><i class="bi bi-plus"></i></button>
                    <button type="button"  onclick="removeInput()" class="btn btn-icon feature_btn btn-danger"> <i class="bi bi-dash"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="fpb-7">
        <label for="status" class="eForm-label"><?php echo e(get_phrase('Status')); ?></label>
        <select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove">
            <option value=""><?php echo e(get_phrase('Select a status')); ?></option>
            <option value="1"><?php echo e(get_phrase('Active')); ?></option>
            <option value="0"><?php echo e(get_phrase('Archive')); ?></option>
        </select>
    </div>

    <div class="fpb-7 pt-2">
        <button type="submit" class="btn-form"><?php echo e(get_phrase('Create')); ?></button>
    </div>
</form>

<script type="text/javascript">
    "use strict";

    $(document).ready(function() {
    $('.multiple_teacher').select2({
        placeholder: "<?php echo e(get_phrase('Select Teachers')); ?>",
        allowClear: true
    });
});

function appendInput() {
      var container = document.getElementById('inputContainer');
      var newInput = document.createElement('input');
      newInput.setAttribute('type', 'text');
      newInput.setAttribute('placeholder', '<?php echo e(get_phrase('Write Qustion')); ?>');
      newInput.setAttribute('class', 'eForm-control mt-2');
      newInput.setAttribute('name', 'question[]');
      container.appendChild(newInput);
    }

    function removeInput() {
      var container = document.getElementById('inputContainer');
      var inputs = container.getElementsByTagName('input');
      if (inputs.length > 1) {
        container.removeChild(inputs[inputs.length - 1]);
      }
    }
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/appraisal/createQustion.blade.php ENDPATH**/ ?>