<style>
    .limitedst{
    display: none;
}
</style>

<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('superadmin.package.update', ['id' => $package->id])); ?>">
         <?php echo csrf_field(); ?> 
        <div class="form-row">
            <div class="fpb-7">
                <label for="name" class="eForm-label"><?php echo e(get_phrase('Name')); ?></label>
                <input type="text" class="form-control eForm-control" value="<?php echo e($package->name); ?>" id="name" name = "name" placeholder="Provide package name" required>
            </div>
            <div class="fpb-7">
                <label for="price" class="eForm-label"><?php echo e(get_phrase('Package price')); ?></label>
                <input type="number" min="0" class="form-control eForm-control" value="<?php echo e($package->price); ?>" id="price" name = "price" placeholder="Provide package price" required>
            </div>
            <div class="fpb-7">
                <label for="package_type" class="eForm-label"><?php echo e(get_phrase('Package Type')); ?></label>
                <select name="package_type" id="package_type" class="form-select eForm-select eChoice-multiple-with-remove" >
                    <option value=""><?php echo e(get_phrase('Select a package type')); ?></option>
                    <option value="paid" <?php echo e($package->package_type == 'paid' ?  'selected':''); ?> ><?php echo e(get_phrase('Paid')); ?></option>
                    <option value="trial" <?php echo e($package->package_type == 'trial' ?  'selected':''); ?>><?php echo e(get_phrase('Trial')); ?></option>
                    
                </select>
            </div>
            <div class="fpb-7">
                <label for="interval" class="eForm-label"><?php echo e(get_phrase('Interval')); ?></label>
                <select name="interval" id="interval" class="form-select eForm-select eChoice-multiple-with-remove" onchange="togglepackageWiseOptions(this.value)">
                    <option value=""><?php echo e(get_phrase('Select a interval')); ?></option>
                    <option value="Days" <?php echo e($package->interval == 'Days' ?  'selected':''); ?> ><?php echo e(get_phrase('Days')); ?></option>
                    <option value="Monthly" <?php echo e($package->interval == 'Monthly' ?  'selected':''); ?> ><?php echo e(get_phrase('Monthly')); ?></option>
                    <option value="Yearly" <?php echo e($package->interval == 'Yearly' ?  'selected':''); ?> ><?php echo e(get_phrase('Yearly')); ?></option>
                    <option value="life_time" <?php echo e($package->interval == 'life_time' ?  'selected':''); ?>><?php echo e(get_phrase('Life time')); ?></option>
                </select>
            </div>

            <div class="fpb-7">
                <label for="days" class="eForm-label"><?php echo e(get_phrase('Interval Preiod')); ?></label>
                <input type="number" min="0" class="form-control eForm-control" value="<?php echo e($package->days); ?>" id="days" name = "days" placeholder="Provide interval days/month/year" required>
            </div>

            <div class="fpb-7 limitst">
                <input  class="form-check-input" type="radio" name="studentLimit" id="unlimitedst" value="Unlimited" <?php echo e($package->studentLimit == 'Unlimited' ?  'checked':''); ?> >
                <label class="eForm-label" for="unlimitedst">
                    Unlimited Students
                </label>
            </div>

            <div class="fpb-7">
                <input class="form-check-input" type="radio" name="studentLimit" id="limited" value="<?php echo e($package->studentLimit); ?>"     <?php echo e($package->studentLimit != 'Unlimited' ?  'checked':''); ?>>
                <label class="eForm-label" for="limited">
                  Limited Students
                </label>
            </div>

            <div class="fpb-7 limitedst">
                <label for="studentLimit" class="eForm-label"><?php echo e(get_phrase('Students Limit')); ?></label>
                <input type="number" min="0" class="form-control eForm-control" id="studentLimit" name="" value="<?php echo e($package->studentLimit); ?>" placeholder="Provide Students Limit" >
            </div>


            <div class="fpb-7">
                <label for="status" class="eForm-label"><?php echo e(get_phrase('Status')); ?></label>
                <select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove">
                    <option value=""><?php echo e(get_phrase('Select a status')); ?></option>
                    <option value="1" <?php echo e($package->status == '1' ?  'selected':''); ?> ><?php echo e(get_phrase('Active')); ?></option>
                    <option value="0" <?php echo e($package->status == '0' ?  'selected':''); ?> ><?php echo e(get_phrase('Archive')); ?></option>
                </select>
            </div>
            <?php if(!empty($package->features)): ?>
            <div class="fpb-7">
                <label for="features" class="eForm-label"><?php echo e(get_phrase('Features')); ?></label>
                <div class="new_div">
                    <div class="row">
                       
                        <?php

						$packages_features = json_decode($package->features);
                        
					   ?> 
                        <div class="col-sm-9" id="inputContainer">
                            <?php $__currentLoopData = $packages_features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $packages_feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						
                        <input type="text" name="features[]" class="form-control eForm-control mb-1" value="<?php echo e($packages_feature); ?>" placeholder="<?php echo e(get_phrase('Write a new features')); ?>">
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       
                        </div>
                        <div class="col-sm-3 p-0">
                            <button type="button" onclick="appendInput()" class="btn btn-icon feature_btn btn-success"><i class="bi bi-plus"></i></button>
                            <button type="button"  onclick="removeInput()" class="btn btn-icon feature_btn btn-danger"> <i class="bi bi-dash"></i></button>
                        </div>
                    </div>
                </div>
            </div>  
            <?php else: ?>
            <div class="fpb-7">
                <label for="features" class="eForm-label"><?php echo e(get_phrase('Features')); ?></label>
                <div class="new_div">
                    <div class="row">
                        <div class="col-sm-9" id="inputContainer">
                            <input type="text" name="features[]" class="eForm-control form-control" placeholder="<?php echo e(get_phrase('Write Features')); ?>">
                        </div>
                        <div class="col-sm-3 p-0">
                            <button type="button" onclick="appendInput()" class="btn btn-icon feature_btn btn-success"><i class="bi bi-plus"></i></button>
                            <button type="button"  onclick="removeInput()" class="btn btn-icon feature_btn btn-danger"> <i class="bi bi-dash"></i></button>
                        </div>
                    </div>
                </div>
            </div>             
            <?php endif; ?>
            <div class="fpb-7">
                <label for="description" class="eForm-label"><?php echo e(get_phrase('Description')); ?></label>
                <textarea class="form-control eForm-control" id="description" name = "description" rows="2" placeholder="Provide a short description" required><?php echo e($package->description); ?></textarea>
            </div>
            <div class="fpb-7 pt-2">
                <button class="btn-form" type="submit"><?php echo e(get_phrase('Update package')); ?></button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">

    "use strict";
    
    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });

    function togglepackageWiseOptions(interval) {
        if (interval === "life_time") {
            
            $("#days").hide();
            $('#limited').prop('disabled', true);
        }else{
           
            $("#days").show();
            $('#limited').prop('disabled', false);
        }
    }

    $(document).ready(function(){
  $("#unlimitedst").click(function(){
    $(".limitedst").hide();
  });
  $("#limited").click(function(){
    $(".limitedst").show();
    $("#studentLimit").attr('name', 'studentLimit');

  });
});


function appendInput() {
      var container = document.getElementById('inputContainer');
      var newInput = document.createElement('input');
      newInput.setAttribute('type', 'text');
      newInput.setAttribute('placeholder', '<?php echo e(get_phrase('Write service')); ?>');
      newInput.setAttribute('class', 'eForm-control mt-2');
      newInput.setAttribute('name', 'features[]');
      container.appendChild(newInput);
    }

    function removeInput() {
      var container = document.getElementById('inputContainer');
      var inputs = container.getElementsByTagName('input');
      if (inputs.length > 1) {
        container.removeChild(inputs[inputs.length - 1]);
      }
    }

</script><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/superadmin/package/edit_package.blade.php ENDPATH**/ ?>