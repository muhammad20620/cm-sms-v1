<?php
use App\Http\Controllers\CommonController;
?>

<?php if(count($invoices) > 0): ?>
<div class="table-responsive">
    <table id="student_fee" class="table eTable eTable-2">
        <thead>
            <tr>
                <th><?php echo e(get_phrase('Invoice No')); ?></th>
                <th><?php echo e(get_phrase('Student')); ?></th>
                <th><?php echo e(get_phrase('Invoice Title')); ?></th>
                <th><?php echo e(get_phrase('Total Amount')); ?></th>
                <th><?php echo e(get_phrase('Paid Amount')); ?></th>
                <th><?php echo e(get_phrase('Document')); ?></th>
                <th><?php echo e(get_phrase('Status')); ?></th>
                <th><?php echo e(get_phrase('Option')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td>
                        <?php echo e(sprintf('%08d', $invoice['id'])); ?>

                    </td>
                    <td>
                        <strong><?php echo e($student_details['name']); ?></strong> <br>
                        <small> <strong><?php echo e(get_phrase('Class')); ?> :</strong> <?php echo e($student_details['class_name']); ?></small> <br>
                        <small> <strong><?php echo e(get_phrase('Section')); ?> :</strong> <?php echo e($student_details->section_name); ?></small>
                    </td>
                    <td>
                        <?php echo e($invoice['title']); ?>

                    </td>
                    <td>
                        <?php echo e(school_currency($invoice['total_amount'])); ?> <br>
                        <small> <strong> <?php echo e(get_phrase('Created at')); ?> : </strong> <?php echo e(date('d-M-Y', $invoice['timestamp'])); ?> </small>
                    </td>
                    <td>
                        <?php echo e(school_currency($invoice['paid_amount'])); ?> <br>
                        <small>
                            <strong> <?php echo e(get_phrase('Payment date')); ?> : </strong>
                            <?php
                            $updated_time = strtotime($invoice['updated_at']);
                            ?>
                            <?php if ($updated_time != ""): ?>
                                <?php echo e(date('d-M-Y', $updated_time)); ?>

                            <?php else: ?>
                                <?php echo e(get_phrase('Not found')); ?>

                            <?php endif; ?>

                        </small>
                    </td>
                     <td> <a href="<?php echo e(asset('assets/uploads/offline_payment/'.$invoice['document_image'])); ?>" download> <?php echo e($invoice['document_image']); ?> </a></td>
                    <td>
                        <?php if (strtolower($invoice['status']) == 'unpaid'): ?>
                            <span class="eBadge ebg-success"><?php echo e(ucfirst($invoice['status'])); ?></span>
                        <?php else: ?>
                            <span class="eBadge ebg-danger"><?php echo e(ucfirst($invoice['status'])); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
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
                              <li>
                                <a class="dropdown-item" href="<?php echo e(route('admin.studentFeeinvoice', ['id'=>$invoice['id']])); ?>" target="_blank"><?php echo e(get_phrase('Print invoice')); ?></a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.update_offline_payment', ['id' => $invoice->id,'status'=>'approve'])); ?>', 'undefined');"><?php echo e(get_phrase('Approve')); ?></a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.update_offline_payment', ['id' => $invoice->id,'status'=>'decline'])); ?>', 'undefined');"><?php echo e(get_phrase('Decline')); ?></a>
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
<div class="card-body permission_content">
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
        <br>
        <?php echo e(get_phrase('No data found')); ?>

    </div>
</div>
<?php endif; ?>


<div class="table-responsive display-none-view" id="student_fee_report ">
    <table id="student_fee_report" class="table eTable eTable-2">
        <thead>
            <tr>
                <th><?php echo e(get_phrase('Invoice No')); ?></th>
                <th><?php echo e(get_phrase('Student')); ?></th>
                <th><?php echo e(get_phrase('Class & Section')); ?></th>
                <th><?php echo e(get_phrase('Invoice Title')); ?></th>
                <th><?php echo e(get_phrase('Total Amount')); ?></th>
                <th><?php echo e(get_phrase('Created at')); ?></th>
                <th><?php echo e(get_phrase('Paid Amount')); ?></th>
                <th><?php echo e(get_phrase('Status')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td>
                        <?php echo e(sprintf('%08d', $invoice['id'])); ?>

                    </td>
                    <td>
                        <strong><?php echo e($student_details['name']); ?></strong>
                    </td>
                    <td>
                        <small><?php echo e($student_details['class_name']); ?></small><br>
                        <small><?php echo e($student_details['section_name']); ?></small>
                    </td>
                    <td>
                        <?php echo e($invoice['title']); ?>

                    </td>
                    <td>
                        <?php echo e(school_currency($invoice['total_amount'])); ?>

                    </td>
                    <td>
                        <small><?php echo e(date('d-M-Y', $invoice['timestamp'])); ?> </small>
                    </td>
                    <td>
                        <?php echo e(school_currency($invoice['paid_amount'])); ?>

                    </td>
                    <td>
                        <?php if (strtolower($invoice['status']) == 'unpaid'): ?>
                            <span class="eBadge ebg-success"><?php echo e(ucfirst($invoice['status'])); ?></span>
                        <?php else: ?>
                            <span class="eBadge ebg-danger"><?php echo e(ucfirst($invoice['status'])); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/student_fee_manager/list_pending.blade.php ENDPATH**/ ?>