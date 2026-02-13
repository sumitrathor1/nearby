import { validateForm } from './forms.js';

const forgotPasswordForm = document.querySelector('#forgotPasswordForm');

if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!validateForm(forgotPasswordForm)) {
            return;
        }

        const submitBtn = forgotPasswordForm.querySelector('button[type="submit"]');
        const originalLabel = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Sending...';

        const formData = new FormData(forgotPasswordForm);
        const payload = Object.fromEntries(formData.entries());

        try {
            const result = await NearBy.fetchJSON('api/auth/forgot-password.php', {
                method: 'POST',
                body: payload
            });

            NearBy.showMessage(result.message || 'Reset link sent to your email', 'success');

            // Optionally redirect after some time
            if (result.success) {
                forgotPasswordForm.reset();
            }
        } catch (error) {
            NearBy.showMessage(error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalLabel;
        }
    });
}
