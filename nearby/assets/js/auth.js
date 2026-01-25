import { validateForm } from './forms.js';

const loginForm = document.querySelector('#loginForm');
const registerForm = document.querySelector('#registerForm');

if (loginForm) {
    const loginEmailInput = loginForm.querySelector('#loginEmail');
    const loginRoleSelect = loginForm.querySelector('#loginRole');
    if (loginEmailInput && loginRoleSelect) {
        const syncLoginEmailRequirement = () => {
            const requireDomain = loginRoleSelect.value === 'junior';
            loginEmailInput.dataset.requireMitsEmail = requireDomain ? 'true' : 'false';
            if (!requireDomain) {
                loginEmailInput.setCustomValidity('');
            }
        };

        syncLoginEmailRequirement();
        loginRoleSelect.addEventListener('change', syncLoginEmailRequirement);
    }
}

if (registerForm) {
    const registerEmailInput = registerForm.querySelector('#regEmail');
    const userCategorySelect = registerForm.querySelector('#regUserCategory');
    if (registerEmailInput && userCategorySelect) {
        const syncRegisterEmailRequirement = () => {
            const requireDomain = userCategorySelect.value === 'student';
            registerEmailInput.dataset.requireMitsEmail = requireDomain ? 'true' : 'false';
            if (!requireDomain) {
                registerEmailInput.setCustomValidity('');
            }
        };

        syncRegisterEmailRequirement();
        userCategorySelect.addEventListener('change', syncRegisterEmailRequirement);
    }
}

const handleSubmit = (form, endpoint) => {
    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!validateForm(form)) {
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalLabel = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing';

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        try {
            const result = await NearBy.fetchJSON(endpoint, {
                method: 'POST',
                body: payload
            });

            NearBy.showMessage(result.message || 'Success');

            if (result.redirect) {
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 600);
            }
        } catch (error) {
            NearBy.showMessage(error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalLabel;
        }
    });
};

handleSubmit(loginForm, 'api/auth/login.php');
handleSubmit(registerForm, 'api/auth/register.php');
