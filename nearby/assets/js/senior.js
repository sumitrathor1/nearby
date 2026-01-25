import { validateForm } from './forms.js';

const form = document.querySelector('#postAccommodation');
if (form) {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!validateForm(form)) {
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Posting';

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());
        if (formData.getAll('facilities[]').length) {
            payload.facilities = formData.getAll('facilities[]');
        }

        try {
            const result = await NearBy.fetchJSON('api/accommodations/create.php', {
                method: 'POST',
                body: payload
            });
            NearBy.showMessage(result.message);
            form.reset();
        } catch (error) {
            NearBy.showMessage(error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Post Accommodation';
            form.classList.remove('was-validated');
        }
    });
}
