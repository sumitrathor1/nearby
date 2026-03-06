import { escapeHtml } from './ui.js';

document.addEventListener('DOMContentLoaded', () => {
    const filterContainer = document.querySelector('[data-filter-form]');
    const productsGrid = document.querySelector('[data-products-grid]');
    const clearButton = filterContainer ? filterContainer.querySelector('[data-clear-filters]') : null;
    const priceInput = filterContainer ? filterContainer.querySelector('[data-filter-price]') : null;
    const priceValueLabel = filterContainer ? filterContainer.querySelector('[data-price-value]') : null;
    const categoryInputs = filterContainer ? Array.from(filterContainer.querySelectorAll('input[name="category"]')) : [];
    const conditionInputs = filterContainer ? Array.from(filterContainer.querySelectorAll('input[name="condition"]')) : [];
  const searchInput = document.querySelector('[data-search-input]');
const sortSelect = document.querySelector('[data-sort]');
const locationInput = document.querySelector('[data-filter-location]');
const loadMoreButton = document.querySelector('[data-load-more]');
    let currentOffset = 0;
    const limit = 12;
    let isLoading = false;

    const updatePriceLabel = () => {
        if (!priceInput || !priceValueLabel) return;
        const formatted = Number(priceInput.value).toLocaleString('en-IN');
        priceValueLabel.textContent = `₹${formatted}`;
    };

    const getActiveValues = (inputs) => inputs.filter(input => input.checked).map(input => input.value);
    const defaultProducts = [
{
    id: 0,
    title: "Single Door Fridge",
    category: "fridge",
    price: 4500,
    condition: "good",
    location: "MITS Gate",
    seller_name: "Demo Seller",
    image_url: "https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=600"
},
{
    id: 0,
    title: "Study Table",
    category: "furniture",
    price: 1500,
    condition: "good",
    location: "Hostel Road",
    seller_name: "Demo Seller",
    image_url: "https://images.unsplash.com/photo-1519710164239-da123dc03ef4?w=600"
},
{
    id: 0,
    title: "Room Air Cooler",
    category: "cooler",
    price: 2200,
    condition: "new-like",
    location: "Thatipur",
    seller_name: "Demo Seller",
    image_url: "https://images.unsplash.com/photo-1598300053653-4c5b61a18c64?w=600"
}
];
    const fetchProducts = async (append = false) => {
        if (isLoading) return;
        isLoading = true;

       const selectedCategories = getActiveValues(categoryInputs);
const selectedConditions = getActiveValues(conditionInputs);
const maxPrice = priceInput ? priceInput.value : null;
const search = searchInput ? searchInput.value.trim() : null;
const location = locationInput ? locationInput.value.trim() : null;
const sort = sortSelect ? sortSelect.value : "latest";
        const params = new URLSearchParams({
            limit: limit,
            offset: append ? currentOffset : 0
        });

        if (selectedCategories.length > 0) params.append('category', selectedCategories[0]);
if (maxPrice) params.append('max_price', maxPrice);
if (selectedConditions.length > 0) params.append('condition', selectedConditions[0]);
if (search) params.append('search', search);
if (location) params.append('location', location);
if (sort) params.append('sort', sort);
        try {
            const response = await fetch(`/nearby/api/fetch_products.php?${params}`);

/* Read raw response first to avoid JSON parsing crash */
const text = await response.text();

if (!text) {
    throw new Error("Empty API response");
}

let data;

try {
    data = JSON.parse(text);
} catch (err) {
    console.error("Invalid JSON returned from API:", text);
    throw new Error("Invalid JSON response from server");
}

            if (data.success) {
                if (!append) {
    productsGrid.innerHTML = '';
    currentOffset = 0;
}

/* Show default products if database is empty */
if (!append && data.products.length === 0) {

    defaultProducts.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });

    if (loadMoreButton) {
        loadMoreButton.classList.add('d-none');
    }

    const emptyState = document.querySelector('[data-empty-state]');
    if (emptyState) emptyState.classList.add('d-none');

    return;
}

                data.products.forEach(product => {
                    const productCard = createProductCard(product);
                    productsGrid.appendChild(productCard);
                });

                currentOffset += data.products.length;

                if (loadMoreButton) {
                    loadMoreButton.classList.toggle('d-none', currentOffset >= data.total);
                }

                // Show empty state if no products
                const emptyState = document.querySelector('[data-empty-state]');
                if (emptyState) {
                    emptyState.classList.toggle('d-none', data.products.length > 0 || currentOffset > 0);
                }
            }
        } catch (error) {
    console.error('Error fetching products:', error);

    const emptyState = document.querySelector('[data-empty-state]');
    if (emptyState) {
        emptyState.classList.remove('d-none');
        emptyState.innerHTML = `
            <div class="text-center text-danger py-5">
                <i class="bi bi-exclamation-triangle fs-2"></i>
                <p class="mt-2">Failed to load products. Please refresh.</p>
            </div>
        `;
    }
} finally {
            isLoading = false;
        }
    };

    const createProductCard = (product) => {
        const col = document.createElement('div');
        col.className = 'col-sm-6 col-xl-4';
        col.setAttribute('data-product-card', '');
        col.setAttribute('data-category', product.category);
        col.setAttribute('data-condition', product.condition);
        col.setAttribute('data-price', product.price);

        const imageUrl = product.image_url || 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&auto=format&fit=crop&q=60';

        col.innerHTML = `
            <article class="product-card glass-card h-100">
                <div class="product-image-wrapper position-relative">

    <img src="${imageUrl}" class="product-image" alt="${escapeHtml(product.title)}">

    <button 
        class="wishlist-btn btn btn-light position-absolute top-0 end-0 m-2"
        onclick="toggleWishlist(${product.id})"
        title="Save item">

        <i class="bi bi-heart"></i>

    </button>

</div>
                <div class="product-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h3 class="h5 product-title">${escapeHtml(product.title)}</h3>
                        <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">${escapeHtml(product.category.replace('-', ' '))}</span>
                    </div>
                    <ul class="list-unstyled small text-muted d-grid gap-2 mb-4">
                        <li><i class="bi bi-tag me-2 text-success"></i>₹${product.price.toLocaleString('en-IN')}</li>
                        <li><i class="bi bi-stars me-2 text-success"></i>Condition: ${escapeHtml(product.condition.replace('-', ' '))}</li>
                        <li><i class="bi bi-geo-alt me-2 text-success"></i>${escapeHtml(product.location)}</li>
                    </ul>
                    <div class="seller-info small text-muted mb-3">
    <div class="d-flex align-items-center gap-2 flex-wrap">

        <span class="fw-semibold text-success">
            <i class="bi bi-person-circle me-1"></i>
            ${escapeHtml(product.seller_name)}
        </span>

        <span class="badge bg-success-subtle text-success">
            <i class="bi bi-patch-check-fill"></i>
            Verified
        </span>

        <span class="badge bg-light text-dark">
            <i class="bi bi-calendar3"></i>
            Member since 2024
        </span>

        <span class="badge bg-warning-subtle text-warning">
            <i class="bi bi-star-fill"></i>
            4.8 Rating
        </span>

        <span class="badge bg-info-subtle text-info">
            <i class="bi bi-clock"></i>
            Responds quickly
        </span>

    </div>
</div>

<div class="mt-auto d-flex justify-content-between align-items-center">

    <button class="btn btn-outline-success btn-sm" type="button" onclick="showProductDetails(${product.id})">
        View Details
    </button>

    <button class="btn btn-success btn-sm">
        <i class="bi bi-chat-dots me-1"></i>
        Contact
    </button>

</div>
                </div>
            </article>
        `;

        return col;
    };

    const applyFilters = () => {
        updatePriceLabel();
        fetchProducts(false);
    };

    const handleInputChange = () => {
        applyFilters();
    };

    // Event listeners
    [...categoryInputs, ...conditionInputs].forEach(input => {
        input.addEventListener('change', handleInputChange);
    });

    if (priceInput) {
        priceInput.addEventListener('input', handleInputChange);
    }

    if (searchInput) {
    searchInput.addEventListener('input', debounce(handleInputChange, 300));
}

