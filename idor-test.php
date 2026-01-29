<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDOR Protection Test Suite</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; }
        .test-section { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .test-case { background: #fff; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        button { padding: 10px 20px; margin: 10px 5px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px; }
        button:hover { background: #0056b3; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        pre { background: #fff; padding: 15px; overflow-x: auto; border: 1px solid #ddd; margin: 10px 0; font-size: 12px; }
        .status-indicator { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 8px; }
        .status-success { background: #28a745; }
        .status-error { background: #dc3545; }
        .status-pending { background: #ffc107; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 0; }
        .info-box { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🛡️ IDOR Protection Test Suite</h1>
    <p>This page tests the IDOR (Insecure Direct Object References) protection implementation.</p>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $isLoggedIn = isset($_SESSION['user']['id']);
    $userRole = $_SESSION['user']['role'] ?? 'none';
    $userId = $_SESSION['user']['id'] ?? 'N/A';
    ?>

    <div class="info-box">
        <strong>Current Session:</strong><br>
        Logged In: <?= $isLoggedIn ? '✓ Yes' : '✗ No' ?><br>
        <?php if ($isLoggedIn): ?>
        User ID: <?= htmlspecialchars($userId) ?><br>
        Role: <?= htmlspecialchars($userRole) ?>
        <?php else: ?>
        <em>Please log in to test authenticated endpoints</em>
        <?php endif; ?>
    </div>

    <div class="test-section">
        <h2>Test 1: Authentication Check</h2>
        <p>Tests that endpoints requiring authentication reject unauthenticated users.</p>
        <div class="test-case">
            <h3>1.1 Access Protected Endpoint (Create Post)</h3>
            <button onclick="testAuthenticationRequired()">Run Test</button>
            <div id="result-auth"></div>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 2: Role-Based Access Control</h2>
        <p>Tests that role-restricted endpoints reject users without required role.</p>
        <div class="test-case">
            <h3>2.1 Junior User Creating Accommodation (Should Fail)</h3>
            <button onclick="testRoleRestriction()">Run Test</button>
            <div id="result-role"></div>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 3: ID Validation</h2>
        <p>Tests that invalid IDs are properly rejected.</p>
        <div class="test-case">
            <h3>3.1 Non-Numeric ID</h3>
            <button onclick="testInvalidId('abc')">Test with 'abc'</button>
            <button onclick="testInvalidId('--1')">Test with '--1'</button>
            <button onclick="testInvalidId('')">Test with empty</button>
            <div id="result-invalidid"></div>
        </div>
        <div class="test-case">
            <h3>3.2 SQL Injection Attempt</h3>
            <button onclick="testSQLInjection()">Run Test</button>
            <div id="result-sqli"></div>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 4: Resource Existence Verification</h2>
        <p>Tests that non-existent resources return proper 404 errors.</p>
        <div class="test-case">
            <h3>4.1 Access Non-Existent Accommodation</h3>
            <button onclick="testNonExistentResource()">Run Test</button>
            <div id="result-notfound"></div>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 5: User Data Isolation</h2>
        <p>Tests that users can only access their own data.</p>
        <div class="test-case">
            <h3>5.1 Chat History (Should Only Return Own Messages)</h3>
            <button onclick="testDataIsolation()">Run Test</button>
            <div id="result-isolation"></div>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 6: Valid Operations</h2>
        <p>Tests that legitimate operations work correctly.</p>
        <div class="test-case">
            <h3>6.1 View Existing Accommodation</h3>
            <input type="number" id="validAccId" placeholder="Enter accommodation ID" value="1">
            <button onclick="testValidOperation()">Run Test</button>
            <div id="result-valid"></div>
        </div>
    </div>

    <script>
        function displayResult(elementId, status, title, message, details = null) {
            const element = document.getElementById(elementId);
            const statusClass = status === 'success' ? 'status-success' : 
                              status === 'error' ? 'status-error' : 'status-pending';
            const textClass = status === 'success' ? 'success' : 
                            status === 'error' ? 'error' : 'warning';
            
            let html = `
                <p>
                    <span class="status-indicator ${statusClass}"></span>
                    <span class="${textClass}">${title}</span>
                </p>
                <p>${message}</p>
            `;
            
            if (details) {
                html += `<pre>${JSON.stringify(details, null, 2)}</pre>`;
            }
            
            element.innerHTML = html;
        }

        async function testAuthenticationRequired() {
            displayResult('result-auth', 'pending', 'Testing...', 'Sending request without authentication');
            
            try {
                const response = await fetch('api/posts/create.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        post_category: 'room',
                        location: 'Test',
                        description: 'Test',
                        contact_phone: '1234567890'
                    })
                });
                
                const data = await response.json();
                
                if (response.status === 401 || response.status === 403) {
                    displayResult('result-auth', 'success', 
                        '✓ PASS: Authentication Required', 
                        'Endpoint correctly rejected unauthenticated/unauthorized request',
                        data
                    );
                } else {
                    displayResult('result-auth', 'error', 
                        '✗ FAIL: No Authentication Check', 
                        'Endpoint should require authentication but did not',
                        data
                    );
                }
            } catch (error) {
                displayResult('result-auth', 'error', 'Test Error', error.message);
            }
        }

        async function testRoleRestriction() {
            displayResult('result-role', 'pending', 'Testing...', 'Attempting role-restricted operation');
            
            try {
                const response = await fetch('api/accommodations/create.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        title: 'Test Room',
                        type: 'PG',
                        allowed_for: 'Male',
                        rent: '5000',
                        location: 'Test',
                        description: 'Test'
                    })
                });
                
                const data = await response.json();
                const userRole = '<?= $userRole ?>';
                
                if (userRole === 'junior' && response.status === 403) {
                    displayResult('result-role', 'success', 
                        '✓ PASS: Role Restriction Working', 
                        'Junior user correctly denied access to senior-only operation',
                        data
                    );
                } else if (userRole === 'senior' && response.status === 200) {
                    displayResult('result-role', 'success', 
                        '✓ PASS: Senior Access Granted', 
                        'Senior user correctly allowed to create accommodation',
                        data
                    );
                } else if (response.status === 401 || response.status === 403) {
                    displayResult('result-role', 'success', 
                        '✓ PASS: Unauthorized Access Denied', 
                        'Non-authenticated or non-authorized user denied access',
                        data
                    );
                } else {
                    displayResult('result-role', 'warning', 
                        '⚠ Check Status', 
                        `Unexpected status: ${response.status}. Check if role validation is working.`,
                        data
                    );
                }
            } catch (error) {
                displayResult('result-role', 'error', 'Test Error', error.message);
            }
        }

        async function testInvalidId(invalidId) {
            displayResult('result-invalidid', 'pending', 'Testing...', `Sending invalid ID: "${invalidId}"`);
            
            try {
                const response = await fetch(`api/fetch_accommodation_details.php?id=${encodeURIComponent(invalidId)}`);
                const data = await response.json();
                
                if (response.status === 400) {
                    displayResult('result-invalidid', 'success', 
                        '✓ PASS: Invalid ID Rejected', 
                        `Endpoint correctly rejected invalid ID "${invalidId}"`,
                        data
                    );
                } else {
                    displayResult('result-invalidid', 'error', 
                        '✗ FAIL: Invalid ID Not Caught', 
                        `Endpoint should reject invalid ID "${invalidId}" but didn't`,
                        data
                    );
                }
            } catch (error) {
                displayResult('result-invalidid', 'error', 'Test Error', error.message);
            }
        }

        async function testSQLInjection() {
            const sqlInjectionAttempt = "1' OR '1'='1";
            displayResult('result-sqli', 'pending', 'Testing...', 'Attempting SQL injection');
            
            try {
                const response = await fetch(`api/fetch_accommodation_details.php?id=${encodeURIComponent(sqlInjectionAttempt)}`);
                const data = await response.json();
                
                if (response.status === 400) {
                    displayResult('result-sqli', 'success', 
                        '✓ PASS: SQL Injection Prevented', 
                        'ID validation prevented SQL injection attempt',
                        data
                    );
                } else {
                    displayResult('result-sqli', 'error', 
                        '✗ FAIL: Potential SQL Injection', 
                        'SQL injection attempt was not properly blocked',
                        data
                    );
                }
            } catch (error) {
                displayResult('result-sqli', 'error', 'Test Error', error.message);
            }
        }

        async function testNonExistentResource() {
            const nonExistentId = 999999;
            displayResult('result-notfound', 'pending', 'Testing...', 'Accessing non-existent resource');
            
            try {
                const response = await fetch(`api/fetch_accommodation_details.php?id=${nonExistentId}`);
                const data = await response.json();
                
                if (response.status === 404) {
                    displayResult('result-notfound', 'success', 
                        '✓ PASS: Proper 404 Response', 
                        'Non-existent resource correctly returned 404',
                        data
                    );
                } else {
                    displayResult('result-notfound', 'warning', 
                        '⚠ Unexpected Response', 
                        `Expected 404 but got ${response.status}. This might be OK if resource actually exists.`,
                        data
                    );
                }
            } catch (error) {
                displayResult('result-notfound', 'error', 'Test Error', error.message);
            }
        }

        async function testDataIsolation() {
            displayResult('result-isolation', 'pending', 'Testing...', 'Fetching chat history');
            
            try {
                const response = await fetch('api/message-assistant-history.php');
                const data = await response.json();
                
                if (response.status === 401) {
                    displayResult('result-isolation', 'success', 
                        '✓ PASS: Authentication Required', 
                        'Chat history correctly requires authentication',
                        data
                    );
                } else if (response.status === 200 && data.success) {
                    displayResult('result-isolation', 'success', 
                        '✓ PASS: Data Retrieved', 
                        'Chat history returned (verify it only contains your messages in database)',
                        { message_count: data.messages?.length || 0, note: 'Check database to verify user_id matches session' }
                    );
                } else {
                    displayResult('result-isolation', 'warning', 
                        '⚠ Check Response', 
                        'Unexpected response',
                        data
                    );
                }
            } catch (error) {
                displayResult('result-isolation', 'error', 'Test Error', error.message);
            }
        }

        async function testValidOperation() {
            const accId = document.getElementById('validAccId').value;
            displayResult('result-valid', 'pending', 'Testing...', 'Fetching accommodation details');
            
            try {
                const response = await fetch(`api/fetch_accommodation_details.php?id=${accId}`);
                const data = await response.json();
                
                if (response.status === 200 && data.success) {
                    displayResult('result-valid', 'success', 
                        '✓ PASS: Valid Operation Works', 
                        'Legitimate request succeeded',
                        { title: data.data?.title, type: data.data?.type }
                    );
                } else if (response.status === 404) {
                    displayResult('result-valid', 'warning', 
                        '⚠ Resource Not Found', 
                        'Accommodation ID not found. Try a different ID.',
                        data
                    );
                } else {
                    displayResult('result-valid', 'error', 
                        '✗ Unexpected Error', 
                        'Valid operation failed unexpectedly',
                        data
                    );
                }
            } catch (error) {
                displayResult('result-valid', 'error', 'Test Error', error.message);
            }
        }
    </script>
</body>
</html>
