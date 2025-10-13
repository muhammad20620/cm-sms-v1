<form method="POST" class="col-md-12 ajaxForm" action="<?php echo e(route('admin.offline_admission.excel_create')); ?>" id = "student_admission_form" enctype="multipart/form-data">
    <?php echo csrf_field(); ?> 
    <div class="row justify-content-md-center">
        <div class="col-xl-4">
            <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" onchange="classWiseSection(this.value)" required>
                <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                <?php $__currentLoopData = $data['classes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-xl-4" id = "section_content">
            <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value=""><?php echo e(get_phrase('Select section')); ?></option>
            </select>
        </div>
        <div class="row mt-4">
            <div class="col-xl-2"></div>
            <div class="col-xl-4 p-1">
                <div class="col-12">
                    <a href="<?php echo e(asset('assets/csv_file/student.generate.csv')); ?>" class="btn btn-success btn-sm" download>Generate csv file <i class="bi bi-download"></i> </a>
                    <button href="#" class="btn btn-dark bi bi-eye" onclick="largeModal('<?php echo e(route('admin.offline_admission.preview')); ?>', 'CSV Format');" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo e('Preview csv format'); ?>"></button>

                </div>
            </div>
            <br>
        </div>
        <div class="col-md-8 mt-4">
            <div class="form-group">
                <label class="eForm-label"><?php echo e('Upload CSV'); ?></label>
                <div class="custom-file-upload">
                    <input type="file" id="csv_file" class="form-control eForm-control-file" name="csv_file" required>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-secondary col-md-4 col-sm-12 mb-4 mt-3"><?php echo e(get_phrase('Add students')); ?></button>
    </div>
</form>

<script type="text/javascript">
  
    "use strict";
    
    $(document).ready(function(){
        initCustomFileUploader();
    });

    var form;
    $(".ajaxForm").submit(function(e) {
      form = $(this);
      ajaxSubmit(e, form, refreshForm);
    });
    var refreshForm = function () {
        form.trigger("reset");
    }
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/offline_admission/excel_student_admission.blade.php ENDPATH**/ ?>