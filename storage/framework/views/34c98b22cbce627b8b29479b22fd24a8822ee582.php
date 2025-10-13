   
<?php $__env->startSection('content'); ?>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Appraisal')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Appraisal')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Appraisal Qustion')); ?></a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('<?php echo e(route('admin.appraisal.createQustion')); ?>', '<?php echo e(get_phrase('Create Qustion')); ?>')"><i class="bi bi-plus"></i><?php echo e(get_phrase('Add New Qustion')); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Table -->
<div class="row">
    <div class="col-12">
        <?php if(count($appraisals) > 0): ?>
        <div class="eSection-wrap-2">
            <div class="table-responsive">
                <table class="table eTable eTable-2">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col"><?php echo e(get_phrase('Title')); ?></th>
                      <th scope="col"><?php echo e(get_phrase('Teachers')); ?></th>
                      <th scope="col"><?php echo e(get_phrase('Qustions')); ?></th>
                      <th scope="col"><?php echo e(get_phrase('Ans Type')); ?></th>
                      <th scope="col"><?php echo e(get_phrase('Status')); ?></th>
                      <th scope="col"><?php echo e(get_phrase('Action')); ?></th>
                  </thead>
                    <tbody>
                        <?php $__currentLoopData = $appraisals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appraisal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                // Get class details
                                $classes = DB::table('classes')->where('id', $appraisal->class_id)->first();
                    
                                // Get teacher IDs and names
                                $teacher_ids = json_decode($appraisal->teacher_id);
                                $teacher_names = [];
                                foreach($teacher_ids as $teacher_id) {
                                    $teacher = DB::table('users')->where('id', $teacher_id)->first();
                                    if ($teacher) {
                                        $teacher_names[] = $teacher->name;
                                    }
                                }
                                $teacher_list = implode(', ', $teacher_names); // Join all teacher names with a comma
                    
                                // Get questions list
                                $questions = json_decode($appraisal->question);
                            ?>
                    
                            <tr>
                                <th scope="row">
                                    <p class="row-number"><?php echo e($loop->index + 1); ?></p>
                                </th>
                                <td>
                                    <div class="dAdmin_profile d-flex align-items-center">
                                        <div class="dAdmin_profile_name dAdmin_info_name">
                                            <h4><?php echo e($appraisal->title); ?></h4>
                                            <p>
                                                <?php if(empty($classes->name)): ?>
                                                    <span><?php echo e(get_phrase('Class')); ?>:</span> <?php echo e(get_phrase('removed')); ?>

                                                <?php else: ?>
                                                    <span><?php echo e(get_phrase('Class')); ?>:</span> <?php echo e($classes->name); ?>

                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name">
                                        <div class="teacher-list-container">
                                            <ol class="teacher-list">
                                                <?php $__currentLoopData = $teacher_names; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher_name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($loop->index + 1); ?>. <?php echo e($teacher_name); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ol>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name">
                                        <?php if(!empty($questions)): ?>
                                            <div class="question-list-container">
                                                <ol class="question-list">
                                                    <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                
                                                        <li><?php echo e($loop->index + 1); ?>. <?php echo e($question); ?></li> 
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ol>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name">
                                        <p><?php echo e(ucwords($appraisal->ans_type)); ?></p>
                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name">
                                        <?php if($appraisal->status == 1): ?>
                                            <span class="eBadge ebg-soft-success"><?php echo e(get_phrase('Active')); ?></span>
                                        <?php else: ?>
                                            <span class="eBadge ebg-soft-danger"><?php echo e(get_phrase('Archived')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name">
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
                                                <a class="dropdown-item" href="javascript:;" onclick="rightModal('<?php echo e(route('admin.appraisal.appraisalQustionEdit', ['id' => $appraisal->id])); ?>', '<?php echo e(get_phrase('Edit Qustion')); ?>')"><?php echo e(get_phrase('Edit')); ?></a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.appraisal.appraisalQustionDelete', ['id' => $appraisal->id])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
                                            </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                    </tbody>
                </table>
                <div class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                    <p class="admin-tInfo"><?php echo e(get_phrase('Showing').' 1 - '.count($appraisals).' '.get_phrase('from').' '.$appraisals->total().' '.get_phrase('data')); ?></p>
                    <div class="admin-pagi">
                      <?php echo $appraisals->appends(request()->all())->links(); ?>

                    </div>
                </div>
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
</div>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/appraisal/appraisalQustions.blade.php ENDPATH**/ ?>