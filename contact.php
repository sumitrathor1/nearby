<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3 text-center">Contact Us</h4>
                    <p class="text-muted text-center">
                        Have a question or need assistance? Feel free to contact us.
                    </p>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">
                            ✅ Thank you! Your message has been submitted successfully.
                        </div>
                    <?php elseif (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            ❌ Something went wrong. Please try again.
                        </div>
                    <?php endif; ?>

                    <form method="post" action="submit_contact.php">
                        <div class="mb-3">
                            <label class="form-label">Name (optional)</label>
                            <input type="text" name="name" class="form-control" placeholder="Your name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="Your email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject (optional)</label>
                            <input type="text" name="subject" class="form-control" placeholder="Subject">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
