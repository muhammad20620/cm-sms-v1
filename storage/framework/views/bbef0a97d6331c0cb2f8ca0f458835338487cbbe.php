<?php $__env->startSection('content'); ?>
    <section class="section-padding">
        <div class="container-xl">
            <div class="title-area text-center">
                <h1>Contact Us</h1>
                <h3>We'd love to hear from you</h3>
                <p>Have questions about admissions, pricing, or features? Get in touch.</p>
            </div>
            <div class="row justify-content-center mt-4">
                <div class="col-lg-6 col-md-8">
                    <div class="card p-4">
                        <h4 class="mb-3">Send us a message</h4>
                        <form action="mailto:info@example.com" method="post" enctype="text/plain">
                            <div class="mb-3">
                                <label class="form-label">Your Name</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="subscribe-btn">Send</button>
                        </form>
                    </div>
                    <div class="mt-4">
                        <h5>Or reach us directly</h5>
                        <ul class="list-unstyled">
                            <li><i class="fa-solid fa-phone"></i> +1 (555) 123‑4567</li>
                            <li><i class="fa-solid fa-envelope"></i> info@example.com</li>
                            <li><i class="fa-solid fa-location-dot"></i> 123 School Street, City, Country</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('frontend.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/cm-sms-v1/resources/views/frontend/contact.blade.php ENDPATH**/ ?>