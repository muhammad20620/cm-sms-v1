
   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4><?php echo e(get_phrase('Grades')); ?></h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Examknation')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Grades')); ?></a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn" onclick="rightModal('<?php echo e(route('admin.grade.open_modal')); ?>', '<?php echo e(get_phrase('Create Grade')); ?>')"><?php echo e(get_phrase('Add grade')); ?></a>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <?php if(count($grades) > 0): ?>
            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col"><?php echo e(get_phrase('Grade')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Grade Point')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Mark From')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Mark Upto')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                             <tr>
                                <td><?php echo e($loop->index + 1); ?></td>
                                <td><?php echo e($grade->name); ?></td>
                                <td><?php echo e($grade->grade_point); ?></td>
                                <td><?php echo e($grade->mark_from); ?></td>
                                <td><?php echo e($grade->mark_upto); ?></td>
                                <td class="text-start">
                                    <div class="adminTable-action ms-0">
                                        <button
                                          type="button"
                                          class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                                          data-bs-toggle="dropdown"
                                          aria-expanded="false"
                                        >
                                          <?php echo e(get_phrase('Actions')); ?>

                                        </button>
                                        <ul
                                          class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
                                        >
                                          <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('<?php echo e(route('admin.edit.grade', ['id' => $grade->id])); ?>', '<?php echo e(get_phrase('Edit Grade')); ?>')"><?php echo e(get_phrase('Edit')); ?></a>
                                          </li>
                                          <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.grade.delete', ['id' => $grade->id])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
                                          </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
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
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/grade/grade_list.blade.php ENDPATH**/ ?>