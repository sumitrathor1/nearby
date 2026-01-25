export const validateForm = (form) => {
    const elements = Array.from(form.elements).filter(el => el.hasAttribute('name'));
    let isValid = true;

    elements.forEach(el => {
        if (el.type === 'email' && el.dataset.requireMitsEmail === 'true') {
            const allowedDomain = '@mitsgwl.ac.in';
            const isCollegeEmail = el.value.toLowerCase().endsWith(allowedDomain);
            if (!isCollegeEmail) {
                el.setCustomValidity('Use your @mitsgwl.ac.in college email');
            } else {
                el.setCustomValidity('');
            }
        } else if (el.type === 'email') {
            el.setCustomValidity('');
        }

        if (!el.checkValidity()) {
            isValid = false;
        }

        el.addEventListener('input', () => {
            el.setCustomValidity('');
            el.reportValidity();
        }, { once: true });
    });

    if (!isValid) {
        form.classList.add('was-validated');
    }

    return isValid;
};