if (locationInput) {
    locationInput.addEventListener('input', debounce(handleInputChange, 300));
}

if (sortSelect) {
    sortSelect.addEventListener('change', handleInputChange);
}

    if (clearButton) {
    clearButton.addEventListener('click', () => {
        [...categoryInputs, ...conditionInputs].forEach(input => input.checked = false);

        if (priceInput) priceInput.value = 15000;
        if (searchInput) searchInput.value = '';
        if (locationInput) locationInput.value = '';
        if (sortSelect) sortSelect.value = 'latest';

        handleInputChange();
    });
}

    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', () => fetchProducts(true));
    }

    // Debounce function for search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initial load
    fetchProducts();

    // Handle product posting
    const submitProductBtn = document.getElementById('submitProduct');
    if (submitProductBtn) {
        submitProductBtn.addEventListener('click', async () => {
            const formData = {
                title: document.getElementById('productTitle').value.trim(),
                category: document.getElementById('productCategory').value,
                price: parseInt(document.getElementById('productPrice').value),
                condition: document.getElementById('productCondition').value,
                location: document.getElementById('productLocation').value.trim(),
                description: document.getElementById('productDescription').value.trim(),
                contact_phone: document.getElementById('contactPhone').value.trim(),
                image_url: document.getElementById('productImage').value.trim() || null
            };

            // Basic validation
            if (!formData.title || !formData.category || !formData.price || !formData.condition || !formData.location || !formData.description || !formData.contact_phone) {
                alert('Please fill in all required fields.');
                return;
            }

            try {
                const response = await fetch('api/create_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    alert('Product posted successfully!');
                    // Close modal and reset form
                    const modal = bootstrap.Modal.getInstance(document.getElementById('postProductModal'));
                    modal.hide();
                    document.getElementById('postProductForm').reset();
                    // Refresh products
                    fetchProducts();
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                console.error('Error posting product:', error);
                alert('Failed to post product. Please try again.');
            }
        });
    }
});

