

<?php $__env->startSection('content'); ?>
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4><?php echo e(get_phrase('Packages')); ?></h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                            <li><a href="#"><?php echo e(get_phrase('Packages')); ?></a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <div class="export-btn-area">
                            <a href="javascript:;" class="export_btn"
                                onclick="rightModal('<?php echo e(route('superadmin.create.package')); ?>', 'Create package')"><i
                                    class="bi bi-plus"></i><?php echo e(get_phrase('Add Package')); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap">

                <ul class="nav nav-tabs eNav-Tabs-custom pt-2" id="myTab" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="approve-tab" data-bs-toggle="tab" data-bs-target="#approvetable"
                            type="button" role="tab" aria-controls="approvetable" aria-selected="false">
                            <?php echo e(get_phrase('Active ')); ?><p class="badge bg-success  ms-1"><?php echo e(count($active_packages)); ?></p>
                            <span></span>
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="decline-tab" data-bs-toggle="tab" data-bs-target="#declinetable"
                            type="button" role="tab" aria-controls="declinetable" aria-selected="false">
                            <?php echo e(get_phrase('Archive ')); ?><p class="badge bg-danger "><?php echo e(count($archive_packages)); ?></p>
                            <span></span>
                        </button>
                    </li>

                </ul>

                <div class="tab-content pb-2" id="nav-tabContent">

                    <div class="tab-pane fade show active" id="approvetable" role="tabpanel" aria-labelledby="approve-tab">

                        <div
                            class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                            <form action="<?php echo e(route('superadmin.package')); ?>">
                                <div class="search-input d-flex justify-content-start align-items-center">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path id="Search_icon" data-name="Search icon"
                                                d="M2,7A4.951,4.951,0,0,1,7,2a4.951,4.951,0,0,1,5,5,4.951,4.951,0,0,1-5,5A4.951,4.951,0,0,1,2,7Zm12.3,8.7a.99.99,0,0,0,1.4-1.4l-3.1-3.1A6.847,6.847,0,0,0,14,7,6.957,6.957,0,0,0,7,0,6.957,6.957,0,0,0,0,7a6.957,6.957,0,0,0,7,7,6.847,6.847,0,0,0,4.2-1.4Z"
                                                fill="#797c8b" />
                                        </svg>
                                    </span>
                                    <input type="text" id="search" name="search" value="<?php echo e($search); ?>"
                                        placeholder="Search Package" class="form-control" />
                                </div>
                            </form>
                            <!-- Export Button -->
                            <?php if(count($active_packages) > 0): ?>
                                <div class="position-relative">
                                    <button class="eBtn-3 dropdown-toggle" type="button" id="defaultDropdown"
                                        data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                        <span class="pr-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77"
                                                viewBox="0 0 10.771 12.31">
                                                <path id="arrow-right-from-bracket-solid"
                                                    d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                                                    transform="translate(0 12.31) rotate(-90)" fill="#00a3ff" />
                                            </svg>
                                        </span>
                                        <?php echo e(get_phrase('Export')); ?>

                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                                        <li>
                                            <a class="dropdown-item" id="pdf" href="javascript:;"
                                                onclick="activeExport()"><?php echo e(get_phrase('PDF')); ?></a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" id="print" href="javascript:;"
                                                onclick="printableDiv('active_package_list')"><?php echo e(get_phrase('Print')); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if(count($active_packages) > 0): ?>
                            <table class="table eTable">
                                <thead>
                                    <th>#</th>
                                    <th><?php echo e(get_phrase('Package')); ?></th>
                                    <th><?php echo e(get_phrase('Price')); ?></th>
                                    <th><?php echo e(get_phrase('Interval')); ?></th>
                                    <th><?php echo e(get_phrase('Preiod')); ?></th>
                                    <th><?php echo e(get_phrase('Student Limit')); ?></th>
                                    <th><?php echo e(get_phrase('Status')); ?></th>
                                    <th class="text-end"><?php echo e(get_phrase('Action')); ?></th>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $active_packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $active_package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($active_packages->firstItem() + $key); ?></td>
                                            <td><strong><?php echo e($active_package->name); ?></strong></td>
                                            <td><?php echo e($active_package->price); ?></td>
                                            <?php if($active_package->interval == 'life_time'): ?>
                                                <td><?php echo e(get_phrase('Life Time')); ?></td>
                                            <?php else: ?>
                                                <td><?php echo e($active_package->interval); ?></td>
                                            <?php endif; ?>
                                            <?php if($active_package->interval == 'life_time'): ?>
                                                <td><?php echo e(get_phrase('Life Time')); ?></td>
                                            <?php else: ?>
                                                <td><?php echo e($active_package->days); ?></td>
                                            <?php endif; ?>

                                            <?php if(empty($active_package->studentLimit)): ?>
                                                <td>---</td>
                                            <?php else: ?>
                                                <td><?php echo e($active_package->studentLimit); ?></td>
                                            <?php endif; ?>

                                            <td>
                                                <?php if ($active_package->status != '1'): ?>
                                                <span class="eBadge ebg-danger"><?php echo e(get_phrase('Archive')); ?></span>
                                                <?php else: ?>
                                                <span class="eBadge ebg-success"><?php echo e(get_phrase('Active')); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-start">
                                                <div class="adminTable-action">
                                                    <button type="button"
                                                        class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <?php echo e(get_phrase('Actions')); ?>

                                                    </button>
                                                    <ul
                                                        class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:;"
                                                                onclick="rightModal('<?php echo e(route('superadmin.edit.package', ['id' => $active_package->id])); ?>', 'Edit Active Package')"><?php echo e(get_phrase('Edit')); ?></a>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <?php echo $active_packages->appends(request()->all())->links(); ?>

                        <?php else: ?>
                            <div class="empty_box center">
                                <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                                <br>
                                <?php echo e(get_phrase('No data found')); ?>

                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="tab-pane fade show " id="declinetable" role="tabpanel" aria-labelledby="decline-tab">

                        <div
                            class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                            <form action="<?php echo e(route('superadmin.package')); ?>">
                                <div class="search-input d-flex justify-content-start align-items-center">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path id="Search_icon" data-name="Search icon"
                                                d="M2,7A4.951,4.951,0,0,1,7,2a4.951,4.951,0,0,1,5,5,4.951,4.951,0,0,1-5,5A4.951,4.951,0,0,1,2,7Zm12.3,8.7a.99.99,0,0,0,1.4-1.4l-3.1-3.1A6.847,6.847,0,0,0,14,7,6.957,6.957,0,0,0,7,0,6.957,6.957,0,0,0,0,7a6.957,6.957,0,0,0,7,7,6.847,6.847,0,0,0,4.2-1.4Z"
                                                fill="#797c8b" />
                                        </svg>
                                    </span>
                                    <input type="text" id="search" name="search" value="<?php echo e($search); ?>"
                                        placeholder="Search Package" class="form-control" />
                                </div>
                            </form>
                            <!-- Export Button -->
                            <?php if(count($archive_packages) > 0): ?>
                                <div class="position-relative">
                                    <button class="eBtn-3 dropdown-toggle" type="button" id="defaultDropdown"
                                        data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                        <span class="pr-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77"
                                                viewBox="0 0 10.771 12.31">
                                                <path id="arrow-right-from-bracket-solid"
                                                    d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                                                    transform="translate(0 12.31) rotate(-90)" fill="#00a3ff" />
                                            </svg>
                                        </span>
                                        <?php echo e(get_phrase('Export')); ?>

                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                                        <li>
                                            <a class="dropdown-item" id="pdf" href="javascript:;"
                                                onclick="archiveExport()"><?php echo e(get_phrase('PDF')); ?></a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" id="print" href="javascript:;"
                                                onclick="printableDiv('active_package_list')"><?php echo e(get_phrase('Print')); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if(count($archive_packages) > 0): ?>
                            <table class="table eTable">
                                <thead>
                                    <th>#</th>
                                    <th><?php echo e(get_phrase('Package')); ?></th>
                                    <th><?php echo e(get_phrase('Price')); ?></th>
                                    <th><?php echo e(get_phrase('Interval')); ?></th>
                                    <th><?php echo e(get_phrase('Preiod')); ?></th>
                                    <th><?php echo e(get_phrase('Student Limit')); ?></th>
                                    <th><?php echo e(get_phrase('Status')); ?></th>
                                    <th class="text-end"><?php echo e(get_phrase('Action')); ?></th>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $archive_packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $archive_package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($archive_packages->firstItem() + $key); ?></td>
                                            <td><strong><?php echo e($archive_package->name); ?></strong></td>
                                            <td><?php echo e($archive_package->price); ?></td>
                                            <?php if($archive_package->package_type == 'life_time'): ?>
                                                <td><?php echo e(get_phrase('Life Time')); ?></td>
                                            <?php else: ?>
                                                <td><?php echo e($archive_package->interval); ?></td>
                                            <?php endif; ?>
                                            <td><?php echo e($archive_package->days); ?></td>
                                            <?php if(empty($archive_package->studentLimit)): ?>
                                                <td>---</td>
                                            <?php else: ?>
                                                <td><?php echo e($archive_package->studentLimit); ?></td>
                                            <?php endif; ?>

                                            <td>
                                                <?php if ($archive_package->status != '1'): ?>
                                                <span class="eBadge ebg-danger"><?php echo e(get_phrase('Archive')); ?></span>
                                                <?php else: ?>
                                                <span class="eBadge ebg-success"><?php echo e(get_phrase('Active')); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-start">
                                                <div class="adminTable-action">
                                                    <button type="button"
                                                        class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <?php echo e(get_phrase('Actions')); ?>

                                                    </button>
                                                    <ul
                                                        class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:;"
                                                                onclick="rightModal('<?php echo e(route('superadmin.edit.package', ['id' => $archive_package->id])); ?>', 'Edit Archive Package')"><?php echo e(get_phrase('Edit')); ?></a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:;"
                                                                onclick="confirmModal('<?php echo e(route('superadmin.package.delete', ['id' => $archive_package->id])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <?php echo $archive_packages->appends(request()->all())->links(); ?>

                        <?php else: ?>
                            <div class="empty_box center">
                                <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                                <br>
                                <?php echo e(get_phrase('No data found')); ?>

                            </div>
                        <?php endif; ?>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php if(count($active_packages) > 0): ?>
        <div class="table-responsive package_list display-none-view" id="active_package_list">
            <h4 class=""
                style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:5%; margin-bottom:15px; margin-top:17px;">
                <?php echo e(get_phrase('Package List')); ?></h4>
            <table class="table eTable">
                <thead>
                    <th>#</th>
                    <th><?php echo e(get_phrase('Package')); ?></th>
                    <th><?php echo e(get_phrase('Price')); ?></th>
                    <th><?php echo e(get_phrase('Interval')); ?></th>
                    <th><?php echo e(get_phrase('Preiod')); ?></th>
                    <th><?php echo e(get_phrase('Status')); ?></th>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $active_packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $active_package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($active_packages->firstItem() + $key); ?></td>
                            <td><strong><?php echo e($active_package->name); ?></strong></td>
                            <td><?php echo e($active_package->price); ?></td>
                            <?php if($active_package->package_type == 'life_time'): ?>
                                <td><?php echo e(get_phrase('Life Time')); ?></td>
                            <?php else: ?>
                                <td><?php echo e($active_package->interval); ?></td>
                            <?php endif; ?>
                            <td><?php echo e($active_package->days); ?></td>
                            <td>
                                <?php if ($active_package->status != '1'): ?>
                                <span class="eBadge ebg-danger"><?php echo e(get_phrase('Archive')); ?></span>
                                <?php else: ?>
                                <span class="eBadge ebg-success"><?php echo e(get_phrase('Active')); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php echo $active_packages->appends(request()->all())->links(); ?>

        </div>
    <?php endif; ?>

    <?php if(count($archive_packages) > 0): ?>
        <div class="table-responsive package_list display-none-view" id="archive_package_list">
            <h4 class=""
                style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:5%; margin-bottom:15px; margin-top:17px;">
                <?php echo e(get_phrase('Package List')); ?></h4>
            <table class="table eTable">
                <thead>
                    <th>#</th>
                    <th><?php echo e(get_phrase('Package')); ?></th>
                    <th><?php echo e(get_phrase('Price')); ?></th>
                    <th><?php echo e(get_phrase('Interval')); ?></th>
                    <th><?php echo e(get_phrase('Preiod')); ?></th>
                    <th><?php echo e(get_phrase('Status')); ?></th>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $archive_packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $archive_package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($archive_packages->firstItem() + $key); ?></td>
                            <td><strong><?php echo e($archive_package->name); ?></strong></td>
                            <td><?php echo e($archive_package->price); ?></td>
                            <?php if($archive_package->package_type == 'life_time'): ?>
                                <td>Life Time</td>
                            <?php else: ?>
                                <td><?php echo e($archive_package->interval); ?></td>
                            <?php endif; ?>
                            <td><?php echo e($archive_package->days); ?></td>
                            <td>
                                <?php if ($archive_package->status != '1'): ?>
                                <span class="eBadge ebg-danger"><?php echo e(get_phrase('Archive')); ?></span>
                                <?php else: ?>
                                <span class="eBadge ebg-success"><?php echo e(get_phrase('Active')); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php echo $archive_packages->appends(request()->all())->links(); ?>

        </div>
    <?php endif; ?>

    <script type="text/javascript">
        "use strict";

        function activeExport() {

            // Choose the element that our invoice is rendered in.
            const element = document.getElementById('active_package_list');

            // clone the element
            var clonedElement = element.cloneNode(true);

            // change display of cloned element
            $(clonedElement).css("display", "block");

            // Choose the clonedElement and save the PDF for our user.
            var opt = {
                margin: 1,
                filename: 'active_package_list_<?php echo e(date('y-m-d')); ?>.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(clonedElement).save();

            // remove cloned element
            clonedElement.remove();
        }

        function archiveExport() {

            // Choose the element that our invoice is rendered in.
            const element = document.getElementById('archive_package_list');

            // clone the element
            var clonedElement = element.cloneNode(true);

            // change display of cloned element
            $(clonedElement).css("display", "block");

            // Choose the clonedElement and save the PDF for our user.
            var opt = {
                margin: 1,
                filename: 'archive_package_list_<?php echo e(date('y-m-d')); ?>.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(clonedElement).save();

            // remove cloned element
            clonedElement.remove();
        }

        function printableDiv(printableAreaDivId) {
            var printContents = document.getElementById(printableAreaDivId).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('superadmin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/superadmin/package/package.blade.php ENDPATH**/ ?>