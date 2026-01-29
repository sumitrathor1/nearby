<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSRF Protection Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .test-section { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        button { padding: 10px 20px; margin: 10px 5px; cursor: pointer; }
        pre { background: #fff; padding: 15px; overflow-x: auto; border: 1px solid #ddd; }
        .token-display { background: #fff; padding: 10px; margin: 10px 0; word-break: break-all; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <h1>🛡️ CSRF Protection Test Suite</h1>
    <p>This page tests the CSRF protection implementation in the NearBy application.</p>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/includes/helpers/csrf.php';
    $token = getCSRFToken();
    ?>

    <div class="test-section">
        <h2>1. Current CSRF Token</h2>
        <p>Your current session CSRF token:</p>
        <div class="token-display"><?= htmlspecialchars($token) ?></div>
        <p><small>Token expires: <?= date('Y-m-d H:i:s', $_SESSION['csrf_token_time'] + 3600) ?></small></p>
    </div>

    <div class="test-section">
        <h2>2. Test Valid Request</h2>
        <p>This test will send a POST request WITH a valid CSRF token.</p>
        <button onclick="testValidRequest()">Test Valid Request ✓</button>
        <div id="result-valid"></div>
    </div>

    <div class="test-section">
        <h2>3. Test Missing Token</h2>
        <p>This test will send a POST request WITHOUT a CSRF token.</p>
        <button onclick="testMissingToken()">Test Missing Token ✗</button>
        <div id="result-missing"></div>
    </div>

    <div class="test-section">
        <h2>4. Test Invalid Token</h2>
        <p>This test will send a POST request with an INVALID CSRF token.</p>
        <button onclick="testInvalidToken()">Test Invalid Token ✗</button>
        <div id="result-invalid"></div>
    </div>

    <div class="test-section">
        <h2>5. Implementation Summary</h2>
        <ul>
            <li>✅ CSRF helper functions created in <code>includes/helpers/csrf.php</code></li>
            <li>✅ All POST endpoints protected with CSRF validation</li>
            <li>✅ All HTML forms include CSRF tokens</li>
            <li>✅ JavaScript automatically includes CSRF tokens in AJAX requests</li>
            <li>✅ Tokens expire after 1 hour</li>
            <li>✅ Uses cryptographically secure random token generation</li>
            <li>✅ Timing-safe token comparison to prevent timing attacks</li>
        </ul>
    </div>

    <script>
        const CSRF_TOKEN = '<?= $token ?>';

        async function displayResult(elementId, success, message, details = null) {
            const element = document.getElementById(elementId);
            const statusClass = success ? 'success' : 'error';
            const statusIcon = success ? '✓' : '✗';
            let html = `<p class="${statusClass}"><strong>${statusIcon} ${message}</strong></p>`;
            if (details) {
                html += `<pre>${JSON.stringify(details, null, 2)}</pre>`;
            }
            element.innerHTML = html;
        }

        async function testValidRequest() {
            try {
                const response = await fetch('api/posts/create.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        csrf_token: CSRF_TOKEN,
                        post_category: 'room',
                        location: 'Test Location',
                        description: 'CSRF test post',
                        contact_phone: '1234567890',
                        rent_or_price: '5000'
                    })
                });
                const data = await response.json();
                
                // Even if validation succeeds, the request may fail due to other validation
                // What matters is that we DON'T get a 403 CSRF error
                if (response.status === 403 && data.error?.includes('CSRF')) {
                    displayResult('result-valid', false, 'CSRF validation failed unexpectedly!', data);
                } else {
                    displayResult('result-valid', true, 'CSRF token validated successfully! (Request passed CSRF check)', data);
                }
            } catch (error) {
                displayResult('result-valid', false, 'Request failed', { error: error.message });
            }
        }

        async function testMissingToken() {
            try {
                const response = await fetch('api/posts/create.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        post_category: 'room',
                        location: 'Test Location',
                        description: 'CSRF test post',
                        contact_phone: '1234567890',
                        rent_or_price: '5000'
                    })
                });
                const data = await response.json();
                
                if (response.status === 403) {
                    displayResult('result-missing', true, 'CSRF protection working! Request blocked as expected.', data);
                } else {
                    displayResult('result-missing', false, 'CSRF protection FAILED! Request should have been blocked.', data);
                }
            } catch (error) {
                displayResult('result-missing', false, 'Request failed', { error: error.message });
            }
        }

        async function testInvalidToken() {
            try {
                const response = await fetch('api/posts/create.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': 'invalid_token_12345'
                    },
                    body: JSON.stringify({
                        csrf_token: 'invalid_token_12345',
                        post_category: 'room',
                        location: 'Test Location',
                        description: 'CSRF test post',
                        contact_phone: '1234567890',
                        rent_or_price: '5000'
                    })
                });
                const data = await response.json();
                
                if (response.status === 403) {
                    displayResult('result-invalid', true, 'CSRF protection working! Invalid token rejected as expected.', data);
                } else {
                    displayResult('result-invalid', false, 'CSRF protection FAILED! Invalid token should have been rejected.', data);
                }
            } catch (error) {
                displayResult('result-invalid', false, 'Request failed', { error: error.message });
            }
        }
    </script>
</body>
</html>
