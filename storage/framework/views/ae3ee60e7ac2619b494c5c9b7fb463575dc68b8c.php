<?php 

use App\Http\Controllers\CommonController;
use App\Models\Book;

?>


<?php if(count($book_issues) > 0): ?>
<div class="issued_book" id="issued_book_report">
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th>#</th>
                <th><?php echo e(get_phrase('Book name')); ?></th>
                <th><?php echo e(get_phrase('Issue date')); ?></th>
                <th><?php echo e(get_phrase('Student')); ?></th>
                <th><?php echo e(get_phrase('Class')); ?></th>
                <th><?php echo e(get_phrase('Status')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $book_issues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book_issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php 
                $book_details = Book::find($book_issue['book_id']);
                $student_details = (new CommonController)->get_student_details_by_id($book_issue['student_id']);
                ?>
                <tr>
                    <td><?php echo e($loop->index + 1); ?></td>
                    <td><?php echo e($book_details['name']); ?></td>
                    <td>
                        <?php echo e(date('D, d/M/Y', $book_issue['issue_date'])); ?>

                    </td>
                    <td>
                        <?php echo e($student_details['name']); ?><br> <small><?php echo e(get_phrase('Student code')); ?>: <?php echo e($student_details['code']); ?></small>
                    </td>
                    <td>
                        <?php echo e($student_details['class_name']); ?>

                    </td>
                    <td>
                        <?php if ($book_issue['status']): ?>
                            <span class="eBadge ebg-soft-success"><?php echo e(get_phrase('Returned')); ?></span>
                        <?php else: ?>
                            <span class="eBadge ebg-soft-warning"><?php echo e(get_phrase('Pending')); ?></span>
                        <?php endif; ?>
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
<?php endif; ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/student/book/issued_list.blade.php ENDPATH**/ ?>