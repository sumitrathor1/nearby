const MAP_CENTER = [26.2325, 78.2053];
const MAP_ZOOM = 14;
const API_ENDPOINT = 'api/map/locations.php';
const LEAFLET_STYLESHEET = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
const LEAFLET_SCRIPT = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
const numberFormatter = new Intl.NumberFormat('en-IN');

const state = {
    root: null,
    map: null,
    canvas: null,
    loadingEl: null,
    listingsEl: null,
    countEl: null,
    chips: null,
    priceInput: null,
    priceLabel: null,
    searchInput: null,
    markersLayer: null,
    filters: {
        type: 'all',
        maxPrice: 15000,
        query: ''
    },
    listings: [],
    lastRequestId: 0
};

function init() {
    state.root = document.querySelector('[data-map-explorer]');
    if (!state.root) {
        return;
    }

    collectElements();
    if (!state.canvas) {
        return;
    }

    ensureLeafletResources()
        .then(setupMap)
        .then(() => {
            bindEvents();
            updatePriceLabel();
            refreshLocations();
        })
        .catch((error) => {
            renderError(error instanceof Error ? error.message : 'Unable to load map resources');
        });
}

function collectElements() {
    state.canvas = state.root.querySelector('[data-map-canvas]');
    state.loadingEl = state.root.querySelector('[data-map-loading]');
    state.listingsEl = state.root.querySelector('[data-map-listings]');
    state.countEl = state.root.querySelector('[data-location-count]');
    state.chips = Array.from(state.root.querySelectorAll('[data-map-chips] button'));
    state.priceInput = state.root.querySelector('#mapPriceRange');
    state.priceLabel = state.root.querySelector('[data-price-value]');
    state.searchInput = state.root.querySelector('#mapSearchField');

    if (state.priceInput) {
        state.priceInput.value = String(state.filters.maxPrice);
    }
}

function ensureLeafletResources() {
    if (window.L) {
        return Promise.resolve();
    }

    return Promise.all([
        injectStylesheet(LEAFLET_STYLESHEET),
        injectScript(LEAFLET_SCRIPT)
    ]);
}

function injectStylesheet(href) {
    return new Promise((resolve, reject) => {
        if (document.querySelector(`link[href="${href}"]`)) {
            resolve();
            return;
        }

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.onload = () => resolve();
        link.onerror = () => reject(new Error('Failed to load map styles'));
        document.head.appendChild(link);
    });
}

function injectScript(src) {
    return new Promise((resolve, reject) => {
        const existing = document.querySelector(`script[src="${src}"]`);
        if (existing) {
            existing.addEventListener('load', () => resolve(), { once: true });
            existing.addEventListener('error', () => reject(new Error('Failed to load map library')), { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = src;
        script.defer = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Failed to load map library'));
        document.head.appendChild(script);
    });
}

function setupMap() {
    state.map = L.map(state.canvas, {
        zoomControl: false,
        maxZoom: 18,
        attributionControl: false
    }).setView(MAP_CENTER, MAP_ZOOM);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: ''
    }).addTo(state.map);

    L.control.zoom({ position: 'bottomright' }).addTo(state.map);
    state.markersLayer = L.layerGroup().addTo(state.map);

    if (state.loadingEl) {
        state.loadingEl.remove();
        state.loadingEl = null;
    }
}

function bindEvents() {
    state.chips.forEach((chip) => {
        chip.addEventListener('click', () => {
            state.chips.forEach((button) => button.classList.remove('active'));
            chip.classList.add('active');
            state.filters.type = chip.dataset.filter || 'all';
            refreshLocations();
        });
    });

    if (state.priceInput && state.priceLabel) {
        state.priceInput.addEventListener('input', () => {
            state.filters.maxPrice = Number(state.priceInput.value) || 15000;
            updatePriceLabel();
        });
        state.priceInput.addEventListener('change', () => refreshLocations());
    }

    if (state.searchInput) {
        const debounced = debounce((value) => {
            state.filters.query = value.trim();
            refreshLocations();
        }, 400);
        state.searchInput.addEventListener('input', (event) => {
            debounced((event.target || {}).value || '');
        });
    }

    if (state.listingsEl) {
        state.listingsEl.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            const listing = target.closest('[data-location-id]');
            if (!listing) {
                return;
            }

            const locationId = Number(listing.getAttribute('data-location-id'));
            const location = state.listings.find((item) => item.id === locationId);
            handleListingInteraction(location, target);
        });

        state.listingsEl.addEventListener('keydown', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            const listing = target.closest('[data-location-id]');
            if (!listing) {
                return;
            }

            event.preventDefault();
            const locationId = Number(listing.getAttribute('data-location-id'));
            const location = state.listings.find((item) => item.id === locationId);
            handleListingInteraction(location, target);
        });
    }
}

function debounce(fn, delay) {
    let timer;
    return (value) => {
        clearTimeout(timer);
        timer = window.setTimeout(() => fn(value), delay);
    };
}

function refreshLocations() {
    if (!state.listingsEl) {
        return;
    }

    state.listingsEl.innerHTML = '<div class="text-center py-4 text-muted small">Finding places seniors trust...</div>';
    updateCountBadge(0);

    const requestId = ++state.lastRequestId;
    fetchLocations(state.filters)
        .then((locations) => {
            if (requestId !== state.lastRequestId) {
                return;
            }
            state.listings = locations;
            renderListings(locations);
            renderMarkers(locations);
            updateCountBadge(locations.length);
        })
        .catch((error) => {
            if (requestId !== state.lastRequestId) {
                return;
            }
            renderError(error instanceof Error ? error.message : 'Failed to fetch locations');
        });
}

