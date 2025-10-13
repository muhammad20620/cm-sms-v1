<?php 

use App\Http\Controllers\CommonController;
use App\Models\Enrollment;
use App\Models\DailyAttendances;

$active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
$enrols = Enrollment::where(['class_id' => $page_data['class_id'], 'section_id' => $page_data['section_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get();

?>

<div class="row mb-2">
    <div class="col-6"><a href="javascript:" class="btn btn-sm btn-secondary" onclick="present_all()"><?php echo e(get_phrase('Present All')); ?></a></div>
    <div class="col-6"><a href="javascript:" class="btn btn-sm btn-secondary float-right" onclick="absent_all()"><?php echo e(get_phrase('Absent All')); ?></a></div>
</div>

<div class="table-responsive-sm row col-md-12">
    <table class="table eTable table-bordered">
        <thead>
            <tr>
                <th><?php echo e(get_phrase('Name')); ?></th>
                <th><?php echo e(get_phrase('Status')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $enrols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $student_details = (new CommonController)->get_student_details_by_id($enroll['user_id'])->toArray(); ?>
            <tr>
                <td>
                    <?php echo e($student_details['name']); ?>

                </td>
                <td>
                    <input type="hidden" name="student_id[]" value="<?php echo e($enroll->user_id); ?>">
                    <div class="custom-control custom-radio">
                        <?php $update_attendance = DailyAttendances::where(['timestamp' => strtotime($page_data['attendance_date']), 'class_id' => $page_data['class_id'], 'section_id' => $page_data['section_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session, 'student_id' => $enroll->user_id]); 
                        $count_row = $update_attendance->get();
                        ?>
                        <?php if($count_row->count() > 0): ?>
                            <?php $row = $update_attendance->first(); ?>
                            <input type="hidden" name="attendance_id[]" value="<?php echo e($row->id); ?>">
                            <input type="radio" id="" name="status-<?php echo e($enroll['user_id']); ?>" value="1" class="present" <?php echo e($row->status == 1 ? 'checked':''); ?> required> <?php echo e(get_phrase('present')); ?> &nbsp;
                            <input type="radio" id="" name="status-<?php echo e($enroll['user_id']); ?>" value="0" class="absent" <?php echo e($row->status != 1 ? 'checked':''); ?> required> <?php echo e(get_phrase('absent')); ?>

                        <?php else: ?>
                            <input type="radio" id="" name="status-<?php echo e($enroll['user_id']); ?>" value="1" class="present" required> <?php echo e(get_phrase('present')); ?> &nbsp;
                            <input type="radio" id="" name="status-<?php echo e($enroll['user_id']); ?>" value="0" class="absent" checked required> <?php echo e(get_phrase('absent')); ?>

                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">

  "use strict";

    function present_all() {
        $(".present").prop('checked', true);
    }

    function absent_all() {
        $(".absent").prop('checked',true);
    }
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/attendance/student.blade.php ENDPATH**/ ?>