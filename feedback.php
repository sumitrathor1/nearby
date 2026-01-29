<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3 text-center">We value your feedback</h4>
                    <p class="text-muted text-center">
                        Share your suggestions, report issues, or tell us how we can improve.
                    </p>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">
                            ✅ Thank you! Your feedback has been submitted successfully.
                        </div>
                    <?php elseif (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            ❌ Something went wrong. Please try again.
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="submit_feedback.php">
                        <div class="mb-3">
                            <label class="form-label">Name (optional)</label>
                            <input type="text" name="name" class="form-control" placeholder="Your name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email (optional)</label>
                            <input type="email" name="email" class="form-control" placeholder="Your email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Feedback Type</label>
                            <select name="feedback_type" class="form-select" required>
                                <option value="">Select type</option>
                                <option>Suggestion</option>
                                <option>Bug</option>
                                <option>General</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>