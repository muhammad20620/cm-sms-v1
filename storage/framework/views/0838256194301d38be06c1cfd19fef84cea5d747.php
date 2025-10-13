<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('admin.create.expense_category')); ?>">
  <?php echo csrf_field(); ?> 
  <div class="form-row">
    <div class="fpb-7">
      <label for="name" class="eForm-label"><?php echo e(get_phrase('Expense category name')); ?></label>
      <input type="text" class="form-control eForm-control" id="name" name = "name" required>
    </div>

    <div class="fpb-7 pt-2">
      <button class="btn-form" type="submit"><?php echo e(get_phrase('Save category')); ?></button>
    </div>
  </div>
</form>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/expense_category/create.blade.php ENDPATH**/ ?>