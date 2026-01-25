<?php
$pageTitle = 'NearBy | Student Housing Simplified';
$pageScripts = ['assets/js/message.js'];
$enableChatbot = true;
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero-slider position-relative mb-5">
    <div id="homepageSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#homepageSlider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#homepageSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#homepageSlider" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="3000">
                <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1460&q=80" class="slider-image" alt="Student housing near green campus">
                <div class="slider-overlay"></div>
                <div class="slider-content d-flex align-items-center">
                    <div class="container text-center text-lg-start">
                        <span class="badge bg-success bg-opacity-75 text-white mb-3">Verified Senior Listings</span>
                        <h2 class="display-6 fw-semibold mb-3">Live close to friends, class, and campus buzz</h2>
                        <p class="text-white-50 mb-0">Browse curated homes owned by seniors who understand student life.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="3000">
                <img src="https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1460&q=80" class="slider-image" alt="Students studying in lounge">
                <div class="slider-overlay"></div>
                <div class="slider-content d-flex align-items-center">
                    <div class="container text-center text-lg-start">
                        <span class="badge bg-light text-success mb-3">Smart Filters</span>
                        <h2 class="display-6 fw-semibold mb-3">Compare rent, facilities, and locations instantly</h2>
                        <p class="text-white-50 mb-0">Use advanced filters to find spaces with Wi-Fi, meals, and more.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="3000">
                <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1460&q=80" class="slider-image" alt="Students meeting community">
                <div class="slider-overlay"></div>
                <div class="slider-content d-flex align-items-center">
                    <div class="container text-center text-lg-start">
                        <span class="badge bg-warning text-dark mb-3">Community First</span>
                        <h2 class="display-6 fw-semibold mb-3">Connect with seniors and settle in confidently</h2>
                        <p class="text-white-50 mb-0">Get guidance on neighbourhood food, transport, and essential services.</p>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homepageSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homepageSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
<section class="hero-section d-flex flex-column flex-lg-row align-items-center">
    <div class="hero-card text-center text-lg-start">
        <span class="badge bg-light text-success mb-3">Find Your Perfect Stay</span>
        <h1 class="display-5 fw-semibold mb-4">Discover Student-Friendly Rooms Near Your Campus</h1>
        <p class="lead text-muted mb-4">Search verified accommodations, connect with senior owners, and settle into a comfortable, affordable space designed for student life.</p>
        <div class="d-flex flex-column flex-md-row gap-3">
            <a href="search.php" class="btn btn-primary btn-lg px-4">Start Searching</a>
            <a href="register.php" class="btn btn-lg px-4" style="color: #319d65; border: 2px solid;
