import { buildPostCard, renderResults } from './ui.js';
import { validateForm } from './forms.js';

const formSelectors = ['#dashboardSearch', '#advancedSearch'];
const refreshHandlers = [];

const fetchPosts = async (params = {}) => {
    const url = new URL('api/posts/list.php', window.location.href);
    Object.entries(params).forEach(([key, value]) => {
        if (Array.isArray(value)) {
            value.filter(Boolean).forEach(item => url.searchParams.append(`${key}[]`, item));
        } else if (value !== '' && value !== null && value !== undefined) {
            url.searchParams.set(key, value);
        }
    });

    const response = await fetch(url.toString(), {headers: {'Accept': 'application/json'}});
    const data = await response.json();
    if (!data.success) {
        throw new Error(data.message || 'Unable to fetch posts');
    }
    return data.data;
};

const buildParamsFromForm = (form) => {
    const formData = new FormData(form);
    const params = {};
    formData.forEach((value, key) => {
        if (key.endsWith('[]')) {
            const arrayKey = key.slice(0, -2);
            params[arrayKey] = params[arrayKey] || [];
            params[arrayKey].push(value);
        } else {
            params[key] = value;
        }
    });
    return params;
};

const initSearchForm = (selector) => {
    const form = document.querySelector(selector);
    if (!form) {
        return;
    }

    const resultsContainer = document.querySelector(form.dataset.resultsTarget || '#searchResults');
    if (!resultsContainer) {
        return;
    }

    const pageSize = 9;
    let currentPage = 1;
    let cachedResults = [];
    const paginationSelector = form.dataset.paginationTarget || '[data-pagination]';
    const paginationWrapper = document.querySelector(paginationSelector);
    const prevBtn = paginationWrapper?.querySelector('[data-pagination-prev]') || null;
    const nextBtn = paginationWrapper?.querySelector('[data-pagination-next]') || null;
    const summaryLabel = paginationWrapper?.querySelector('[data-pagination-summary]') || null;

    const updatePagination = (totalPages) => {
        if (!paginationWrapper) {
            return;
        }
        const shouldHide = totalPages <= 1;
        paginationWrapper.hidden = shouldHide;
        if (shouldHide) {
            return;
        }
        if (prevBtn) {
            prevBtn.disabled = currentPage === 1;
        }
        if (nextBtn) {
            nextBtn.disabled = currentPage === totalPages;
        }
        if (summaryLabel) {
            summaryLabel.textContent = `Page ${currentPage} of ${totalPages}`;
        }
    };

    const renderPage = (page = 1) => {
        const totalPages = Math.ceil(cachedResults.length / pageSize);
        if (totalPages === 0) {
            resultsContainer.innerHTML = '<div class="text-center py-5 text-muted">No posts match your filters yet. Try adjusting the criteria.</div>';
            updatePagination(totalPages);
            return;
        }

        currentPage = Math.min(Math.max(page, 1), totalPages);
        const startIndex = (currentPage - 1) * pageSize;
        const pageItems = cachedResults.slice(startIndex, startIndex + pageSize).map(buildPostCard);
        renderResults(resultsContainer, pageItems);
        updatePagination(totalPages);
    };

    const handleSearch = async () => {
        const params = buildParamsFromForm(form);
        try {
            resultsContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success" role="status"></div><p class="text-muted small mt-2">Loading fresh posts...</p></div>';
            const results = await fetchPosts(params);
            cachedResults = results;
            currentPage = 1;
            if (!cachedResults.length) {
                renderPage();
                return;
            }
            renderPage(currentPage);
        } catch (error) {
            resultsContainer.innerHTML = '';
            NearBy.showMessage(error.message, 'danger');
        }
    };

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                renderPage(currentPage - 1);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(cachedResults.length / pageSize);
            if (currentPage < totalPages) {
                renderPage(currentPage + 1);
            }
        });
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        handleSearch();
    });

    form.addEventListener('reset', () => {
        setTimeout(handleSearch, 150);
    });

    refreshHandlers.push(handleSearch);
    handleSearch();
};

const updateRequiredAttributes = (form, category) => {
    const roomFields = form.querySelector('[data-room-fields]');
    const serviceFields = form.querySelector('[data-service-fields]');
    const roomPriceField = form.querySelector('[data-room-price]');
    const servicePriceField = form.querySelector('[data-service-price]');
    const accommodationField = form.querySelector('[name="accommodation_type"]');
    const allowedForField = form.querySelector('[name="allowed_for"]');
    const serviceTypeField = form.querySelector('[name="service_type"]');
    const availabilityField = form.querySelector('[name="availability_time"]');

    if (roomFields && serviceFields) {
        roomFields.classList.toggle('d-none', category !== 'room');
        serviceFields.classList.toggle('d-none', category !== 'service');
    }

    if (roomPriceField) {
        roomPriceField.disabled = category !== 'room';
        roomPriceField.required = category === 'room';
        if (category !== 'room') {
            roomPriceField.value = '';
        }
    }
    if (servicePriceField) {
        servicePriceField.disabled = category !== 'service';
        servicePriceField.required = false;
        if (category !== 'service') {
            servicePriceField.value = '';
        }
    }
    if (accommodationField) {
        accommodationField.required = category === 'room';
        accommodationField.disabled = category !== 'room';
        if (category !== 'room') {
            accommodationField.value = '';
        }
    }
    if (allowedForField) {
        allowedForField.required = category === 'room';
        allowedForField.disabled = category !== 'room';
        if (category !== 'room') {
            allowedForField.value = '';
        }
    }
    if (serviceTypeField) {
        serviceTypeField.required = category === 'service';
        serviceTypeField.disabled = category !== 'service';
        if (category !== 'service') {
            serviceTypeField.value = '';
        }
    }
    if (availabilityField) {
        availabilityField.required = category === 'service';
        availabilityField.disabled = category !== 'service';
        if (category !== 'service') {
            availabilityField.value = '';
        }
    }
};

const initCreatePostForm = () => {
    const form = document.querySelector('#createPostForm');
    const modalEl = document.querySelector('#createPostModal');
    if (!form || !modalEl) {
        return;
    }

    const postTypeInputs = form.querySelectorAll('[name="post_category"]');
    const facilitiesGroup = form.querySelector('[data-room-fields]');

    const getSelectedCategory = () => form.querySelector('[name="post_category"]:checked')?.value || 'room';

    const syncCategoryState = () => {
        const category = getSelectedCategory();
        updateRequiredAttributes(form, category);
        if (category === 'service' && facilitiesGroup) {
            facilitiesGroup.querySelectorAll('input[type="checkbox"]').forEach(input => {
                input.checked = false;
            });
        }
    };

    postTypeInputs.forEach(input => {
        input.addEventListener('change', syncCategoryState);
    });

    syncCategoryState();

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!validateForm(form)) {
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalLabel = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Publishing';

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());
        const facilities = formData.getAll('facilities[]');
        if (facilities.length) {
            payload.facilities = facilities;
        }

        try {
            await NearBy.fetchJSON('api/posts/create.php', {method: 'POST', body: payload});
            NearBy.showMessage('Post created successfully');
            form.reset();
            syncCategoryState();

            if (window.bootstrap?.Modal) {
                const Modal = window.bootstrap.Modal;
                const modalInstance = Modal.getInstance(modalEl) || new Modal(modalEl);
                modalInstance.hide();
            }

            refreshHandlers.forEach(handler => handler());
        } catch (error) {
            NearBy.showMessage(error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalLabel;
            form.classList.remove('was-validated');
        }
    });
};

formSelectors.forEach(initSearchForm);
initCreatePostForm();