function fetchLocations(filters) {
    const params = new URLSearchParams();
    if (filters.type && filters.type !== 'all') {
        params.set('type', filters.type);
    }
    if (filters.maxPrice) {
        params.set('max_price', filters.maxPrice.toString());
    }
    if (filters.query) {
        params.set('q', filters.query);
    }

    const query = params.toString();
    const url = query ? `${API_ENDPOINT}?${query}` : API_ENDPOINT;

    return fetch(url, { headers: { 'Accept': 'application/json' } })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Map data request failed');
            }
            return response.json();
        })
        .then((payload) => {
            if (!payload.success) {
                throw new Error(payload.message || 'Unable to load map data');
            }
            return Array.isArray(payload.data) ? payload.data : [];
        });
}

function renderListings(locations) {
    if (!state.listingsEl) {
        return;
    }

    if (!locations.length) {
        state.listingsEl.innerHTML = '<div class="text-center py-4 text-muted small">No verified place matches the current filters yet.</div>';
        return;
    }

    const fragment = document.createDocumentFragment();
    locations.forEach((location) => {
        fragment.appendChild(buildListingCard(location));
    });

    state.listingsEl.innerHTML = '';
    state.listingsEl.appendChild(fragment);
}

function buildListingCard(location) {
    const card = document.createElement('article');
    card.className = 'map-listing-card text-start w-100';
    card.dataset.locationId = String(location.id);
    card.setAttribute('role', 'button');
    card.tabIndex = 0;

    card.innerHTML = [
        `<div class="d-flex justify-content-between align-items-start">`,
        `<div>`,
        `<p class="fw-semibold mb-1">${escapeHtml(location.name)}</p>`,
        `<p class="text-muted small mb-2">${escapeHtml(location.description)}</p>`,
        `</div>`,
        `<span class="badge text-bg-light text-uppercase small map-type-${escapeHtml(location.type)}">${escapeHtml(location.type)}</span>`,
        `</div>`,
        location.price ? `<div class="fw-semibold text-success small mb-2">${escapeHtml(location.price)}</div>` : '',
        `<div class="d-flex justify-content-between align-items-center mt-2">`,
        `<span class="small text-muted"><i class="bi bi-patch-check-fill me-1 text-success"></i>Verified by ${escapeHtml(location.verified_by)}</span>`,
        `<button class="btn btn-outline-success btn-sm" type="button" data-map-contact>Contact</button>`,
        `</div>`
    ].join('');

    return card;
}

function renderMarkers(locations) {
    if (!state.map || !state.markersLayer) {
        return;
    }

    state.markersLayer.clearLayers();
    if (!locations.length) {
        state.map.setView(MAP_CENTER, MAP_ZOOM);
        return;
    }

    const bounds = [];

    locations.forEach((location) => {
        const marker = L.marker([location.lat, location.lng], {
            icon: buildIcon(location.type)
        });

        marker.bindPopup(buildPopupHtml(location));
        marker.addTo(state.markersLayer);
        bounds.push([location.lat, location.lng]);
    });

    if (bounds.length === 1) {
        state.map.setView(bounds[0], 16);
    } else {
        state.map.fitBounds(bounds, { padding: [32, 32] });
    }
}

function buildIcon(type) {
    const colors = {
        housing: 'blue',
        food: 'red',
        essential: 'violet',
        college: 'orange'
    };
    const color = colors[type] || 'green';

    return L.icon({
        iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
}

function buildPopupHtml(location) {
    const priceLine = location.price ? `<p class="fw-semibold mb-1">${escapeHtml(location.price)}</p>` : '';
    return [
        `<div class="map-popup">`,
        `<h3 class="h6 fw-semibold mb-1">${escapeHtml(location.name)}</h3>`,
        `<p class="text-muted small mb-2">${escapeHtml(location.description)}</p>`,
        priceLine,
        `<p class="text-muted small mb-0">Verified by ${escapeHtml(location.verified_by)}</p>`,
        `</div>`
    ].join('');
}

function handleListingInteraction(location, target) {
    if (!location || !state.map) {
        return;
    }

    state.map.setView([location.lat, location.lng], 16);
    const layers = [];
    state.markersLayer.eachLayer((layer) => {
        layers.push(layer);
    });

    const marker = layers.find((layer) => {
        const point = layer.getLatLng();
        return point.lat === location.lat && point.lng === location.lng;
    });

    if (marker) {
        marker.openPopup();
    }

    if (target.matches('[data-map-contact]')) {
        showContactToast(location);
    }
}

function showContactToast(location) {
    if (window.NearBy && typeof window.NearBy.showMessage === 'function') {
        window.NearBy.showMessage(`Reach out to ${location.verified_by} via the contact section.`, 'info');
    }
}

function updateCountBadge(count) {
    if (state.countEl) {
        state.countEl.textContent = String(count);
    }
}

function renderError(message) {
    if (state.listingsEl) {
        state.listingsEl.innerHTML = `<div class="text-center py-4 text-danger small">${escapeHtml(message)}</div>`;
    }
    if (state.countEl) {
        state.countEl.textContent = '0';
    }
}

function updatePriceLabel() {
    if (state.priceLabel) {
        state.priceLabel.textContent = numberFormatter.format(state.filters.maxPrice);
    }
}

function escapeHtml(value) {
    if (value === null || value === undefined) {
        return '';
    }

    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

document.addEventListener('DOMContentLoaded', init);
