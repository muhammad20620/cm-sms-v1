<?php use App\Models\Classes; ?>


   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Subjects')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Academic')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Subjects')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 col-md-7 col-sm-12 col-12 offset-md-2">
        <div class="eSection-wrap pb-2">
        	<?php if(count($subjects) > 0): ?>
        		<table id="basic-datatable" class="table eTable">
        			<thead>
						<tr>
							<th><?php echo e(get_phrase('Name')); ?></th>
                            <th><?php echo e(get_phrase('Class')); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td><?php echo e($subject['name']); ?></td>
                            <td>
                                <?php $class_details = Classes::find($subject['class_id']); ?>
                                <?php echo e($class_details['name']); ?>

                            </td>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
        		</table>
                <?php echo $subjects->appends(request()->all())->links(); ?>

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
<?php echo $__env->make('student.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/student/subject/subject_list.blade.php ENDPATH**/ ?>