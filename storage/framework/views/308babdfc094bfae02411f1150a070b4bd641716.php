<?php 

use App\Http\Controllers\CommonController;
use App\Models\Book;

?>


<?php if(count($book_issues) > 0): ?>
<div class="table-responsive">
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th>#</th>
                <th><?php echo e(get_phrase('Book name')); ?></th>
                <th><?php echo e(get_phrase('Issue date')); ?></th>
                <th><?php echo e(get_phrase('Student')); ?></th>
                <th><?php echo e(get_phrase('Class')); ?></th>
                <th><?php echo e(get_phrase('Status')); ?></th>
                <th class="text-center"><?php echo e(get_phrase('Option')); ?></th>
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
                    <td><strong><?php echo e($book_details['name']); ?></strong></td>
                    <td>
                        <?php echo e(date('D, d/M/Y', $book_issue['issue_date'])); ?>

                    </td>
                    <td>
                        <strong><?php echo e($student_details['name']); ?></strong>
                        <br> 
                        <strong><?php echo e(get_phrase('Id')); ?>: </strong>
                        <small><?php echo e($student_details['code']); ?></small>
                    </td>
                    <td>
                        <?php echo e($student_details['class_name']); ?>

                    </td>
                    <td>
                        <?php if ($book_issue['status']): ?>
                            <span class="eBadge ebg-success"><?php echo e(get_phrase('Returned')); ?></span>
                        <?php else: ?>
                            <span class="eBadge ebg-danger"><?php echo e(get_phrase('Pending')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-start">
                        <div class="adminTable-action">
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
                            <?php if (!$book_issue['status']): ?>
                                  <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="rightModal('<?php echo e(route('admin.edit.book_issue', ['id' => $book_issue->id])); ?>', '<?php echo e(get_phrase('Update issued book')); ?>')"><?php echo e(get_phrase('Edit')); ?></a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.book_issue.return', ['id' => $book_issue->id])); ?>', 'undefined');"><?php echo e(get_phrase('Return this book')); ?></a>
                                  </li>
                            <?php endif; ?>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.book_issue.delete', ['id' => $book_issue->id])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
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

<?php if(count($book_issues) > 0): ?>
<div class="table-responsive display-none-view" id="book_issue_report">
    <h4 class="" style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;"><?php echo e(get_phrase('Book issue list')); ?></h4>
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
                    <td><strong><?php echo e($book_details['name']); ?></strong></td>
                    <td>
                        <?php echo e(date('D, d/M/Y', $book_issue['issue_date'])); ?>

                    </td>
                    <td>
                        <strong><?php echo e($student_details['name']); ?></strong>
                        <br> 
                        <strong><?php echo e(get_phrase('Id')); ?>: </strong>
                        <small><?php echo e($student_details['code']); ?></small>
                    </td>
                    <td>
                        <?php echo e($student_details['class_name']); ?>

                    </td>
                    <td>
                        <?php if ($book_issue['status']): ?>
                            <span class="eBadge ebg-success"><?php echo e(get_phrase('Returned')); ?></span>
                        <?php else: ?>
                            <span class="eBadge ebg-danger"><?php echo e(get_phrase('Pending')); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php endif; ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/book_issue/list.blade.php ENDPATH**/ ?>