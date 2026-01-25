const contactButton = document.querySelector('[data-contact-id]');
if (contactButton) {
    contactButton.addEventListener('click', async () => {
        const id = contactButton.getAttribute('data-contact-id');
        contactButton.disabled = true;
        contactButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending';
        try {
            const result = await NearBy.fetchJSON('api/contact/request.php', {
                method: 'POST',
                body: {accommodation_id: id}
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
