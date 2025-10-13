
   
<?php $__env->startSection('content'); ?>

<?php
use App\Models\School; 
use App\Models\Subscription;


$subscription = Subscription::latest()->first();

    
?>


<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Pending Request')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Subscriptions')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Pending Request')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
        	<div class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15 pb-4">
              <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('superadmin.subscription.filter_pending')); ?>">
              	<?php echo csrf_field(); ?>
              	<div class="d-flex justify-content-start align-items-center row">
	                <div class="col-xl-8">
	                  <input type="text" class="form-control eForm-control" name="eDateRange" value="<?php echo e(date('m/d/Y', $date_from).' - '.date('m/d/Y', $date_to)); ?>" />
	                </div>
	                <div class="col-xl-2">
	                	<button type="submit" class="eBtn eBtn btn-secondary form-control"><?php echo e(get_phrase('Filter')); ?></button>
	                </div>
                </div>
              </form>
              <!-- Export Button -->
              <?php if(count($payment_histories) > 0): ?>
              <div class="position-relative">
                <button
                  class="eBtn-3 dropdown-toggle"
                  type="button"
                  id="defaultDropdown"
                  data-bs-toggle="dropdown"
                  data-bs-auto-close="true"
                  aria-expanded="false"
                >
                  <span class="pr-10">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="12.31"
                      height="10.77"
                      viewBox="0 0 10.771 12.31"
                    >
                      <path
                        id="arrow-right-from-bracket-solid"
                        d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                        transform="translate(0 12.31) rotate(-90)"
                        fill="#00a3ff"
                      />
                    </svg>
                  </span>
                  <?php echo e(get_phrase('Export')); ?>

                </button>
                <ul
                  class="dropdown-menu dropdown-menu-end eDropdown-menu-2"
                >
	                <li>
	                 	<a class="dropdown-item" id="pdf" href="javascript:;" onclick="download_pdf()"><?php echo e(get_phrase('PDF')); ?></a>
	                </li>
	                <li>
	                	<a class="dropdown-item" id="print" href="javascript:;" onclick="report_print('subscription_pending_report')"><?php echo e(get_phrase('Print')); ?></a>
	                </li>
                </ul>
              </div>
              <?php endif; ?>
            </div>
			<?php if(count($payment_histories) > 0): ?>
			<?php $amount = 0; ?>
			<?php foreach($payment_histories as $payment_history):
				$amount = $amount + $payment_history->amount;

				if (!empty($subscription)) {
					$active_status = $subscription->active;

					$paymentAmount = $payment_history->amount ;

					$subscriptionsPrice = $subscription->paid_amount;

					$upgradePrice = $paymentAmount - $subscriptionsPrice;
				}				
					
				 
			endforeach; ?>
			<div class="att-report-banner d-flex justify-content-center justify-content-md-between align-items-center flex-wrap">
		        <div class="att-report-summary order-1">
		            <h4 class="title"><?php echo e(get_phrase('Subscription Report')); ?></h4>
		            <p class="summary-item"><?php echo e(get_phrase('From')); ?>: <span><?php echo e(date('m/d/Y', $date_from)); ?></span></p>
		            <p class="summary-item"><?php echo e(get_phrase('To')); ?>: <span><?php echo e(date('m/d/Y', $date_to)); ?></span></p>
		            <p class="summary-item"><?php echo e(get_phrase('Total amount')); ?>: <span> 
					<?php if(!empty($subscription)): ?> 
					<?php if($active_status == '1'): ?> 
						<?php echo e(currency($upgradePrice)); ?>

					<?php endif; ?>
					<?php else: ?>
						<?php echo e(currency($amount)); ?>

					<?php endif; ?>
					</span></p>
		          </div>
		          <div class="att-banner-img order-0 order-md-1">
		            <img
		              src="<?php echo e(asset('assets/images/attendance-report-banner.png')); ?>"
		              alt=""
		            />
		        </div>
	        </div>
	        <div class="table-responsive tScrollFix pb-2">
				<table class="table eTable">
					<thead>
	                    <th>#</th>
	                    <th><?php echo e(get_phrase('School Name')); ?></th>
	                    <th><?php echo e(get_phrase('Price')); ?></th>
	                    <th><?php echo e(get_phrase('Payment For')); ?></th>
	                    <th><?php echo e(get_phrase('Payment Document')); ?></th>
	                    <th><?php echo e(get_phrase('Status')); ?></th>
	                    <th class="text-center"><?php echo e(get_phrase('Action')); ?></th>
	                </thead>
	                <tbody>
	                	<?php $__currentLoopData = $payment_histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $payment_history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	                	<?php $school = School::find($payment_history->school_id); ?>
	                		<tr>
	                			<td><?php echo e($payment_histories->firstItem() + $key); ?></td>
	                			<td><strong><?php echo e($school->title); ?></strong></td>
	                			<td>
									<?php if(!empty($subscription)): ?> 
									<?php if($active_status == '1'): ?> 
									<?php echo e(($upgradePrice)); ?>

									<?php endif; ?>
									<?php else: ?>
									<?php echo e(($amount)); ?>

									<?php endif; ?></td>
	                			<td><?php echo e(ucwords($payment_history->payment_type)); ?></td>
	                			<td class="link" >
									<?php if(!empty($payment_history->document_image)): ?>
										<?php
											$fileExtension = pathinfo($payment_history->document_image, PATHINFO_EXTENSION);
											$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
											
										?>
											

										<?php if(in_array(strtolower($fileExtension), $allowedExtensions)): ?>
											
											<a href="<?php echo e(asset('assets/uploads/offline_payment/'.$payment_history->document_image)); ?>" download 	data-lightbox="models" data-title="<?php echo e($payment_history->document_image); ?>" > <strong><?php echo e($payment_history->document_image); ?> </strong>
											
										<?php else: ?>
											<a href="<?php echo e(asset('assets/uploads/offline_payment/'.$payment_history->document_image)); ?>" download  data-title="<?php echo e($payment_history->document_image); ?>" > <strong><?php echo e($payment_history->document_image); ?> </strong>
										<?php endif; ?>
									<?php endif; ?>
	                							
									</a>	
	                			</td>
	                			<td>
	                				<span class="eBadge ebg-info"><?php echo e(get_phrase('Pending')); ?></span>
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
	                                        <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('superadmin.subscription.status', ['status' => 'approve', 'id' => $payment_history->id])); ?>', 'undefined');"><?php echo e(get_phrase('Approve')); ?></a>
	                                      </li>
	                                      <li>
	                                        <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('superadmin.subscription.status', ['status' => 'suspended', 'id' => $payment_history->id])); ?>', 'undefined');"><?php echo e(get_phrase('Suspended')); ?></a>
	                                      </li>
	                                      <li>
	                                        <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('superadmin.subscription.delete', ['id' => $payment_history->id])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
	                                      </li>
	                                    </ul>
	                                </div>
			                    </td>
								<div id="FullImageView">

								</div>
	                		</tr>
	                	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	                </tbody>
				</table>
				<?php echo $payment_histories->appends(request()->all())->links(); ?>

			</div>
			<?php else: ?>
				<div class="empty_box center">
                    <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                    <br>
                </div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php if(count($payment_histories) > 0): ?>
