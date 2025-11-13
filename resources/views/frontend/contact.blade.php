@extends('frontend.index')
@section('content')
    <section class="section-padding">
        <div class="container-xl">
            <div class="title-area text-center">
                <h1 class="mb-2">Contact Us</h1>
                <h3 class="fw-normal">We'd love to hear from you</h3>
                <p class="text-muted">Have questions about admissions, pricing, or features? Get in touch.</p>
            </div>

            <div class="row g-4 mt-4 align-items-stretch">
                <div class="col-lg-5">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <h4 class="mb-3">Contact Information</h4>
                        <p class="text-muted mb-4">Reach us via phone, email, or visit our office.</p>
                        <ul class="list-unstyled d-grid gap-3">
                            <li class="d-flex align-items-start">
                                <span class="me-3 text-primary"><i class="fa-solid fa-phone fa-lg"></i></span>
                                <div>
                                    <div class="fw-semibold">Phone</div>
                                    <a href="tel:+15551234567" class="text-decoration-none">+1 (555) 123‑4567</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-3 text-primary"><i class="fa-solid fa-envelope fa-lg"></i></span>
                                <div>
                                    <div class="fw-semibold">Email</div>
                                    <a href="mailto:info@example.com" class="text-decoration-none">info@example.com</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-3 text-primary"><i class="fa-solid fa-location-dot fa-lg"></i></span>
                                <div>
                                    <div class="fw-semibold">Address</div>
                                    <div>123 School Street, City, Country</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-3 text-primary"><i class="fa-solid fa-clock fa-lg"></i></span>
                                <div>
                                    <div class="fw-semibold">Hours</div>
                                    <div>Mon–Fri, 9:00 AM – 6:00 PM</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <h4 class="mb-3">Send us a message</h4>
                        <form action="mailto:info@example.com" method="post" enctype="text/plain">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="contactName">Your Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                        <input id="contactName" name="name" type="text" class="form-control" placeholder="John Doe" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contactEmail">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-at"></i></span>
                                        <input id="contactEmail" name="email" type="email" class="form-control" placeholder="you@example.com" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="contactSubject">Subject</label>
                                    <input id="contactSubject" name="subject" type="text" class="form-control" placeholder="How can we help?">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="contactMessage">Message</label>
                                    <textarea id="contactMessage" name="message" class="form-control" rows="5" placeholder="Write your message..." required></textarea>
                                </div>
                                <div class="col-12 d-grid d-sm-flex gap-2">
                                    <button type="submit" class="subscribe-btn">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div class="card border-0 shadow-sm">
                    <div class="p-0">
                        <iframe
                            title="Our Location"
                            src="https://maps.google.com/maps?q=New%20York&t=&z=13&ie=UTF8&iwloc=&output=embed"
                            style="width: 100%; height: 360px; border: 0;"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection


