

<?php $__env->startSection('content'); ?>
<style>
	.packageBox{
		min-width: 0;
	}
	.total_std{
		font-size: 12px;
	}
	.packageFeatures li{
		padding-bottom: 1px;
	}
	.packageFeatures{
		padding-bottom: 15px;
	}
	
</style>
<?php
$subscription = DB::table('subscriptions')->first();

?>
<div class="mainSection-title">
	<div class="row">
	  <div class="col-12">
	    <div
	      class="d-flex justify-content-between align-items-center flex-wrap gr-15"
	    >
	      <div class="d-flex flex-column">
	        <h4><?php echo e(get_phrase('Package List')); ?></h4>
	        <ul class="d-flex align-items-center eBreadcrumb-2">
	          <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
	          <li><a href="#"><?php echo e(get_phrase('Package List')); ?></a></li>
	        </ul>
	      </div>
	    </div>
	  </div>
	</div>
</div>

<!-- Start Package List -->
<div class="row">
	<div class="col-12">
	  <div class="eSection-packageList">
	    <div class="row flex-wrap">
	    <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			
			<div class="col-lg-3 col-md-6">
		        <div class="packageBox text-center">
		          <h4 class="packageTitle"><?php echo e($package->name); ?></h4>
		          <p class="packagePrice">				
					<span><?php echo e(currency($package->price)); ?></span>/
					<?php if($package['interval'] == 'life_time'): ?>
					<?php echo e(get_phrase('life time')); ?>

					<?php else: ?>
		          	<?php if($package['interval'] == 'Days'): ?>
	            		<?php echo e($package['days'].' '.$package['interval']); ?>

	            	<?php else: ?>
	            		<?php echo e($package['interval']); ?>

	            	<?php endif; ?>
					<?php endif; ?>
		          </p>
				  <p class="total_std">Total Students: <?php echo e($package->studentLimit); ?></p>
				  
					 <?php
						$packages_features = json_decode($package->features);
					 ?> 

				  	<ul class="packageFeatures">
						<?php $__currentLoopData = $packages_features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $packages_feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<li class="mainFeature"><?php echo e($packages_feature); ?></li>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		            	
		          	</ul>
					  <p class="total_std mb-2"><?php echo e($package->description); ?></p>
				  <a href="<?php echo e(route('admin.subscription.payment',['package_id'=>$package['id'] ])); ?>" class="packageSubs_btn"><?php echo e(get_phrase('Subscribe')); ?></a>		          
		        </div>
		    </div>

		    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
	    </div>
	  </div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/subscription/purchase.blade.php ENDPATH**/ ?>