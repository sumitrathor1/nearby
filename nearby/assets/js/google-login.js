import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.7.0/firebase-app.js';
import { getAuth, GoogleAuthProvider, signInWithPopup, signOut } from 'https://www.gstatic.com/firebasejs/12.7.0/firebase-auth.js';

const firebaseConfig = {
    apiKey: 'AIzaSyAHdxzcFs9rFsMozKV8n5QMkD8Tul5HOo8',
    authDomain: 'nearby-a312c.firebaseapp.com',
    projectId: 'nearby-a312c',
    storageBucket: 'nearby-a312c.firebasestorage.app',
    messagingSenderId: '756788656688',
    appId: '1:756788656688:web:8c463a6acf46c3fdcf8bf5'
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
auth.useDeviceLanguage();

const provider = new GoogleAuthProvider();
provider.setCustomParameters({prompt: 'select_account'});

const googleButton = document.querySelector('[data-google-login]');

if (googleButton) {
    const originalLabel = googleButton.innerHTML;
    const setLoadingState = (isLoading) => {
        googleButton.disabled = isLoading;
        if (isLoading) {
            googleButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span><span>Connecting...</span>';
        } else {
            googleButton.innerHTML = originalLabel;
        }
    };

    const ensureNearBy = () => {
        if (!window.NearBy) {
            throw new Error('Unable to reach NearBy services. Please retry.');
        }
    };

    googleButton.addEventListener('click', async () => {
        try {
            ensureNearBy();
            setLoadingState(true);
            const result = await signInWithPopup(auth, provider);
            const user = result?.user;
            const email = (user?.email || '').toLowerCase();
            if (!email) {
                throw new Error('Google account did not return an email address.');
            }
            const idToken = await user.getIdToken(true);
            const response = await window.NearBy.fetchJSON('api/auth/google-login.php', {
                method: 'POST',
                body: {email, idToken}
            });
            window.NearBy.showMessage(response.message || 'Login successful');
            if (response.redirect) {
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 600);
            }
        } catch (error) {
            const code = error?.code || '';
            let message = error?.message || 'Unable to continue with Google right now.';
            if (code === 'auth/popup-closed-by-user') {
                message = 'Sign-in popup closed before continuing.';
            } else if (code === 'auth/cancelled-popup-request') {
                message = 'Please finish the ongoing sign-in attempt first.';
            }
            if (window?.NearBy) {
                window.NearBy.showMessage(message, 'danger');
            } else {
                console.error(message);
            }
        } finally {
            setLoadingState(false);
            try {
                await signOut(auth);
            } catch (signOutError) {
                console.warn('Failed to sign out from Firebase session', signOutError);
            }
        }
    });
} else {
    console.warn('Google login button not found on this page.');
}
