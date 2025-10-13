
   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4><?php echo e(get_phrase('Promotions')); ?></h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Examination')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Promotions')); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="row mt-3">

                <div class="col-md-1"></div>

                <div class="col-md-2">
                    <label for="session_from" class="eForm-label"><?php echo e(get_phrase('Current session')); ?></label>
                    <select class="form-select eForm-select eChoice-multiple-with-remove" id = "session_from" name="session_from">
                        <option value=""><?php echo e(get_phrase('Session from')); ?></option>
                        <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($session->id); ?>"><?php echo e($session->session_title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>

                <div class="col-md-2">
                    <label for="session_to" class="eForm-label"><?php echo e(get_phrase('Next session')); ?></label>
                    <select class="form-select eForm-select eChoice-multiple-with-remove" id = "session_to" name="session_to">
                        <option value=""><?php echo e(get_phrase('Session to')); ?></option>
                        <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($session->id); ?>"><?php echo e($session->session_title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="class_id_from" class="eForm-label"><?php echo e(get_phrase('Promoting from')); ?></label>
                    <select name="class_id_from" id="class_id_from" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSectionFrom(this.value)">
                        <option value=""><?php echo e(get_phrase('Promoting from')); ?></option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="section_id_from" class="eForm-label"><?php echo e(get_phrase('Section')); ?></label>
                    <select name="section_id_from" id="section_id_from" class="form-select eForm-select eChoice-multiple-with-remove" required >
                        <option value=""><?php echo e(get_phrase('First select a class')); ?></option>
                    </select>
                </div>
                
            </div>

            <div class="row mt-3">

                <div class="col-md-2"></div>

                <div class="col-md-2">
                    <label for="class_id_to" class="eForm-label"><?php echo e(get_phrase('Promoting to')); ?></label>
                    <select name="class_id_to" id="class_id_to" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSectionTo(this.value)">
                        <option value=""><?php echo e(get_phrase('Promoting to')); ?></option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="section_id_to" class="eForm-label"><?php echo e(get_phrase('Section')); ?></label>
                    <select name="section_id_to" id="section_id_to" class="form-select eForm-select eChoice-multiple-with-remove" required >
                        <option value=""><?php echo e(get_phrase('First select a class')); ?></option>
                    </select>
                </div>

                <div class="col-md-3 pt-2">
                    <button class="eBtn eBtn btn-secondary mt-4" type="button" id = "manage_student" onclick="manage_student()"><?php echo e(get_phrase('Manage promotion')); ?></button>
                </div>

                <div class="card-body table-responsive subject_content">
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<script type="text/javascript">

  "use strict";

    function classWiseSectionFrom(classId) {
        let url = "<?php echo e(route('admin.class_wise_sections', ['id' => ":classId"])); ?>";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id_from').html(response);
            }
        });
    }

    function classWiseSectionTo(classId) {
        let url = "<?php echo e(route('admin.class_wise_sections', ['id' => ":classId"])); ?>";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id_to').html(response);
            }
        });
    }

    function manage_student(){
        var session_id_from = $('#session_from').val();
        var session_id_to = $('#session_to').val();
        var class_id_from = $('#class_id_from').val();
        var section_id_from = $('#section_id_from').val();
        var class_id_to = $('#class_id_to').val();
        var section_id_to = $('#section_id_to').val();
        if(session_id_from != "" && session_id_to != "" && class_id_from != "" && section_id_from != "" && class_id_to != "" && section_id_to != ""){
            show_promotion_list();
        }else{
        	// console.log("Hello");
        	// alert(class_id_to);
            toastr.error("Please select all the fields correctly");
        }
    }

    var show_promotion_list = function () {
        var session_id_from = $('#session_from').val();
        var session_id_to = $('#session_to').val();
        var class_id_from = $('#class_id_from').val();
        var section_id_from = $('#section_id_from').val();
        var class_id_to = $('#class_id_to').val();
        var section_id_to = $('#section_id_to').val();
        if(session_id_from != "" && session_id_to != "" && class_id_from != "" && section_id_from != "" && class_id_to != "" && section_id_to != ""){


            $.ajax({
                url: '<?php echo e(route('admin.promotion.promotion_list')); ?>',
                type: 'GET',
                data: { 
                	session_id_from: session_id_from, 
                	session_id_to: session_id_to,
                	class_id_from: class_id_from,
                	section_id_from: section_id_from,
                	class_id_to: class_id_to,
                	section_id_to: section_id_to
                },
                success: function(response){
                    $('.subject_content').html(response);
                }
            });
        }
    }

</script>
<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/promotion/promotion.blade.php ENDPATH**/ ?>