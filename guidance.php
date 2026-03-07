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
            <div class="glass-card p-4 h-100 guidance-card">
                <h2 class="h5 fw-semibold mb-2">
                    <i class="bi bi-egg-fried me-2 text-success"></i>Food & Mess
                </h2>
                    <p class="small text-muted mb-3">Popular mess and tiffin services around campus.</p>
                <ul class="list-unstyled d-grid gap-3 small text-muted mb-0 guidance-list">
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
            <div class="glass-card p-4 h-100 guidance-card">
                <h2 class="h5 fw-semibold mb-3"><i class="bi bi-bus-front me-2 text-success"></i>Transport</h2>
                <ul class="list-unstyled d-grid gap-3 small text-muted mb-0 guidance-list">
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
            <div class="glass-card p-4 h-100 guidance-card">
                <h2 class="h5 fw-semibold mb-3"><i class="bi bi-activity me-2 text-success"></i>Services</h2>
                <ul class="list-unstyled d-grid gap-3 small text-muted mb-0 guidance-list">
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
        <div class="col-lg-4">
    <div class="glass-card p-4 h-100 guidance-card">
        <h2 class="h5 fw-semibold mb-3">
            <i class="bi bi-pencil-square me-2 text-success"></i>Stationery & Bookstores
        </h2>
        <ul class="list-unstyled d-grid gap-3 small text-muted mb-0 guidance-list">
            <li>
                <p class="fw-semibold text-dark mb-1">Campus Book Depot</p>
                <p class="mb-0">Engineering books, notebooks, and printing services.</p>
            </li>
            <li>
                <p class="fw-semibold text-dark mb-1">Student Stationery Hub</p>
                <p class="mb-0">Affordable stationery and project supplies.</p>
            </li>
        </ul>
    </div>
</div>

<div class="col-lg-4">
    <div class="glass-card p-4 h-100 guidance-card">
        <h2 class="h5 fw-semibold mb-3">
            <i class="bi bi-bank me-2 text-success"></i>ATMs & Banks
        </h2>
        <ul class="list-unstyled d-grid gap-3 small text-muted mb-0 guidance-list">
            <li>
                <p class="fw-semibold text-dark mb-1">SBI ATM</p>
                <p class="mb-0">Located near main campus gate.</p>
            </li>
            <li>
                <p class="fw-semibold text-dark mb-1">HDFC Bank Branch</p>
                <p class="mb-0">Full banking services for students.</p>
            </li>
        </ul>
    </div>
</div>

<div class="col-lg-4">
    <div class="glass-card p-4 h-100 guidance-card">
        <h2 class="h5 fw-semibold mb-3">
            <i class="bi bi-capsule me-2 text-success"></i>Medical Stores
        </h2>
        <ul class="list-unstyled d-grid gap-3 small text-muted mb-0 guidance-list">
            <li>
                <p class="fw-semibold text-dark mb-1">24x7 Pharmacy</p>
                <p class="mb-0">Emergency medicines available all night.</p>
            </li>
            <li>
                <p class="fw-semibold text-dark mb-1">HealthPlus Pharmacy</p>
                <p class="mb-0">Student discounts on prescriptions.</p>
            </li>
        </ul>
    </div>
</div>

<div class="col-lg-4">
    <div class="glass-card p-4 h-100 guidance-card">
        <h2 class="h5 fw-semibold mb-3">
            <i class="bi bi-cup-hot me-2 text-success"></i>Cafes
        </h2>
        <ul class="list-unstyled d-grid gap-3 small text-muted mb-0 guidance-list">
            <li>
                <p class="fw-semibold text-dark mb-1">Campus Brew Cafe</p>
                <p class="mb-0">Coffee, snacks, and student study seating.</p>
            </li>
            <li>
                <p class="fw-semibold text-dark mb-1">Evening Coffee Corner</p>
                <p class="mb-0">Popular late-night hangout for students.</p>
            </li>
        </ul>
    </div>
</div>

<div class="col-lg-4">
    <div class="glass-card p-4 h-100 guidance-card">
        <h2 class="h5 fw-semibold mb-3">
            <i class="bi bi-book me-2 text-success"></i>Study Spaces
        </h2>
        <ul class="list-unstyled d-grid gap-3 small text-muted mb-0 guidance-list">
            <li>
                <p class="fw-semibold text-dark mb-1">Community Study Hall</p>
                <p class="mb-0">Quiet environment with free Wi-Fi.</p>
            </li>
            <li>
                <p class="fw-semibold text-dark mb-1">Public Library</p>
                <p class="mb-0">Large reading rooms and exam preparation zones.</p>
            </li>
        </ul>
    </div>
    </div>
    
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100 guidance-card">
            <h2 class="h5 fw-semibold mb-3">
                <i class="bi bi-heart-pulse me-2 text-success"></i>Gym & Fitness
            </h2>
            <ul class="list-unstyled d-grid gap-3 small text-muted mb-0 guidance-list">
                <li>
                    <p class="fw-semibold text-dark mb-1">FitZone Gym</p>
                    <p class="mb-0">Affordable student memberships.</p>
                </li>
                <li>
                    <p class="fw-semibold text-dark mb-1">Yoga & Wellness Studio</p>
                    <p class="mb-0">Morning yoga and meditation sessions.</p>
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
                            li.classList.add('guidance-item');

li.innerHTML = `
    <div class="d-flex justify-content-between align-items-start gap-2">
        <div>
            <p class="fw-semibold text-dark mb-1">${item.title}</p>
            <p class="mb-1">${item.description}</p>
        </div>
        <a href="https://maps.google.com/?q=${encodeURIComponent(item.title)}" 
           target="_blank" 
           class="btn btn-sm btn-outline-success guidance-map-btn">
            <i class="bi bi-geo-alt"></i>
        </a>
    </div>
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
