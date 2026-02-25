document.addEventListener("DOMContentLoaded", function () {

    // ============================
    // CONTACT OWNER BUTTON
    // ============================

    const contactButton = document.querySelector('[data-contact-id]');

    if (contactButton) {
        contactButton.addEventListener('click', async () => {
            const id = contactButton.getAttribute('data-contact-id');

            contactButton.disabled = true;
            contactButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Sending';

            try {
                const result = await NearBy.fetchJSON('api/contact/request.php', {
                    method: 'POST',
                    body: { accommodation_id: id }
                });

                NearBy.showMessage(result.message);

            } catch (error) {
                NearBy.showMessage(error.message, 'danger');
            } finally {
                contactButton.disabled = false;
                contactButton.innerHTML = 'Contact Owner';
            }
        });
    }


    // ============================
    // REVIEWS SYSTEM
    // ============================

    const postIdInput = document.getElementById('post-id');

    if (postIdInput) {
        loadReviews();
    }

    const reviewForm = document.getElementById('review-form');

    if (reviewForm) {
        reviewForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const postId = document.getElementById('post-id').value;
            const rating = document.getElementById('rating').value;
            const review = document.getElementById('review-text').value;

            if (!rating || !review) {
                NearBy.showMessage("Please fill all fields", "danger");
                return;
            }

            try {
                const result = await NearBy.fetchJSON('api/posts/add_review.php', {
                    method: 'POST',
                    body: {
                        post_id: postId,
                        rating: rating,
                        review: review
                    }
                });

                NearBy.showMessage(result.message);

                if (result.success) {
                    reviewForm.reset();
                    loadReviews();
                }

            } catch (error) {
                NearBy.showMessage(error.message, 'danger');
            }
        });
    }

});


// ============================
// LOAD REVIEWS FUNCTION
// ============================

async function loadReviews() {
    try {
        const postId = document.getElementById('post-id').value;

        const result = await NearBy.fetchJSON(
            `api/posts/fetch_reviews.php?post_id=${postId}`
        );

        if (!result.success) return;

        // Average Rating
        const avgContainer = document.getElementById('average-rating');
        if (avgContainer) {
            avgContainer.innerHTML =
                `<strong>${result.average} ⭐</strong> (${result.total} reviews)`;
        }

        // Reviews List
        const listContainer = document.getElementById('reviews-list');
        if (!listContainer) return;

        if (result.reviews.length === 0) {
            listContainer.innerHTML = "<p>No reviews yet.</p>";
            return;
        }

        let html = "";

        result.reviews.forEach(r => {
            html += `
                <div class="review-item mb-3 p-2 border rounded">
                    <strong>${r.name}</strong>
                    <div>${'⭐'.repeat(r.rating)}</div>
                    <p>${r.review}</p>
                    <small class="text-muted">${r.created_at}</small>
                </div>
            `;
        });

        listContainer.innerHTML = html;

    } catch (error) {
        console.error("Error loading reviews:", error);
    }
}
