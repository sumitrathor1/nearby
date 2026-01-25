<?php
$pageTitle = 'Second-Hand Products | NearBy';
$pageStyles = ['assets/css/second-hand-products.css'];
$pageScripts = ['assets/js/second-hand-products.js'];
require_once __DIR__ . '/includes/header.php';
?>
<section class="secondhand-hero text-center mb-5">
    <div class="hero-content mx-auto">
        <h1 class="display-6 fw-semibold mb-3">Second-Hand Products</h1>
        <p class="lead text-muted mb-0">Affordable used items for students looking to set up their space without the stress.</p>
    </div>
</section>
<section class="products-layout">
    <div class="row g-4 align-items-start">
        <aside class="col-12 col-lg-3">
            <div class="filter-panel glass-card p-4" data-filter-form>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 fw-semibold mb-0">Filter Items</h2>
                    <button class="btn btn-sm btn-outline-success" type="button" data-clear-filters>Reset</button>
                </div>
                <div class="filter-group mb-4">
                    <h3 class="filter-heading">Categories</h3>
                    <div class="d-grid gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="fridge" id="categoryFridge" name="category">
                            <label class="form-check-label" for="categoryFridge">Fridge</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="cooler" id="categoryCooler" name="category">
                            <label class="form-check-label" for="categoryCooler">Cooler</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="almirah" id="categoryAlmirah" name="category">
                            <label class="form-check-label" for="categoryAlmirah">Almirah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="washing-machine" id="categoryWasher" name="category">
                            <label class="form-check-label" for="categoryWasher">Washing Machine</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="furniture" id="categoryFurniture" name="category">
                            <label class="form-check-label" for="categoryFurniture">Furniture</label>
                        </div>
                    </div>
                </div>
                <div class="filter-group mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="filter-heading mb-0">Price range</h3>
                        <span class="fw-semibold" data-price-value>₹15,000</span>
                    </div>
                    <input type="range" class="form-range" min="2000" max="20000" step="500" value="15000" data-filter-price>
                    <small class="text-muted">Slide to adjust maximum budget.</small>
                </div>
                <div class="filter-group">
                    <h3 class="filter-heading">Condition</h3>
                    <div class="d-grid gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="new-like" id="conditionNewLike" name="condition">
                            <label class="form-check-label" for="conditionNewLike">New-Like</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="good" id="conditionGood" name="condition">
                            <label class="form-check-label" for="conditionGood">Good</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="old" id="conditionOld" name="condition">
                            <label class="form-check-label" for="conditionOld">Old</label>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <div class="col-12 col-lg-9">
            <div class="row g-4" data-products-grid>
                <div class="col-sm-6 col-xl-4" data-product-card data-category="fridge" data-condition="new-like" data-price="14000">
                    <article class="product-card glass-card h-100">
                        <div class="product-image-wrapper">
                            <img src="https://images.unsplash.com/photo-1677470749785-865d577ef881?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8U2luZ2xlJTIwRG9vciUyMEZyaWRnZXxlbnwwfHwwfHx8MA%3D%3D" class="product-image" alt="Single Door Fridge">
                        </div>
                        <div class="product-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="h5 product-title">Single Door Fridge</h3>
                                <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Fridge</span>
                            </div>
                            <ul class="list-unstyled small text-muted d-grid gap-2 mb-4">
                                <li><i class="bi bi-tag me-2 text-success"></i>₹14,000</li>
                                <li><i class="bi bi-stars me-2 text-success"></i>Condition: New-Like</li>
                                <li><i class="bi bi-geo-alt me-2 text-success"></i>Hostel Road, Sector 12</li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="product-meta text-success fw-semibold">Pickup & delivery support</span>
                                <button class="btn btn-outline-success btn-sm" type="button">View Details</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-4" data-product-card data-category="cooler" data-condition="good" data-price="7000">
                    <article class="product-card glass-card h-100">
                        <div class="product-image-wrapper">
                            <img src="https://images.unsplash.com/photo-1713874990887-6e76a16a2884?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8J0FpciUyMENvb2xlcnxlbnwwfHwwfHx8MA%3D%3D" class="product-image" alt="Tower Air Cooler">
                        </div>
                        <div class="product-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="h5 product-title">Tower Air Cooler</h3>
                                <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Cooler</span>
                            </div>
                            <ul class="list-unstyled small text-muted d-grid gap-2 mb-4">
                                <li><i class="bi bi-tag me-2 text-success"></i>₹7,000</li>
                                <li><i class="bi bi-stars me-2 text-success"></i>Condition: Good</li>
                                <li><i class="bi bi-geo-alt me-2 text-success"></i>City Centre Hostel Lane</li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="product-meta text-success fw-semibold">Free stand & cover</span>
                                <button class="btn btn-outline-success btn-sm" type="button">View Details</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-4" data-product-card data-category="almirah" data-condition="good" data-price="5500">
                    <article class="product-card glass-card h-100">
                        <div class="product-image-wrapper">
                            <img src="https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=800&q=80" class="product-image" alt="Metal Almirah">
                        </div>
                        <div class="product-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="h5 product-title">Metal Almirah</h3>
                                <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Almirah</span>
                            </div>
                            <ul class="list-unstyled small text-muted d-grid gap-2 mb-4">
                                <li><i class="bi bi-tag me-2 text-success"></i>₹5,500</li>
                                <li><i class="bi bi-stars me-2 text-success"></i>Condition: Good</li>
                                <li><i class="bi bi-geo-alt me-2 text-success"></i>Sunrise PG, Block A</li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="product-meta text-success fw-semibold">Includes hanger rod</span>
                                <button class="btn btn-outline-success btn-sm" type="button">View Details</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-4" data-product-card data-category="washing-machine" data-condition="new-like" data-price="16500">
                    <article class="product-card glass-card h-100">
                        <div class="product-image-wrapper">
                            <img src="https://plus.unsplash.com/premium_photo-1761432163381-761fb3fba8a0?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8RnJvbnQlMjBMb2FkJTIwV2FzaGVyfGVufDB8fDB8fHww" class="product-image" alt="Front Load Washing Machine">
                        </div>
                        <div class="product-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="h5 product-title">Front Load Washer</h3>
                                <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Washing Machine</span>
                            </div>
                            <ul class="list-unstyled small text-muted d-grid gap-2 mb-4">
                                <li><i class="bi bi-tag me-2 text-success"></i>₹16,500</li>
                                <li><i class="bi bi-stars me-2 text-success"></i>Condition: New-Like</li>
                                <li><i class="bi bi-geo-alt me-2 text-success"></i>Sector 21 Apartments</li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="product-meta text-success fw-semibold">Energy efficient model</span>
                                <button class="btn btn-outline-success btn-sm" type="button">View Details</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-4" data-product-card data-category="furniture" data-condition="good" data-price="4500">
                    <article class="product-card glass-card h-100">
                        <div class="product-image-wrapper">
                            <img src="https://plus.unsplash.com/premium_photo-1664193968929-d9d9544296e0?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8U3R1ZHklMjBUYWJsZSUyMCUyNiUyMENoYWlyfGVufDB8fDB8fHww" class="product-image" alt="Study Table and Chair Set">
                        </div>
                        <div class="product-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="h5 product-title">Study Table & Chair</h3>
                                <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Furniture</span>
                            </div>
                            <ul class="list-unstyled small text-muted d-grid gap-2 mb-4">
                                <li><i class="bi bi-tag me-2 text-success"></i>₹4,500</li>
                                <li><i class="bi bi-stars me-2 text-success"></i>Condition: Good</li>
                                <li><i class="bi bi-geo-alt me-2 text-success"></i>Greenfield Hostels</li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="product-meta text-success fw-semibold">Includes desk lamp</span>
                                <button class="btn btn-outline-success btn-sm" type="button">View Details</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-4" data-product-card data-category="furniture" data-condition="new-like" data-price="8200">
                    <article class="product-card glass-card h-100">
                        <div class="product-image-wrapper">
                            <img src="https://media.istockphoto.com/id/1404705316/photo/bed-frame.webp?a=1&b=1&s=612x612&w=0&k=20&c=k7AJL1hFYIy04r9ZQeYPBPZEU7K9y2Bo6Vxmpkuo0vw=" class="product-image" alt="Wooden Bed Frame">
                        </div>
                        <div class="product-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="h5 product-title">Wooden Bed Frame</h3>
                                <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Furniture</span>
                            </div>
                            <ul class="list-unstyled small text-muted d-grid gap-2 mb-4">
                                <li><i class="bi bi-tag me-2 text-success"></i>₹8,200</li>
                                <li><i class="bi bi-stars me-2 text-success"></i>Condition: New-Like</li>
                                <li><i class="bi bi-geo-alt me-2 text-success"></i>Lakeview Residency</li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="product-meta text-success fw-semibold">Fits queen mattress</span>
                                <button class="btn btn-outline-success btn-sm" type="button">View Details</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-4" data-product-card data-category="furniture,other" data-condition="good" data-price="2800">
                    <article class="product-card glass-card h-100">
                        <div class="product-image-wrapper">
                            <img src="https://media.istockphoto.com/id/1398782425/photo/close-up-shot-of-a-hand-using-a-remote-control-to-operate-a-ceiling-fan-mounted-in-a-house-on.webp?a=1&b=1&s=612x612&w=0&k=20&c=qa3ClMgoDAqDTaJM55EioCs6ikv_fcFZwNBytz0XBm0=" class="product-image" alt="Ceiling Fan with Remote">
                        </div>
                        <div class="product-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="h5 product-title">Ceiling Fan + Remote</h3>
                                <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Household</span>
                            </div>
                            <ul class="list-unstyled small text-muted d-grid gap-2 mb-4">
                                <li><i class="bi bi-tag me-2 text-success"></i>₹2,800</li>
                                <li><i class="bi bi-stars me-2 text-success"></i>Condition: Good</li>
                                <li><i class="bi bi-geo-alt me-2 text-success"></i>Maple Heights Dorm</li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="product-meta text-success fw-semibold">Silent operation mode</span>
                                <button class="btn btn-outline-success btn-sm" type="button">View Details</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-4" data-product-card data-category="furniture" data-condition="old" data-price="3200">
                    <article class="product-card glass-card h-100">
                        <div class="product-image-wrapper">
                            <img src="https://plus.unsplash.com/premium_photo-1674675647726-e4910962e79a?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8T3J0aG9wZWRpYyUyME1hdHRyZXNzfGVufDB8fDB8fHww" class="product-image" alt="Single Mattress">
                        </div>
                        <div class="product-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="h5 product-title">Orthopedic Mattress</h3>
                                <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Furniture</span>
                            </div>
                            <ul class="list-unstyled small text-muted d-grid gap-2 mb-4">
                                <li><i class="bi bi-tag me-2 text-success"></i>₹3,200</li>
                                <li><i class="bi bi-stars me-2 text-success"></i>Condition: Old</li>
                                <li><i class="bi bi-geo-alt me-2 text-success"></i>City PG Block C</li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="product-meta text-success fw-semibold">Freshly sanitized cover</span>
                                <button class="btn btn-outline-success btn-sm" type="button">View Details</button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
            <div class="empty-state text-center py-5 d-none" data-empty-state>
                <div class="glass-card d-inline-block px-4 py-5">
                    <i class="bi bi-box-seam display-6 text-success mb-3"></i>
                    <h3 class="h5 fw-semibold mb-2">No matches yet</h3>
                    <p class="text-muted mb-0">Adjust your filters to see more pre-loved items.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
