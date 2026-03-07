<?php 
$pageTitle = "FAQ";
require 'includes/header.php'; 
?>

<main class="container py-5">
    <div class="glass-card p-4 p-md-5">
        <h1 class="mb-4 text-center">Frequently Asked Questions</h1>

        <p class="text-muted text-center mb-5">
            Find answers to common questions about accounts, listings, and support.
        </p>

        <div class="accordion" id="faqAccordion">

            <!-- Account Section -->
            <h5 class="mt-4 mb-3">Account</h5>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                        Is registration required?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, creating an account allows you to contact property owners, save listings, and manage your activity on the platform.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                        How do I update my account information?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        You can update your profile information from your account dashboard after logging in.
                    </div>
                </div>
            </div>

            <!-- Listings Section -->
            <h5 class="mt-5 mb-3">Listings</h5>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                        How do I search for rooms?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Use the search feature on the homepage to browse verified listings based on your preferred location and budget.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                        Are the listings verified?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We strive to ensure listings are accurate and verified, but users should independently confirm details before making decisions.
                    </div>
                </div>
            </div>

            <!-- Support Section -->
            <h5 class="mt-5 mb-3">Support</h5>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive">
                        How can I contact support?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We provide 24×7 chat support and email assistance. You can reach us anytime for help regarding listings or account issues.
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require 'includes/footer.php'; ?>