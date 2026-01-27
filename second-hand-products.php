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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="search-container flex-grow-1 me-3">
                    <input type="text" class="form-control" placeholder="Search products..." data-search-input>
                </div>
                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#postProductModal">
                    <i class="bi bi-plus-circle me-2"></i>Post Product
                </button>
            </div>
            <div class="row g-4" data-products-grid>
                <!-- Products will be loaded here dynamically -->
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-outline-success d-none" type="button" data-load-more>Load More</button>
            </div>
            <div class="empty-state text-center py-5 d-none" data-empty-state>
                <div class="glass-card d-inline-block px-4 py-5">
                    <i class="bi bi-box-seam display-6 text-success mb-3"></i>
                    <h3 class="h5 fw-semibold mb-2">No products found</h3>
                    <p class="text-muted mb-0">Be the first to post a second-hand product!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Post Product Modal -->
<div class="modal fade" id="postProductModal" tabindex="-1" aria-labelledby="postProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title" id="postProductModalLabel">Post Second-Hand Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postProductForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="productTitle" class="form-label">Product Title *</label>
                            <input type="text" class="form-control" id="productTitle" required>
                        </div>
                        <div class="col-md-6">
                            <label for="productCategory" class="form-label">Category *</label>
                            <select class="form-select" id="productCategory" required>
                                <option value="">Select Category</option>
                                <option value="fridge">Fridge</option>
                                <option value="cooler">Cooler</option>
                                <option value="almirah">Almirah</option>
                                <option value="washing-machine">Washing Machine</option>
                                <option value="furniture">Furniture</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="productPrice" class="form-label">Price (₹) *</label>
                            <input type="number" class="form-control" id="productPrice" min="1" max="100000" required>
                        </div>
                        <div class="col-md-6">
                            <label for="productCondition" class="form-label">Condition *</label>
                            <select class="form-select" id="productCondition" required>
                                <option value="">Select Condition</option>
                                <option value="new-like">New-Like</option>
                                <option value="good">Good</option>
                                <option value="old">Old</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="productLocation" class="form-label">Location *</label>
                            <input type="text" class="form-control" id="productLocation" placeholder="e.g., Hostel Road, Sector 12" required>
                        </div>
                        <div class="col-12">
                            <label for="productDescription" class="form-label">Description *</label>
                            <textarea class="form-control" id="productDescription" rows="3" placeholder="Describe the product condition, features, etc." required></textarea>
                        </div>
                        <div class="col-12">
                            <label for="contactPhone" class="form-label">Contact Phone *</label>
                            <input type="tel" class="form-control" id="contactPhone" placeholder="Your contact number" required>
                        </div>
                        <div class="col-12">
                            <label for="productImage" class="form-label">Product Image URL (Optional)</label>
                            <input type="url" class="form-control" id="productImage" placeholder="https://example.com/image.jpg">
                            <small class="text-muted">Leave empty for default image</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="submitProduct">Post Product</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
