<?php $__env->startSection('content'); ?>
    
    <style>
        .service-icon i {
            font-size: 24px;
            font-weight: bold;
            margin-left: 10px;
            margin-top: 10px;
            color: var(--secondary-color);
        }
    </style>
    <!--  Header Area Start -->
    <header class="header-area">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col-lg-2 col-md-6 col-sm-6 col-5">
                    <!-- Logo  -->
                    <div class="logo">
                        <a href="#"><img src="<?php echo e(asset('assets/uploads/logo/logoUpdated.svg')); ?>" alt="Logo"></a>
                    </div>
                </div>
                <div class="col-lg-7 col-md-6 menu-items">
                    <!-- Menu -->
                    <nav class="header-menu">
                        <ul class="primary-menu d-flex justify-content-center">
                            <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="#feature">Features</a></li>
                            <li class="nav-item"><a class="nav-link" href="#price">Pricing</a></li>
                            <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                            <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                        </ul>
                    </nav>
                    <a class="small-device-show" href="#"><img src="/frontend/assets/image/logo.png"
                            alt="logo"></a>
                    <span class="crose-icon"><i class="fa-solid fa-xmark"></i></span>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-7">
                    <!-- Button Area -->
                    <div class="header-btn">
                        <a class="login-btn" href="#login">Login</a>
                        <a class="signUp-btn" href="#register" data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop">Register</a>
                        <span class="hambargar-bar"><i class="fa-solid fa-bars"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!--  Header Area End   -->
    <!-- Register Form Modal Start -->
    <div class="register-form-modal">
        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5 text-center" id="staticBackdropLabel">
                            <?php echo e(get_phrase('School Register Form')); ?></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="schoolReg" enctype="multipart/form-data" class="d-block ajaxForm"
                            action="<?php echo e(route('school.create')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="reg-modal-form">
                                        <h4><?php echo e(get_phrase('SCHOOL INFO')); ?></h4>
                                        <div class="reg-form-group">
                                            <div class="single-form">
                                                <label for="school_name"><?php echo e(get_phrase('School Name')); ?></label>
                                                <input id="school_name" name="school_name" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="single-form">
                                                <label for="school_address"><?php echo e(get_phrase('School Address')); ?></label>
                                                <input id="school_address" name="school_address" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="single-form">
                                                <label for="school_email"><?php echo e(get_phrase('School Email')); ?></label>
                                                <input id="school_email" name="school_email" type="email"
                                                    class="form-control" required>
                                            </div>
                                            <div class="single-form">
                                                <label for="school_phone"><?php echo e(get_phrase('School Phone')); ?></label>
                                                <input id="school_phone" name="school_phone" type="tel"
                                                    class="form-control" required>
                                            </div>
                                            <div class="single-form">
                                                <label for="school_info"><?php echo e(get_phrase('School info')); ?></label>
                                                <textarea name="school_info" id="school_info" class="form-control" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="reg-modal-form">
                                        <h4><?php echo e(get_phrase('ADMIN INFO')); ?></h4>
                                        <div class="reg-form-group">
                                            <div class="single-form">
                                                <label for="admin_name"><?php echo e(get_phrase('Admin Name')); ?></label>
                                                <input id="admin_name" name="admin_name" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="single-form">
                                                <label for="gender"><?php echo e(get_phrase('Gender')); ?></label>
                                                <select class="form-select" id="gender" name="gender" required>
                                                    <option value=""><?php echo e(get_phrase('Select a gender')); ?></option>
                                                    <option value="Male"><?php echo e(get_phrase('Male')); ?></option>
                                                    <option value="Female"><?php echo e(get_phrase('Female')); ?></option>
                                                </select>
                                            </div>
                                            <div class="single-form">
                                                <label for="blood_group"><?php echo e(get_phrase('Blood group')); ?></label>
                                                <select class="form-select" id="blood_group" name="blood_group" required>
                                                    <option value=""><?php echo e(get_phrase('Select a blood group')); ?>

                                                    </option>
                                                    <option value="a+"><?php echo e(get_phrase('A+')); ?></option>
                                                    <option value="a-"><?php echo e(get_phrase('A-')); ?></option>
                                                    <option value="b+"><?php echo e(get_phrase('B+')); ?></option>
                                                    <option value="b-"><?php echo e(get_phrase('B-')); ?></option>
                                                    <option value="ab+"><?php echo e(get_phrase('AB+')); ?></option>
                                                    <option value="ab-"><?php echo e(get_phrase('AB-')); ?></option>
                                                    <option value="o+"><?php echo e(get_phrase('O+')); ?></option>
                                                    <option value="o-"><?php echo e(get_phrase('O-')); ?></option>
                                                </select>
                                            </div>
                                            <div class="single-form">
                                                <label for="admin_address"><?php echo e(get_phrase('Admin Address')); ?></label>
                                                <input id="admin_address" name="admin_address" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="single-form">
                                                <label for="admin_phone"><?php echo e(get_phrase('Admin Phone Number')); ?></label>
                                                <input id="admin_phone" name="admin_phone" type="tel"
                                                    class="form-control" required>
                                            </div>
                                            <div class="single-form">
                                                <label for="photo"><?php echo e(get_phrase('Photo')); ?></label>
                                                <input class="form-control" type="file" accept="image/*"
                                                    id="photo" name="photo">
                                            </div>
                                            <div class="single-form">
                                                <label for="admin_email"><?php echo e(get_phrase('Admin Email')); ?></label>
                                                <input id="admin_email" name="admin_email" type="email"
                                                    class="form-control" required>
                                            </div>
                                            <div class="single-form">
                                                <label for="admin_password"><?php echo e(get_phrase('Admin Password')); ?></label>
                                                <input id="admin_password" name="admin_password" type="password"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(get_settings('recaptcha_switch_value') == 'Yes'): ?>
                                        <button class="g-recaptcha m-submit-btn"
                                            data-sitekey="<?php echo e(get_settings('recaptcha_site_key')); ?>"
                                            data-callback='onSubmit' data-action='submit'
                                            type="submit"><?php echo e(get_phrase('Submit')); ?></button>
                                    <?php else: ?>
                                        <button class=" m-submit-btn" type="submit"><?php echo e(get_phrase('Submit')); ?></button>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Register Form Modal End -->
    <!--  Bannar Area Start  -->
    <section class="bannar-area">
        <!-- Safe -->
        <span class="safe-top"><img src="<?php echo e(asset('frontend/assets/image/safe-2.png')); ?>" alt="img"></span>
        <span class="safe-left"><img src="<?php echo e(asset('frontend/assets/image/safe-1.png')); ?>" alt="img"></span>
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-6">
                    <!-- Bannar Content -->
                    <div class="bannar-content">
                        
                        <h2>All‑in‑one School</h2>
                        <h3>Management. Simple.</h3>
                        <h3>Powerful. Scalable.</h3>
                        <p>From admissions to attendance, fees to gradebooks — run your entire institute in one secure
                            platform.</p>
                        <div class="d-flex align-items-center gap-2 mb-3 p-3">
                            <a class="signUp-btn" href="#" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop">Start Free Trial</a>
                        </div>
                        <ul class="d-flex flex-column flex-wrap gap-3 list-unstyled text-start pt-2" >
                            <li><i class="fa-solid fa-check"></i> Trusted by schools worldwide</li>
                            <li><i class="fa-solid fa-check"></i> Fast setup</li>
                            <li><i class="fa-solid fa-check"></i> No credit card required</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bananr-right-img">
                        <img src="<?php echo e(asset('frontend/assets/image/bannar-image.png')); ?>" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  Bannar Area End   -->
    <!--  Value Props Start  -->
    <section class="section-padding">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-3 col-sm-6 mb-3">
                    <h4><?php echo e(get_phrase('Save time')); ?></h4>
                    <p><?php echo e(get_phrase('Automate routine academic and admin work.')); ?></p>
                </div>
                <div class="col-lg-3 col-sm-6 mb-3">
                    <h4><?php echo e(get_phrase('Grow confidently')); ?></h4>
                    <p><?php echo e(get_phrase('Multi‑school, multi‑role, multi‑language SaaS.')); ?></p>
                </div>
                <div class="col-lg-3 col-sm-6 mb-3">
                    <h4><?php echo e(get_phrase('Get paid faster')); ?></h4>
                    <p><?php echo e(get_phrase('Integrated online and offline fee collection.')); ?></p>
                </div>
                <div class="col-lg-3 col-sm-6 mb-3">
                    <h4><?php echo e(get_phrase('Stay in sync')); ?></h4>
                    <p><?php echo e(get_phrase('Real‑time dashboards, reports, and notifications.')); ?></p>
                </div>
            </div>
        </div>
    </section>
    <!--  Value Props End  -->
    <!--  Roles Section Start  -->
    <section class="section-padding" id="roles">
        <div class="container-xl">
            <div class="title-area">
                <h1>A Single Platform, Purpose-Built for Every Role</h1>
                <h3>We provide dedicated portals and tools for every member of your school community, all secured with
                    granular permissions.</h3>
            </div>
            <div class="row mt-5 pt-3 z-10 position-relative">
                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-user-shield"></i></div>
                        <div class="service-text">
                            <h3>For Administrators</h3>
                            <p>Manage multiple branches, control finances, customize system settings, and get a 360-degree
                                view of your entire institution.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-chalkboard-teacher"></i></div>
                        <div class="service-text">
                            <h3>For Teachers</h3>
                            <p>Take daily attendance, manage class timetables, enter marks, upload syllabi, and communicate
                                with parents and students effortlessly.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                        <div class="service-text">
                            <h3>For Accountants</h3>
                            <p>Track all student fees, manage online and offline payments, generate invoices, and log all
                                school expenses in one place.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-user-group"></i></div>
                        <div class="service-text">
                            <h3>For Parents</h3>
                            <p>Access your child's profile, check grades, view the noticeboard, communicate with teachers,
                                and pay fees online through a secure portal.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                        <div class="service-text">
                            <h3>For Students</h3>
                            <p>View your class routine, download syllabi, check your marks, and see upcoming events and
                                notices.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-book"></i></div>
                        <div class="service-text">
                            <h3>For Librarians & Staff</h3>
                            <p>A dedicated module to manage your entire book catalog, track issued books, and handle
                                returns.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  Roles Section End  -->
    <!--  Features Area Start  -->
    <section class="service-area section-padding" id="feature">
        <div class="container">
            <!-- Title  -->
            <div class="title-area">
                <h1>Features That Power Your School</h1>
                <h3>From the front office to the classroom, we've got you covered.</h3>
            </div>

            <div class="row mt-5 pt-3">
                <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-id-card"></i></div>
                        <div class="service-text">
                            <h3>Student Admissions & Records</h3>
                            <p>Go paperless from day one. Manage new admissions, import students in bulk (CSV/Excel), and
                                maintain detailed digital profiles with document uploads and custom ID card generation.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-school"></i></div>
                        <div class="service-text">
                            <h3>Academic Management</h3>
                            <p>Build your entire academic structure. Create classes, sections, and subjects. Design and
                                manage daily class timetables (routines) and easily promote students to the next session.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-file-signature"></i></div>
                        <div class="service-text">
                            <h3>Exams & Gradebook</h3>
                            <p>Simplify assessments. Create unlimited exam categories, enter marks, and let the system
                                calculate grades based on your rules. Export professional PDF gradebooks.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-credit-card"></i></div>
                        <div class="service-text">
                            <h3>Online & Offline Fee Management</h3>
                            <p>Generate invoices, track pending payments, and accept money online via Stripe, PayPal,
                                Razorpay, and Paytm. Manage offline payments with an approval workflow.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                        <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-bullhorn"></i></div>
                        <div class="service-text">
                            <h3>Community & Communication</h3>
                            <p>Keep everyone in the loop. Post to the noticeboard, create event calendars, and manage
                                internal messaging between staff, students, and parents.</p>
                        </div>
                    </div>
                            </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-book-open"></i></div>
                            <div class="service-text">
                            <h3>Library & Resource Management</h3>
                            <p>Digitize your library. Catalog books, track issue/return dates, and manage your entire
                                inventory with ease.</p>
                        </div>
                    </div>
                            </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-layer-group"></i></div>
                        <div class="service-text">
                            <h3>Multi-School & Multi-Language</h3>
                            <p>Manage multiple branches from a single Super Admin account. Users can select their preferred
                                language for a familiar experience.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                    <div class="service-items">
                        <div class="service-icon"><i class="fa-solid fa-gear"></i></div>
                        <div class="service-text">
                            <h3>Website & System Settings</h3>
                            <p>Make it yours. Control the landing page, update FAQs, and set logo, branding, currency, and
                                SMTP email settings from the admin panel.</p>
                        </div>
            </div>
                </div>
            </div>
        </div>
    </section>
    <!--  Features Area End   -->
    <!--  Pricing Area Start   -->
    <section class="pricing-area section-padding" id="price">
        <div class="container-xl">
            <!-- Title  -->
            <div class="title-area">
                <h1>Pricing</h1>
                <h3>Straightforward pricing for every school size</h3>
                <p>Choose the plan that fits your needs. All plans are flexible and include powerful features to get you
                    started. No hidden fees. No surprises.</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3 d-flex">
                    <div class="pricing-table h-100 w-100 d-flex flex-column justify-content-between">
                        <span class="trail-price">Starter</span>
                        <h4>$49<span class="small-text">/month</span></h4>
                        <p class="color-ff">Up to 300 students</p>
                        <ul class="pricing-item" style="border-top:0px;">
                            <li class="color-ff">Admissions & student records</li>
                            <li class="color-ff">Classes, sections, subjects</li>
                            <li class="color-ff">Attendance & basic reports</li>
                            <li class="color-ff">Email support</li>
                        </ul>
                        <a href="#" class="subscribe-btn">Choose plan</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3 d-flex">
                    <div class="pricing-table h-100 w-100 d-flex flex-column justify-content-between">
                        <span class="trail-price">Professional</span>
                        <h4>$99<span class="small-text">/month</span></h4>
                        <p class="color-ff">Up to 1,000 students</p>
                            <ul class="pricing-item" style="border-top:0px;">
                            <li class="color-ff">Everything in Starter</li>
                            <li class="color-ff">Exams & gradebook PDFs</li>
                            <li class="color-ff">Online payments (Stripe/PayPal/Razorpay/Paytm)</li>
                            <li class="color-ff">Noticeboard, events, messaging</li>
                            </ul>
                        <a href="#" class="subscribe-btn">Choose plan</a>
                    </div>
                        </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3 d-flex">
                    <div class="pricing-table h-100 w-100 d-flex flex-column justify-content-between">
                        <span class="trail-price">Enterprise</span>
                        <h4>Contact<span class="small-text">/custom</span></h4>
                        <p class="color-ff">Unlimited students & branches</p>
                        <ul class="pricing-item" style="border-top:0px;">
                            <li class="color-ff">Everything in Professional</li>
                            <li class="color-ff">Multi‑school management</li>
                            <li class="color-ff">Advanced reporting</li>
                            <li class="color-ff">Dedicated support & SLA</li>
                        </ul>
                        <a href="#" class="subscribe-btn">Contact sales</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  Pricing Area End   -->
    <!--  Trust / Compliance Strip Start  -->
    <section class="section-padding">
        <div class="container-xl">
            <ul class="d-flex flex-wrap justify-content-center gap-3" style="list-style:none; padding-left:0;">
                <li><?php echo e(get_phrase('Role‑based access')); ?></li>
                <li>•</li>
                <li><?php echo e(get_phrase('Data privacy first')); ?></li>
                <li>•</li>
                <li><?php echo e(get_phrase('Reliable performance')); ?></li>
                <li>•</li>
                <li><?php echo e(get_phrase('Multilingual')); ?></li>
            </ul>
        </div>
    </section>
    <!--  Trust / Compliance Strip End  -->
    <!--  Faq  Area Start   -->
    <section class="faq-area" id="faq">
        <div class="container-xl">
            <!-- Title  -->
            <div class="title-area">
                <h1>Have Any Questions?</h1>
                <h3>We're here to help.</h3>
                <p>Answers about multi‑school, payments, access, languages, and subscriptions.</p>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="accordion-area">
                        <div class="accordion" id="accordionFaq">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading1">
                                    <button class="accordion-button collapsed round-bg" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">Can I manage multiple school branches with one account?</button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="heading1" data-bs-parent="#accordionFaq">
                                    <div class="accordion-body">
                                        <p>Yes! Our platform is a true multi‑school SaaS. The Super Admin can add, manage, and oversee all school branches from a single, powerful dashboard.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading2">
                                    <button class="accordion-button collapsed round-bg" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">What payment gateways do you support for fee collection?</button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#accordionFaq">
                                    <div class="accordion-body">
                                        <p>We support Stripe, PayPal, Razorpay, and Paytm. We also offer offline payments where parents can upload proof for your accountant to approve.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading3">
                                    <button class="accordion-button collapsed round-bg" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">Can parents and students access the system?</button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#accordionFaq">
                                    <div class="accordion-body">
                                        <p>Absolutely. We provide secure portals for parents and students to check profiles, view grades, download syllabi, see notices, and pay fees online.</p>
                                    </div>
                                </div>
                            </div>
                                <div class="accordion-item">
                                <h2 class="accordion-header" id="heading4">
                                    <button class="accordion-button collapsed round-bg" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">Is the system available in different languages?</button>
                                    </h2>
                                <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#accordionFaq">
                                        <div class="accordion-body">
                                        <p>Yes. The platform supports multiple languages, and each user can select their preferred language for their dashboard.</p>
                                    </div>
                                </div>
                                        </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading5">
                                    <button class="accordion-button collapsed round-bg" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">How does the subscription and trial work?</button>
                                </h2>
                                <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#accordionFaq">
                                    <div class="accordion-body">
                                        <p>You can start with a free trial. When ready, purchase a monthly, yearly, or lifetime package from your dashboard. We support both online and offline subscription payments.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  faq Area End   -->
    <!--  Cntact  Area Start  -->
    <section class="contact-us-area" id="contact">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-12">
                    <div class="lan-contact">
                        <div class="contact-left text-center">
                            <h3>Contact us with any questions</h3>
                            <p>We'll get back to you as soon as we can.</p>
                            <a class="contact-us-btn mt-4" href="<?php echo e(route('contact')); ?>"><i
                                    class="fa-solid fa-envelope"></i> Contact Us</a>
                        </div>
                        <div class="contact-right">
                            <div class="envolepe-messeage">
                                <img src="<?php echo e(asset('frontend/assets/image/envelope.png')); ?>" alt="image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  Contact Area End   -->
    <!-- Footer Area Start -->
    <footer class="footer-area">
        <!-- footer Top Area -->
        <div class="footer-top">
            <div class="container-xl">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-5">
                        <div class="footer-items">
                            <div class="footer-logo">
                                <a href="#"><img src="<?php echo e(asset('assets/uploads/logo/LogoLight.svg')); ?>"
                                        alt="image"></a>
                            </div>
                            <p>Empowering schools with modern, secure, and efficient management.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-5">
                        <div class="contacts footer-items">
                            <h4>Contact</h4>
                            <ul class="ad-contacts">
                                <li><a href="tel:+15551234567"><i class="fa-solid fa-phone"></i> +1 (555) 123‑4567</a></li>
                                <li><a href="mailto:info@example.com"><i class="fa-solid fa-envelope"></i> info@example.com</a></li>
                                <li><span><i class="fa-solid fa-location-dot"></i></span>
                                    <p>123 School Street, City, Country</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-5">
                        <div class="addons footer-items">
                            <h4>Social Links</h4>
                            <ul class="footer-social">
                                <li><a href="#" title="Facebook" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
                                <li><a href="#" title="Twitter" target="_blank"><i class="fa-brands fa-twitter"></i></a></li>
                                <li><a href="#" title="LinkedIn" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                <li><a href="#" title="Instagram" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
                            </ul>
                        </div>


                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="copyright-text">
                    <p>© 2025 CM School Manager. All rights reserved.</p>
                </div>
            </div>
    </footer>
    <!-- Footer Area End -->

    <style>
        #toast-container>.toast-warning {
            font-size: 15px;
        }
    </style>

    <script type="text/javascript">
        // JavaScript to handle language selection
        document.addEventListener('DOMContentLoaded', function() {
            let languageLinks = document.querySelectorAll('.language-item');

            languageLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    let languageName = this.getAttribute('data-language-name');
                    document.getElementById('selectedLanguageName').value = languageName;
                    document.getElementById('languageForm').submit();
                });
            });
        });
        "use strict";

        function subscription_warning(roleId) {
            if (roleId == 1) {
                toastr.warning("You can't subscribe as superadmin");
            } else if (roleId == 2) {
                toastr.warning("Your school is already subscribed to a package.");
            } else {
                toastr.warning("You are not authorized! Please login as school admin.");
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var seeBtn = document.getElementById('see-btn');
            if (seeBtn) {
                seeBtn.addEventListener('click', function() {
                    var currentUrl = new URL(window.location.href);
                    var seeAll = currentUrl.searchParams.get('see_all');

                    if (seeAll) {
                        currentUrl.searchParams.delete('see_all');
                    } else {
                        currentUrl.searchParams.set('see_all', true);
                    }

                    window.location.href = currentUrl.toString();
                });
            }
        });

        function onSubmit(token) {
            document.getElementById("schoolReg").submit();
        }
    </script>

    <script src="https://www.google.com/recaptcha/api.js"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/cm-sms-v1/resources/views/frontend/landing_page_new.blade.php ENDPATH**/ ?>