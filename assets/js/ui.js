const facilityIconMap = {
    'Wi-Fi': 'bi-wifi',
    'Food': 'bi-basket',
    'Water': 'bi-droplet',
    'Electricity': 'bi-lightning-charge',
    'Parking': 'bi-car-front',
    'CCTV': 'bi-camera-video',
    'Power Backup': 'bi-battery-charging'
};

const userTypeBadge = {
    student: 'bg-success',
    owner: 'bg-warning',
    service_provider: 'bg-info'
};

export const escapeHtml = (value = '') => {
    const stringValue = String(value ?? '');
    return stringValue.replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[char]);
};

const formatCurrency = (amount) => {
    if (amount === null || Number.isNaN(Number(amount)) || Number(amount) <= 0) {
        return 'Contact for price';
    }
    return `₹${Number(amount).toLocaleString()}`;
};

const formatServiceLabel = (service) => {
    const labels = {
        tiffin: 'Tiffin / Mess',
        gas: 'Gas Supply',
        milk: 'Milk Delivery',
        sabji: 'Vegetable Vendor',
        other: 'Local Service'
    };
    return labels[service] || 'Local Service';
};

const formatUserType = (userType) => {
    const labels = {
        student: 'Student',
        owner: 'Owner',
        service_provider: 'Service Provider'
    };
    return labels[userType] || 'Community Member';
};

const buildFacilityChips = (facilities = []) => {
    if (!facilities.length) {
        return '<span class="badge bg-secondary-subtle text-secondary small">Facilities info shared on request</span>';
    }
    return facilities.map(facility => {
        const icon = facilityIconMap[facility] || 'bi-check-circle';
        return `
            <span class="facility-icon" title="${escapeHtml(facility)}">
                <i class="bi ${icon}"></i>
            </span>
        `;
    }).join('');
};

const formatCreatedAt = (isoValue) => {
    if (!isoValue) {
        return '';
    }
    const date = new Date(isoValue);
    if (Number.isNaN(date.getTime())) {
        return '';
    }
    return date.toLocaleDateString(undefined, {day: 'numeric', month: 'short'});
};

export const buildPostCard = (item) => {
    const isRoom = item.post_category === 'room';
    const typeBadge = isRoom ? 'Room Rent' : 'Local Service';
    const badgeStyle = isRoom ? 'bg-primary' : 'bg-info';
    const userBadgeStyle = userTypeBadge[item.user_type] || 'bg-dark';
    const serviceLabel = isRoom ? `${item.accommodation_type || 'Room'}${item.allowed_for ? ' · ' + item.allowed_for : ''}` : formatServiceLabel(item.service_type);
    const priceLabel = formatCurrency(item.rent_or_price ?? null);
    const safeLocation = escapeHtml(item.location || 'Location shared after contact');
    const safeDescription = escapeHtml(item.description || 'Details shared by owner soon.');
    const availabilityMarkup = !isRoom && item.availability_time
        ? `<div class="small text-muted"><i class="bi bi-clock me-1"></i>${escapeHtml(item.availability_time)}</div>`
        : '';
    const createdOn = formatCreatedAt(item.created_at);
    const contactMarkup = item.can_view_contact && item.contact_phone
        ? `<div class="alert alert-success bg-opacity-10 border-0 mb-0"><i class="bi bi-telephone me-2"></i>${escapeHtml(item.contact_phone)}</div>`
        : '<div class="alert alert-secondary bg-opacity-10 border-0 mb-0"><i class="bi bi-lock me-2"></i><a href="login.php" class="link-secondary text-decoration-underline fw-semibold">Login</a> to view contact details</div>';

    return `
        <div class="col-md-6 col-xl-4">
            <div class="glass-card h-100 p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge ${badgeStyle} bg-opacity-75 text-white">${typeBadge}</span>
                            <span class="badge ${userBadgeStyle} bg-opacity-75 text-white">${escapeHtml(formatUserType(item.user_type))}</span>
                            ${createdOn ? `<span class="badge bg-dark-subtle text-dark-emphasis">${createdOn}</span>` : ''}
                        </div>
                        <h3 class="h5 fw-semibold mb-1">${escapeHtml(serviceLabel)}</h3>
                        <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>${safeLocation}</div>
                    </div>
                    <div class="text-end">
                        <span class="fw-semibold text-success">${priceLabel}</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1">${safeDescription}</p>
                ${availabilityMarkup}
                <div class="d-flex flex-wrap gap-2 align-items-center mt-3 mb-3">
                    ${buildFacilityChips(item.facilities)}
                </div>
                ${contactMarkup}
            </div>
        </div>
    `;
};

export const renderResults = (container, cardMarkupList) => {
    container.innerHTML = cardMarkupList.join('');
};
