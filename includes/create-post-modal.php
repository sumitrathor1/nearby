<?php if (!isset($currentUser) || !$currentUser): ?>
    <?php return; ?>
<?php endif; ?>
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h1 class="modal-title fs-5 fw-semibold" id="createPostModalLabel">Create a Community Post</h1>
                    <p class="text-muted small mb-0">Share a room vacancy or nearby service so students can connect quickly.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <form id="createPostForm" class="needs-validation" novalidate>
                    <?= csrfField() ?>
                    <div class="mb-4">
                        <span class="form-label d-block mb-2">What would you like to post?</span>
                        <div class="d-flex flex-wrap gap-3" role="group" aria-label="Post type selector">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="post_category" id="postTypeRoom" value="room" checked>
                                <label class="form-check-label" for="postTypeRoom">Room Rent</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="post_category" id="postTypeService" value="service">
                                <label class="form-check-label" for="postTypeService">Local Service</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3" data-room-fields>
                        <div class="col-md-6">
                            <label class="form-label" for="postAccommodationType">Accommodation Type</label>
                            <select class="form-select" id="postAccommodationType" name="accommodation_type" required>
                                <option value="">Choose type</option>
                                <option value="PG">PG</option>
                                <option value="Flat">Flat</option>
                                <option value="Room">Room</option>
                                <option value="Hostel">Hostel</option>
                            </select>
                            <div class="invalid-feedback">Select the accommodation category.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="postAllowedFor">Allowed For</label>
                            <select class="form-select" id="postAllowedFor" name="allowed_for" required>
                                <option value="">Choose audience</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Family">Family</option>
                            </select>
                            <div class="invalid-feedback">Choose who can stay here.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="postRent">Monthly Rent (₹)</label>
                            <input type="number" class="form-control" id="postRent" name="rent_or_price" data-room-price min="500" step="100" required>
                            <div class="invalid-feedback">Enter the monthly rent.</div>
                        </div>
                        <div class="col-12">
                            <span class="form-label">Facilities</span>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                <?php
                                $facilities = ['Wi-Fi', 'Food', 'Water', 'Electricity', 'Parking', 'CCTV', 'Power Backup'];
                                foreach ($facilities as $facility):
                                ?>
                                    <label class="form-check form-check-inline facility-checkbox">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="<?= $facility ?>">
                                        <span class="form-check-label"><?= $facility ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 d-none" data-service-fields>
                        <div class="col-md-6">
                            <label class="form-label" for="postServiceType">Service Type</label>
                            <select class="form-select" id="postServiceType" name="service_type">
                                <option value="">Choose service</option>
                                <option value="tiffin">Tiffin / Mess</option>
                                <option value="gas">Gas Provider</option>
                                <option value="milk">Milk (Doodh)</option>
                                <option value="sabji">Vegetable (Sabji)</option>
                                <option value="other">Other Local Service</option>
                            </select>
                            <div class="invalid-feedback">Select a service type.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="postServicePrice">Price (optional)</label>
                            <input type="number" class="form-control" id="postServicePrice" name="rent_or_price" data-service-price min="0" step="50" placeholder="Leave blank if on request" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="postAvailability">Availability / Timings</label>
                            <input type="text" class="form-control" id="postAvailability" name="availability_time" placeholder="Example: Daily 7 AM - 10 AM & 6 PM - 9 PM">
                            <div class="invalid-feedback">Share when the service is available.</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label" for="postLocation">Location / Landmark</label>
                            <input type="text" class="form-control" id="postLocation" name="location" required>
                            <div class="invalid-feedback">Location helps students plan commute.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="postContact">Contact Number</label>
                            <input type="tel" class="form-control" id="postContact" name="contact_phone" pattern="[0-9+\-\s]{7,}" required placeholder="Example: +91 98765 43210">
                            <div class="invalid-feedback">Provide a reachable phone number.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="postDescription">Description</label>
                            <textarea class="form-control" id="postDescription" name="description" rows="4" required placeholder="Mention highlights, rules, or special offers."></textarea>
                            <div class="invalid-feedback">Share details to help others decide faster.</div>
                        </div>
                    </div>

                    <div class="alert alert-secondary bg-opacity-10 border-0 mt-4" role="note">
                        <i class="bi bi-shield-check me-2"></i>Your post will appear instantly. Contact details are visible only to logged-in members.
                    </div>
                    <div class="modal-footer border-0 px-0 pt-3">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Publish Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
