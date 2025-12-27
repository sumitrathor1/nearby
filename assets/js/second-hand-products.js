document.addEventListener('DOMContentLoaded', () => {
    const filterContainer = document.querySelector('[data-filter-form]');
    const productCards = Array.from(document.querySelectorAll('[data-product-card]'));

    if (!filterContainer || productCards.length === 0) {
        return;
    }

    const categoryInputs = Array.from(filterContainer.querySelectorAll('input[name="category"]'));
    const conditionInputs = Array.from(filterContainer.querySelectorAll('input[name="condition"]'));
    const priceInput = filterContainer.querySelector('[data-filter-price]');
    const priceValueLabel = filterContainer.querySelector('[data-price-value]');
    const clearButton = filterContainer.querySelector('[data-clear-filters]');
    const emptyState = document.querySelector('[data-empty-state]');
    const initialPrice = priceInput ? Number(priceInput.value) : 0;

    const updatePriceLabel = () => {
        if (!priceInput || !priceValueLabel) {
            return;
        }
        const formatted = Number(priceInput.value).toLocaleString('en-IN');
        priceValueLabel.textContent = `₹${formatted}`;
    };

    const getActiveValues = (inputs) => inputs.filter((input) => input.checked).map((input) => input.value);

    const parseCategories = (datasetValue) => datasetValue.split(',').map((value) => value.trim()).filter(Boolean);

    const applyFilters = () => {
        if (!priceInput) {
            return;
        }

        const selectedCategories = getActiveValues(categoryInputs);
        const selectedConditions = getActiveValues(conditionInputs);
        const maxPrice = Number(priceInput.value);
        let visibleCount = 0;

        productCards.forEach((card) => {
            const dataset = card.dataset;
            const cardCategories = parseCategories(dataset.category || '');
            const categoryMatch = selectedCategories.length === 0 || cardCategories.some((value) => selectedCategories.includes(value));
            const conditionMatch = selectedConditions.length === 0 || selectedConditions.includes(dataset.condition || '');
            const priceMatch = Number(dataset.price || 0) <= maxPrice;
            const shouldShow = categoryMatch && conditionMatch && priceMatch;

            card.classList.toggle('d-none', !shouldShow);

            if (shouldShow) {
                visibleCount += 1;
            }
        });

        // Reveal friendly empty state when nothing matches.
        if (emptyState) {
            emptyState.classList.toggle('d-none', visibleCount !== 0);
        }
    };

    const handleInputChange = () => {
        updatePriceLabel();
        applyFilters();
    };

    [...categoryInputs, ...conditionInputs].forEach((input) => {
        input.addEventListener('change', handleInputChange);
    });

    if (priceInput) {
        priceInput.addEventListener('input', handleInputChange);
    }

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            [...categoryInputs, ...conditionInputs].forEach((input) => {
                input.checked = false;
            });

            if (priceInput) {
                priceInput.value = initialPrice;
            }

            handleInputChange();
        });
    }

    handleInputChange();
});
