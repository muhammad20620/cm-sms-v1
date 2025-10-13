
   
<?php $__env->startSection('content'); ?>

<?php 

use App\Http\Controllers\CommonController;
use App\Models\School;
use App\Models\User;

?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Feedback')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Back Office')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Feedback')); ?></a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('<?php echo e(route('admin.feedback.create_feedback')); ?>', '<?php echo e(get_phrase('Create Feedback')); ?>')"><i class="bi bi-plus"></i><?php echo e(get_phrase('Add New Feedback')); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
  <?php if(count($feedbacks) > 0): ?>
    <?php $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedback): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $student_details = (new CommonController)->get_student_academic_info($feedback->student_id);
        $parent_details = User::find($student_details->parent_id);

        $admin = User::find($feedback->admin_id);

        if(!empty($admin)){
        $info = json_decode($admin->user_information);
            $user_image = $info->photo;
            if(!empty($info->photo)){
                $user_image = 'uploads/user-images/'.$info->photo;
            }else{
                $user_image = 'uploads/user-images/thumbnail.png';
            }
        }
    ?>
    <div class="col-md-4">
    <div class="eCard eCard-2">
      <div class="eCard-body">
        <div class="d-flex justify-content-between">
          <h5 class="eCard-subtitle"><span><?php echo e(get_phrase('Student')); ?>: </span><?php echo e($student_details->name); ?></h5>
          <h5 class="eCard-subtitle"><span><?php echo e(get_phrase('Parent')); ?>: </span><?php echo e($parent_details->name); ?></h5>
        </div>
        
        
        <div class="card_subtitle d-flex justify-content-between">
            <?php if(empty($student_details->class_name)): ?>
            <h5 class="eCard-subtitle"><span><?php echo e(get_phrase('Class')); ?>: </span><?php echo e(get_phrase('removed')); ?></h5>
            <h5 class="eCard-subtitle"><span><?php echo e(get_phrase('Section')); ?>: </span><?php echo e(get_phrase('removed')); ?></h5>
        <?php else: ?>
            <h5 class="eCard-subtitle"><span><?php echo e(get_phrase('Class')); ?>: </span><?php echo e($student_details->class_name); ?></h5>
            <h5 class="eCard-subtitle"><span><?php echo e(get_phrase('Section')); ?>: </span><?php echo e($student_details->section_name); ?></h5>
            <?php endif; ?>
        </div>
      </div>
      <div class="eCard-body">
        <h5 class="eCard-title"><?php echo e($feedback->title); ?></h5>
        <p class="eCard-text">
            <?php echo e($feedback->feedback_text); ?>

        </p>
        <div class="eCard-AdminBtn d-flex flex-wrap justify-content-between align-items-center">
          <div class="eCard-Admin d-flex align-items-center">
            <?php if(!empty($admin->name)): ?>
            <img src="<?php echo e(asset('assets')); ?>/<?php echo e($user_image); ?>" alt="" class="eCard-userImg">
            
            <span><?php echo e($admin->name); ?></span>
            <?php endif; ?>
          </div>
          <?php if(auth()->user()->id == $feedback->admin_id): ?>
          <div class="adminTable-action">
            <button
              type="button"
              class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
              data-bs-toggle="dropdown"
              aria-expanded="false"
              style="color: #797c8b"
            >
              <?php echo e(get_phrase('Actions')); ?>

            </button>
            <ul
              class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
            >
              <li>
                <a class="dropdown-item" href="javascript:;" onclick="rightModal('<?php echo e(route('admin.feedback.edit_feedback', ['id' => $feedback->id])); ?>', '<?php echo e(get_phrase('Edit feedback')); ?>')"><?php echo e(get_phrase('Edit')); ?></a>
              </li>
              <li>
                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.feedback.delete_feedback', ['id' => $feedback->id])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
              </li>
            </ul>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    </div>
    
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <div class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
      <p class="admin-tInfo"><?php echo e(get_phrase('Showing').' 1 - '.count($feedbacks).' '.get_phrase('from').' '.$feedbacks->total().' '.get_phrase('data')); ?></p>
      <div class="admin-pagi">
        <?php echo $feedbacks->appends(request()->all())->links(); ?>

      </div>
  </div>
  <?php else: ?>
    <div class="empty_box center">
      <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
      <br>
      <span class=""><?php echo e(get_phrase('No data found')); ?></span>
    </div> 
  <?php endif; ?>
</div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/feedback/feedback_list.blade.php ENDPATH**/ ?>