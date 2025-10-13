<?php
use App\Models\Subject;
?>



<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Syllabus')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Academic')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Syllabus')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-8 offset-md-2">
        <div class="eSection-wrap pb-2">
            <?php if(count($syllabuses) > 0): ?>
            <table id="basic-datatable" class="table eTable">
                <thead>
                    <tr>
                        <th><?php echo e(get_phrase('Title')); ?></th>
                        <th><?php echo e(get_phrase('Syllabus')); ?></th>
                        <th><?php echo e(get_phrase('Subject')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $syllabuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $syllabus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($syllabus['title']); ?></td>
                            <td><a href="<?php echo e(asset('assets/uploads/syllabus')); ?>/<?php echo e($syllabus['file']); ?>" class="btn btn-primary btn-sm bi bi-download" download><?php echo e(get_phrase('Download')); ?></a></td>
                            <td>
                                <?php $subjects = Subject::find($syllabus['subject_id']); ?>
                                <?php echo e($subjects->name); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php echo $syllabuses->appends(request()->all())->links(); ?>

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
<?php echo $__env->make('student.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/student/syllabus/syllabus.blade.php ENDPATH**/ ?>