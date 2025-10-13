<?php 

use App\Models\Package;
use App\Models\School;

?>


   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Subscription Report')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Subscriptions')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Subscription Report')); ?></a></li>
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
              <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('superadmin.subscription.filter_report')); ?>">
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
              <?php if(count($subscriptions) > 0): ?>
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
	                	<a class="dropdown-item" id="print" href="javascript:;" onclick="report_print('subscription_report')"><?php echo e(get_phrase('Print')); ?></a>
	                </li>
                </ul>
              </div>
              <?php endif; ?>
            </div>
        	<?php if(count($subscriptions) > 0): ?>
        	<?php $amount = 0; ?>
        	<?php $amount = 0; ?>
			<?php foreach($subscriptions as $subscription):
				$amount = $amount + $subscription->paid_amount;
			endforeach; ?>
        	<div id="subscription_report">
	        	<div class="att-report-banner d-flex justify-content-center justify-content-md-between align-items-center flex-wrap">
		          <div class="att-report-summary order-1">
		            <h4 class="title"><?php echo e(get_phrase('Subscription Report')); ?></h4>
		            <p class="summary-item"><?php echo e(get_phrase('From')); ?>: <span><?php echo e(date('m/d/Y', $date_from)); ?></span></p>
		            <p class="summary-item"><?php echo e(get_phrase('To')); ?>: <span><?php echo e(date('m/d/Y', $date_to)); ?></span></p>
		            <p class="summary-item"><?php echo e(get_phrase('Total amount')); ?>: <span><?php echo e(currency($amount)); ?></span></p>
		          </div>
		          <div class="att-banner-img order-0 order-md-1">
		            <img
		              src="<?php echo e(asset('assets/images/attendance-report-banner.png')); ?>"
		              alt=""
		            />
		          </div>
		        </div>
	        	<div class="table-responsive pb-4">
					<table class="table eTable">
						<thead>
		                    <th>#</th>
		                    <th><?php echo e(get_phrase('School Name')); ?></th>
		                    <th><?php echo e(get_phrase('Package')); ?></th>
		                    <th><?php echo e(get_phrase('Price')); ?></th>
		                    <th><?php echo e(get_phrase('Paid By')); ?></th>
		                    <th><?php echo e(get_phrase('Purchase Date')); ?></th>
		                    <th><?php echo e(get_phrase('Expire Date')); ?></th>
		                </thead>
		                <tbody>
		                	<?php $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		                		<?php 
		                		$package = Package::find($subscription->package_id);
		                		$school = School::find($subscription->school_id);
		                		$offline_tr_keys = json_decode($subscription->transaction_keys);
		                		?>
		                		<tr>
		                			<td><?php echo e($subscriptions->firstItem() + $key); ?></td>
		                			<?php if(empty($school->title)): ?>
									<td><strong>School Removed</strong></td>
									<?php else: ?>
		                			<td><strong><?php echo e($school->title); ?></strong></td>
									<?php endif; ?>
		                			<td><strong><?php echo e($package->name); ?></strong></td>
		                			<td><?php echo e($subscription->paid_amount); ?></td>
		                			<td>
		                				<?php echo e($subscription->payment_method); ?>

		                				<?php if($subscription->payment_method == 'Offline'): ?>
		                				<a href="<?php echo e(asset('assets/uploads/offline_payment/'.$offline_tr_keys->document_file)); ?>" class="btn btn-light-success py-1 px-2 text-14px mt-1" data-bs-toggle="tooltip" title="Document file"><i class="bi bi-file-earmark-richtext-fill"></i></a>
		                				<?php endif; ?>
		                			</td>
		                			<td><?php echo e(date('d-M-Y', $subscription->date_added)); ?></td>
									
									<?php if($subscription->expire_date == '0'): ?>
									<td><?php echo e(get_phrase('Life time')); ?></td>
									<?php else: ?>
									<td><?php echo e(date('d-M-Y', $subscription->expire_date)); ?></td>
									<?php endif; ?>
		                			
		                		</tr>
		                	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		                </tbody>
					</table>
					<?php echo $subscriptions->appends(request()->all())->links(); ?>

				</div>
			</div>
			<?php else: ?>
			<div class="empty_box center">
                <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                <br>
                <?php echo e(get_phrase('No date found')); ?>

            </div>
			<?php endif; ?>
		</div>
	</div>
</div>

<script type="text/javascript">

  	"use strict";

	function download_pdf() {

	    const element = document.getElementById("subscription_report");

	    // clone the element
	    var clonedElement = element.cloneNode(true);

	    // change display of cloned element 
	    $(clonedElement).css("display", "block");

	    // Choose the clonedElement and save the PDF for our user.
		var opt = {
		  margin:       1,
		  filename:     'subscription_report-<?php echo e(date('d-M-Y', $date_from).'-'.date('d-M-Y', $date_to)); ?>.pdf',
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
<?php echo $__env->make('superadmin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/cm-sms-v1/resources/views/superadmin/subscription/subscription_report.blade.php ENDPATH**/ ?>