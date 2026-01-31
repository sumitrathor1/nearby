import { escapeHtml } from './ui.js';

(() => {
    const body = document.body;
    if (!body) {
        return;
    }

    const currentPath = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });

    const buildAlert = (message, type = 'success') => {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
            <div class="alert alert-${type} alert-glass alert-dismissible fade show" role="alert">
                ${escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        return wrapper.firstElementChild;
    };

    const showMessage = (message, type = 'success') => {
        const sweetAlert = window.Swal;
        if (sweetAlert) {
            const iconMap = {success: 'success', danger: 'error', warning: 'warning', info: 'info'};
            const icon = iconMap[type] || 'info';
            const titleMap = {
                success: 'Success',
                error: 'Oops!',
                warning: 'Please check',
                info: 'Heads up'
            };
            sweetAlert.fire({
                icon,
                title: titleMap[icon] || 'Notice',
                text: message,
                confirmButtonColor: '#0d6efd'
            });
            return;
        }

        const target = document.querySelector('[data-app-alerts]') || document.querySelector('.container');
        if (!target) {
            return;
        }
        const alertEl = buildAlert(message, type);
        target.prepend(alertEl);
        setTimeout(() => {
            const alertInstance = bootstrap.Alert.getOrCreateInstance(alertEl);
            alertInstance.close();
        }, 6000);
    };

    const fetchJSON = async (url, options = {}) => {
        const defaultHeaders = {'Accept': 'application/json'};
        
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        options.headers = options.headers ? {...defaultHeaders, ...options.headers} : defaultHeaders;
        
        // Add CSRF token to headers for POST requests
        if (csrfToken && (options.method === 'POST' || options.method === 'PUT' || options.method === 'DELETE')) {
            options.headers['X-CSRF-Token'] = csrfToken;
        }
        
        if (options.body && !(options.body instanceof FormData)) {
            options.headers['Content-Type'] = 'application/json';
            // Add CSRF token to JSON body if not already present
            if (csrfToken && typeof options.body === 'object' && !options.body.csrf_token) {
                options.body.csrf_token = csrfToken;
            }
            options.body = JSON.stringify(options.body);
        } else if (options.body instanceof FormData && csrfToken) {
            // Add CSRF token to FormData if not already present
            if (!options.body.has('csrf_token')) {
                options.body.append('csrf_token', csrfToken);
            }
        }

        const response = await fetch(url, options);
        const rawText = await response.text();

        let data;
        try {
            const normalized = rawText.trim();
            data = normalized ? JSON.parse(normalized) : {};
        } catch (parseError) {
            data = null;
        }

        if (!data) {
            data = {success: false, message: rawText.trim() || 'Invalid server response'};
        }

        const isSuccess = response.ok && data.success !== false;
        if (!isSuccess) {
            const errorMessage = data.message || response.statusText || 'Something went wrong';
            const error = new Error(errorMessage);
            error.status = response.status;
            error.body = rawText;
            throw error;
        }

        return data;
    };

    window.NearBy = {
        fetchJSON,
        showMessage
    };

    // Back to Top Button Functionality
    const backToTopBtn = document.getElementById('backToTopBtn');
    if (backToTopBtn) {
        // Show/hide button based on scroll position
        const toggleBackToTopBtn = () => {
            if (window.pageYOffset > 300) { // Show after scrolling 300px
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        };

        // Smooth scroll to top
        const scrollToTop = () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };

        // Event listeners
        window.addEventListener('scroll', toggleBackToTopBtn);
        backToTopBtn.addEventListener('click', scrollToTop);

        // Initial check
        toggleBackToTopBtn();
    }
})();
