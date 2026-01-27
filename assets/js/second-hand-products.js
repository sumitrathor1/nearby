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

    const fetchProducts = async (append = false) => {
        if (isLoading) return;
        isLoading = true;

        const selectedCategories = getActiveValues(categoryInputs);
        const selectedConditions = getActiveValues(conditionInputs);
        const maxPrice = priceInput ? priceInput.value : null;
        const search = searchInput ? searchInput.value.trim() : null;

        const params = new URLSearchParams({
            limit: limit,
            offset: append ? currentOffset : 0
        });

        if (selectedCategories.length > 0) params.append('category', selectedCategories[0]); // For simplicity, take first
        if (maxPrice) params.append('max_price', maxPrice);
        if (selectedConditions.length > 0) params.append('condition', selectedConditions[0]);
        if (search) params.append('search', search);

        try {
            const response = await fetch(`api/fetch_products.php?${params}`);
            const data = await response.json();

            if (data.success) {
                if (!append) {
                    productsGrid.innerHTML = '';
                    currentOffset = 0;
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
                <div class="product-image-wrapper">
                    <img src="${imageUrl}" class="product-image" alt="${escapeHtml(product.title)}">
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
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <span class="product-meta text-success fw-semibold">By ${escapeHtml(product.seller_name)}</span>
                        <button class="btn btn-outline-success btn-sm" type="button" onclick="showProductDetails(${product.id})">View Details</button>
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

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            [...categoryInputs, ...conditionInputs].forEach(input => input.checked = false);
            if (priceInput) priceInput.value = 15000;
            if (searchInput) searchInput.value = '';
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

// Function to show product details (can be expanded)
function showProductDetails(productId) {
    // For now, just alert. Can be expanded to modal
    alert(`Product details for ID: ${productId}`);
}
