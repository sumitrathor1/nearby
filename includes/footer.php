</div>
</main>
<footer class="glass-footer py-5 mt-auto">
    <div class="container">
        <div class="row gy-4 align-items-start footer-top">
            <div class="col-lg-3 text-center text-lg-start">
                <div class="d-inline-flex align-items-center gap-3 mb-3">
                    <img src="assets/images/nearby_image.png" alt="NearBy logo" class="footer-logo">
                </div>
                <p class="footer-copy mb-3">
                    Discover vetted rooms, apartments, and co-living spaces designed for students and young
                    professionals.
                </p>
                <p class="footer-maintained small mb-0">Platform managed with care by the NearBy Support Team.</p>
            </div>
            <div class="col-sm-6 col-lg-2">
                <h6 class="footer-heading">Support</h6>
                <ul class="footer-list">
                    <li><span>24×7 chat support</span></li>
                    <li><a href="mailto:support@nearbyhousing.com">Send us an email</a></li>
                    <li><a href="guidance.php">Housing guidance</a></li>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-2">
                <h6 class="footer-heading">Quick links</h6>
                <ul class="footer-list">
                    <li><a href="search.php">For renters</a></li>
                    <li><a href="register.php">Create account</a></li>
                    <li><a href="login.php">Member login</a></li>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-2">
                <h6 class="footer-heading">Important links</h6>
                <ul class="footer-list">
                    <li><a href="#">Privacy policy</a></li>
                    <li><a href="#">Terms of use</a></li>
                    <li><a href="guidance.php">FAQ</a></li>
                    <li><a href="login.php">My account</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-3">
                <h6 class="footer-heading">Download app</h6>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a class="footer-store" href="#" aria-label="Download on Play Store">Play Store</a>
                    <a class="footer-store" href="#" aria-label="Download on App Store">App Store</a>
                </div>
                <div class="footer-social d-flex gap-2">
                    <a class="social-link" href="https://github.com/sumitrathor1/nearby" target="_blank"
                        rel="noopener noreferrer" aria-label="GitHub">
                        <i class="bi bi-github"></i>
                        <span class="visually-hidden">GitHub</span>
                    </a>

                    <a class="social-link" href="mailto:sumitrathor142272@gmail.com" target="_blank"
                        rel="noopener noreferrer" aria-label="Email">
                        <i class="bi bi-envelope-fill"></i>
                        <span class="visually-hidden">Email</span>
                    </a>

                    <a class="social-link" href="https://www.linkedin.com/in/sumitrathor" target="_blank"
                        rel="noopener noreferrer" aria-label="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                        <span class="visually-hidden">LinkedIn</span>
                    </a>

                    <a class="social-link" href="https://twitter.com/nearbyhousing" target="_blank"
                        rel="noopener noreferrer" aria-label="X (Twitter)">
                        <i class="bi bi-twitter-x"></i>
                        <span class="visually-hidden">X (Twitter)</span>
                    </a>

                    <a class="social-link" href="https://instagram.com/nearbyhousing" target="_blank"
                        rel="noopener noreferrer" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                        <span class="visually-hidden">Instagram</span>
                    </a>

                    <a class="social-link" href="https://facebook.com/nearbyhousing" target="_blank"
                        rel="noopener noreferrer" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                        <span class="visually-hidden">Facebook</span>
                    </a>
                </div>


            </div>
        </div>
        <div class="footer-divider"></div>
        <div class="footer-bottom d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
            <span class="small">Website secured and maintained by <strong>NearBy Web &amp; Security</strong></span>
            <span class="small">© <?= date('Y') ?> NearBy Student Housing. All rights reserved.</span>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTopBtn" class="btn btn-primary back-to-top-btn" aria-label="Back to top">
    <i class="bi bi-chevron-up"></i>
</button>

<?php if (!empty($enableChatbot)): ?>
    <?php include __DIR__ . '/chatbot-widget.php'; ?>
<?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js"></script>
<?php if (!empty($pageScripts)): ?>
        <?php foreach ($pageScripts as $scriptPath): ?>
                <script src="<?= htmlspecialchars($scriptPath) ?>" type="module"></script>
        <?php endforeach; ?>
<?php endif; ?>
</body>

</html>