">Join NearBy</a>
        </div>
    </div>
    <div class="hero-illustration p-4 mt-4 mt-lg-0 w-100">
        <div class="row g-3">
            <div class="col-6">
                <div class="glass-card p-4 text-center">
                    <i class="bi bi-house-heart fs-1 text-success"></i>
                    <p class="fw-semibold mt-3 mb-1">Verified Listings</p>
                    <p class="small text-muted">Curated stays with trusted senior owners.</p>
                </div>
            </div>
            <div class="col-6">
                <div class="glass-card p-4 text-center">
                    <i class="bi bi-wifi fs-1 text-success"></i>
                    <p class="fw-semibold mt-3 mb-1">Essential Facilities</p>
                    <p class="small text-muted">Wi-Fi, meals, and amenities in every budget.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="glass-card p-4 text-center">
                    <i class="bi bi-geo-alt fs-1 text-success"></i>
                    <p class="fw-semibold mt-3 mb-1">Stay Close to Campus</p>
                    <p class="small text-muted">Locate rooms around your college in minutes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title fw-semibold">Why NearBy Works</h2>
        <a href="register.php" class="btn btn-sm btn-outline-light">Become a member</a>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h5 fw-semibold">Built for Students</h3>
                <p class="text-muted">Discover accommodations curated for student needs with budgets tailored around college life.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h5 fw-semibold">Safe & Verified</h3>
                <p class="text-muted">Listings are posted by senior students with owner verification badges for peace of mind.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h5 fw-semibold">Smart Recommendations</h3>
                <p class="text-muted">Powerful filters and real-time search make finding the perfect stay quick and stress-free.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="row g-4 align-items-center">
        <div class="col-lg-6">
            <h2 class="section-title fw-semibold mb-4">How NearBy Supports Juniors & Seniors</h2>
            <ul class="list-unstyled d-grid gap-3">
                <li class="glass-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="facility-icon"><i class="bi bi-search"></i></span>
                        <div>
                            <p class="fw-semibold mb-1">Explore instantly</p>
                            <p class="small text-muted mb-0">Search rooms by location, budget, type, and facilities with live updates.</p>
                        </div>
                    </div>
                </li>
                <li class="glass-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="facility-icon"><i class="bi bi-chat-dots"></i></span>
                        <div>
                            <p class="fw-semibold mb-1">Connect securely</p>
                            <p class="small text-muted mb-0">AJAX-powered contact requests keep your conversation private and fast.</p>
                        </div>
                    </div>
                </li>
                <li class="glass-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="facility-icon"><i class="bi bi-clipboard-plus"></i></span>
                        <div>
                            <p class="fw-semibold mb-1">Host with ease</p>
                            <p class="small text-muted mb-0">Senior students can post rooms with all essentials and get junior leads quickly.</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="col-lg-6">
            <div class="glass-card p-4">
                <h3 class="h5 fw-semibold mb-3">Ready to experience NearBy?</h3>
                <p class="text-muted">Create an account to explore verified accommodations, get local recommendations, and settle into your second home.</p>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    <a class="btn btn-primary flex-fill" href="register.php">Create account</a>
                    <a class="btn btn-outline-light flex-fill" href="login.php">I already have one</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="section-title fw-semibold">Featured stays from our community</h2>
            <p class="text-muted small mb-0">Take a peek at popular verified accommodations senior hosts recently shared.</p>
        </div>
        <a class="btn btn-outline-light btn-sm" href="search.php">Browse all spaces</a>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-card featured-card h-100">
                <img src="https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80" class="featured-image" alt="Bright twin sharing room">
                <div class="p-4 d-flex flex-column gap-3">
                    <span class="feature-tag"><i class="bi bi-patch-check-fill"></i>Verified · Near MITS</span>
                    <div>
                        <h3 class="h5 fw-semibold mb-1">Bank Colony</h3>
                        <p class="small text-muted mb-0">2 min walk to campus · Wi-Fi · Meals · Laundry</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-success">₹7,500 / month</span>
                        <span class="small text-muted"><a href="https://maps.app.goo.gl/ygA3C2ZgjkL5BgHJ7" class="text-decoration-none"><i class="bi bi-geo-alt me-1"></i>Bank Colony, Gwalior</a></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card featured-card h-100">
                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80" class="featured-image" alt="Cozy studio apartment interior">
                <div class="p-4 d-flex flex-column gap-3">
                    <span class="feature-tag"><i class="bi bi-lightning-charge"></i>Fast Wi-Fi · AC</span>
                    <div>
                        <h3 class="h5 fw-semibold mb-1">Gole Ka Mandir</h3>
                        <p class="small text-muted mb-0">Dedicated study desk · 24x7 power backup · Parking</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-success">₹11,200 / month</span>
                        <span class="small text-muted"><a href="https://maps.app.goo.gl/hBhKMJuK48Utgj568" class="text-decoration-none"><i class="bi bi-geo-alt me-1"></i>Gole Ka Mandir, Gwalior</a></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card featured-card h-100">
                <img src="https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?auto=format&fit=crop&w=1200&q=80" class="featured-image" alt="Group of students in community area">
                <div class="p-4 d-flex flex-column gap-3">
                    <span class="feature-tag"><i class="bi bi-people"></i>Community Living</span>
                    <div>
                        <h3 class="h5 fw-semibold mb-1">Krishna Nagar</h3>
                        <p class="small text-muted mb-0">Shared lounge · Gym access · Evening mentoring circles</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-success">₹9,300 / month</span>
                        <span class="small text-muted"><a href="https://maps.app.goo.gl/3EPudEjjDycgT3ca9" class="text-decoration-none"><i class="bi bi-geo-alt me-1"></i>Krishna Nagar, Gwalior</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="row g-4 align-items-center">
        <div class="col-lg-5">
            <h2 class="section-title fw-semibold mb-4">Start in three simple steps</h2>
            <p class="text-muted">NearBy keeps the onboarding flow quick whether you are searching for a room or helping juniors as a host.</p>
            <div class="glass-card p-4 d-grid gap-4 mt-4">
                <div class="d-flex gap-3 step-card">
                    <span class="icon-circle"><i class="bi bi-person-badge"></i></span>
                    <div>
                        <h3 class="h6 fw-semibold mb-1">Register using your college email</h3>
                        <p class="small text-muted mb-0">Sign up as junior or senior with automatic role-based dashboards.</p>
                    </div>
                </div>
                <div class="d-flex gap-3 step-card">
                    <span class="icon-circle"><i class="bi bi-funnel"></i></span>
                    <div>
                        <h3 class="h6 fw-semibold mb-1">Apply filters tailored to your needs</h3>
                        <p class="small text-muted mb-0">Search by rent range, facilities, and who the stay is allowed for.</p>
                    </div>
                </div>
                <div class="d-flex gap-3 step-card">
                    <span class="icon-circle"><i class="bi bi-envelope-check"></i></span>
                    <div>
                        <h3 class="h6 fw-semibold mb-1">Connect instantly with verified owners</h3>
                        <p class="small text-muted mb-0">Send contact requests via AJAX and receive replies in your inbox.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="glass-card p-4 p-lg-5 h-100">
                <div class="d-flex flex-column flex-md-row gap-4 align-items-md-center">
                    <div class="flex-shrink-0">
                        <span class="icon-circle" style="width:64px;height:64px;"><i class="bi bi-chat-quote"></i></span>
                    </div>
                    <div>
                        <p class="fst-italic text-muted mb-3">“Within a day I found a furnished room near campus and the senior host helped me figure out the best tiffin services. NearBy made the move-in stress free.”</p>
                        <div>
                            <p class="fw-semibold mb-0">Aditi Sharma</p>
                            <p class="small text-muted">First-year B.Tech · Madhav Institute Of Technology And Science Gwalior</p>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-3 text-center text-md-start">
                    <div class="col-md-4">
                        <h3 class="h2 fw-bold mb-1">12</h3>
                        <p class="small text-muted mb-0">Active student members</p>
                    </div>
                    <div class="col-md-4">
                        <h3 class="h2 fw-bold mb-1">2</h3>
                        <p class="small text-muted mb-0">Verified senior hosts</p>
                    </div>
                    <div class="col-md-4">
                        <h3 class="h2 fw-bold mb-1">95%</h3>
                        <p class="small text-muted mb-0">Match satisfaction rate</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <h2 class="section-title fw-semibold mb-3">Frequently asked questions</h2>
            <p class="text-muted">Still curious about the process? We have collected some quick answers for you.</p>
        </div>
        <div class="col-lg-7">
            <div class="accordion accordion-glass" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            How long does verification take for senior hosts?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="faqOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Once you submit accommodation details with identity proof, our team reviews and approves within 24 working hours. Verified badges appear automatically after approval.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Is there any charge for juniors to contact owners?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            No. NearBy keeps student housing conversations free. You can send unlimited contact requests and only the verified owner sees your basic profile and message.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Can I manage multiple listings as a senior?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="faqThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Absolutely. Add each accommodation via your dashboard. You can pause or update availability anytime and juniors will see real-time status in search results.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>