<?php
$pageTitle = 'Local Guidance | NearBy';
$pageScripts = ['assets/js/message.js'];
$enableChatbot = true;
require_once __DIR__ . '/includes/header.php';
?>
<section class="mb-5">
    <div class="text-center mb-4">
        <h1 class="h4 fw-semibold">Navigate Your New Neighbourhood</h1>
        <p class="text-muted small">Handpicked recommendations from seniors for essentials around campus.</p>
    </div>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h2 class="h5 fw-semibold mb-3"><i class="bi bi-egg-fried me-2 text-success"></i>Food & Mess</h2>
                <ul class="list-unstyled d-grid gap-3 small text-muted mb-0">
                    <li>
                        <p class="fw-semibold text-dark mb-1">Campus Delight Mess</p>
                        <p class="mb-0">Veg and non-veg meals · ₹2500/month · 7:30AM–10PM</p>
                    </li>
                    <li>
                        <p class="fw-semibold text-dark mb-1">Green Bowl Tiffin</p>
                        <p class="mb-0">North & South Indian meals delivered daily.</p>
                    </li>
                    <li>
                        <p class="fw-semibold text-dark mb-1">Late Night Bytes</p>
                        <p class="mb-0">Cafe near south gate · Wi-Fi · Quiet study booths.</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h2 class="h5 fw-semibold mb-3"><i class="bi bi-bus-front me-2 text-success"></i>Transport</h2>
                <ul class="list-unstyled d-grid gap-3 small text-muted mb-0">
                    <li>
                        <p class="fw-semibold text-dark mb-1">Shuttle Bus Route</p>
                        <p class="mb-0">Campus ↔ City Center · Every 20 mins (6AM–10PM)</p>
                    </li>
                    <li>
                        <p class="fw-semibold text-dark mb-1">Metro Line Blue</p>
                        <p class="mb-0">Nearest station: College Square · 10 min walk.</p>
                    </li>
                    <li>
                        <p class="fw-semibold text-dark mb-1">Bike Rentals</p>
                        <p class="mb-0">Rent-A-Ride kiosk at north gate · 24/7 support.</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h2 class="h5 fw-semibold mb-3"><i class="bi bi-activity me-2 text-success"></i>Services</h2>
                <ul class="list-unstyled d-grid gap-3 small text-muted mb-0">
                    <li>
                        <p class="fw-semibold text-dark mb-1">CityCare Hospital</p>
                        <p class="mb-0">24x7 emergency · Student health desk available.</p>
                    </li>
                    <li>
                        <p class="fw-semibold text-dark mb-1">Study Hub Library</p>
                        <p class="mb-0">Extended hours during exams · Quiet and group zones.</p>
                    </li>
                    <li>
                        <p class="fw-semibold text-dark mb-1">QuickFix Laundry</p>
                        <p class="mb-0">Express wash · Pickup & delivery across hostels.</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load guidance from API
    fetch('api/fetch_guidance.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                // Group by category
                const categories = {};
                data.data.forEach(item => {
                    if (!categories[item.category]) {
                        categories[item.category] = [];
                    }
                    categories[item.category].push(item);
                });
                
                // Update the sections
                const sections = {
                    'food': document.querySelector('.col-lg-4:nth-child(1) .list-unstyled'),
                    'transport': document.querySelector('.col-lg-4:nth-child(2) .list-unstyled'),
                    'services': document.querySelector('.col-lg-4:nth-child(3) .list-unstyled')
                };
                
                Object.keys(sections).forEach(category => {
                    if (categories[category]) {
                        const list = sections[category];
                        list.innerHTML = '';
                        categories[category].forEach(item => {
                            const li = document.createElement('li');
                            li.innerHTML = `
                                <p class="fw-semibold text-dark mb-1">${item.title}</p>
                                <p class="mb-0">${item.description}</p>
                            `;
                            list.appendChild(li);
                        });
                    }
                });
                
                console.log('Guidance data loaded:', categories);
            }
        })
        .catch(error => console.error('Error loading guidance:', error));
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