<div class="subscription_pending_report display-none-view" id="subscription_pending_report">
	<div class="att-report-banner d-flex justify-content-center justify-content-md-between align-items-center flex-wrap">
      <div class="att-report-summary order-1">
        <h4 class="title"><?php echo e(get_phrase('Subscription Report')); ?></h4>
        <p class="summary-item"><?php echo e(get_phrase('From')); ?>: <span><?php echo e(date('m/d/Y', $date_from)); ?></span></p>
        <p class="summary-item"><?php echo e(get_phrase('To')); ?>: <span><?php echo e(date('m/d/Y', $date_to)); ?></span></p>
      </div>
      <div class="att-banner-img order-0 order-md-1">
        <img
          src="<?php echo e(asset('assets/images/attendance-report-banner.png')); ?>"
          alt=""
        />
      </div>
    </div>
	<div class="table-responsive">">
		<table class="table eTable">
			<thead>
	            <th>#</th>
	            <th><?php echo e(get_phrase('School Name')); ?></th>
	            <th><?php echo e(get_phrase('Price')); ?></th>
	            <th><?php echo e(get_phrase('Payment For')); ?></th>
	            <th><?php echo e(get_phrase('Payment Document')); ?></th>
	            <th><?php echo e(get_phrase('Status')); ?></th>
	        </thead>
	        <tbody>
	        	<?php $__currentLoopData = $payment_histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $payment_history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	        	<?php $school = School::find($payment_history->school_id); ?>
	        		<tr>
	        			<td><?php echo e($payment_histories->firstItem() + $key); ?></td>
	        			<td><strong><?php echo e($school->title); ?></strong></td>
	        			<td><?php echo e($payment_history->amount); ?></td>
	        			<td><?php echo e(ucwords($payment_history->payment_type)); ?></td>
	        			<td class="link">
	        					<a href="<?php echo e(asset('assets/uploads/offline_payment/'.$payment_history->document_image)); ?>" download> <strong><?php echo e($payment_history->document_image); ?> </strong></a>
	        			</td>
	        			<td>
	        				<span class="eBadge ebg-info"><?php echo e(get_phrase('Pending')); ?></span>
	        			</td>
	        		</tr>
	        	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	        </tbody>
		</table>
		<?php echo $payment_histories->appends(request()->all())->links(); ?>

	</div>
</div>
<?php endif; ?>

<script type="text/javascript">

  	"use strict";

		//   function FullView(ImgLink){
		// 	alert(ImgLink);
		// }

	function download_pdf() {

	    const element = document.getElementById("subscription_pending_report");

	    // clone the element
	    var clonedElement = element.cloneNode(true);

	    // change display of cloned element 
	    $(clonedElement).css("display", "block");

	    // Choose the clonedElement and save the PDF for our user.
		var opt = {
		  margin:       1,
		  filename:     'subscription_pending_report-<?php echo e(date('d-M-Y', $date_from).'-'.date('d-M-Y', $date_to)); ?>.pdf',
		  image:        { type: 'jpeg', quality: 0.98 },
		  html2canvas:  { scale: 2 },
		};

		// New Promise-based usage:
		html2pdf().set(opt).from(clonedElement).save();

	    // remove cloned element
	    clonedElement.remove();
	}

	function report_print(printableAreaDivId) {
		var printContents = document.getElementById(printableAreaDivId).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('superadmin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/superadmin/subscription/pending.blade.php ENDPATH**/ ?>