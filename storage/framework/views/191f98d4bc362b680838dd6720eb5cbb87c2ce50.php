<?php 

use App\Models\ExpenseCategory;

?>
<?php if(count($expenses) > 0): ?>
<div class="table-responsive expense_report" id="expense_report">
    <table id="basic-datatable" class="table eTable">
        <thead>
          <tr>
            <th><?php echo e(get_phrase('Date')); ?></th>
            <th><?php echo e(get_phrase('Amount')); ?></th>
            <th><?php echo e(get_phrase('Expense category')); ?></th>
            <th class="text-end"><?php echo e(get_phrase('Option')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($expenses as $expense): ?>
            <tr>
                <td>
                    <?php echo e(date('D, d-M-Y', $expense['date'])); ?>

                </td>
                <td>
                    <?php echo e(school_currency($expense['amount'])); ?>

                </td>
                <td>
                    <?php $expense_categories = ExpenseCategory::find($expense['expense_category_id']); ?>
                    <?php echo e($expense_categories['name']); ?>

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
                          <li>
                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('<?php echo e(route('admin.edit.expenses', ['id' => $expense->id])); ?>', '<?php echo e(get_phrase('Edit Expense')); ?>')"><?php echo e(get_phrase('Edit')); ?></a>
                          </li>
                          <li>
                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.expense.delete', ['id' => $expense->id])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
                          </li>
                        </ul>
                    </div>
                </td>
            </tr>
          <?php endforeach; ?>
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/expenses/list.blade.php ENDPATH**/ ?>