/* ===============================
   Wishlist Feature (#126)
================================*/

function getWishlist() {
    const saved = localStorage.getItem("nearby_wishlist");
    return saved ? JSON.parse(saved) : [];
}

function saveWishlist(list) {
    localStorage.setItem("nearby_wishlist", JSON.stringify(list));
}

function toggleWishlist(productId) {

    let wishlist = getWishlist();

    if (wishlist.includes(productId)) {
        wishlist = wishlist.filter(id => id !== productId);
        alert("Removed from wishlist");
    } else {
        wishlist.push(productId);
        alert("Saved to wishlist");
    }

    saveWishlist(wishlist);
}


/* ===============================
   Product Details Modal
================================*/

async function showProductDetails(productId) {

    try {

        const response = await fetch(`api/fetch_product_details.php?id=${productId}`);
        const data = await response.json();

        if (!data.success) {
            alert("Failed to load product details");
            return;
        }

        const product = data.product;

        document.getElementById('detailProductTitle').textContent = product.title;
        document.getElementById('detailProductPrice').textContent =
            `₹${product.price.toLocaleString('en-IN')}`;

        document.getElementById('detailProductCondition').textContent = product.condition;
        document.getElementById('detailProductLocation').textContent = product.location;
        document.getElementById('detailProductDescription').textContent = product.description;
        document.getElementById('detailSellerName').textContent = product.seller_name;

        document.getElementById('detailProductImage').src =
            product.image_url ||
            "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600";

        const modal = new bootstrap.Modal(
            document.getElementById('productDetailsModal')
        );

        modal.show();

    } catch (error) {

        console.error("Error loading product:", error);
        alert("Error loading product details");

    }
}