# Security Guide for NearBy API

This document outlines the comprehensive security measures implemented in the NearBy API and provides guidelines for secure usage.

## 🛡️ Security Overview

The NearBy API implements multiple layers of security to protect user data and prevent common web vulnerabilities:

- **Input Validation & Sanitization**
- **SQL Injection Prevention**
- **Cross-Site Scripting (XSS) Protection**
- **Cross-Site Request Forgery (CSRF) Protection**
- **Rate Limiting & DDoS Protection**
- **Secure Authentication & Session Management**
- **Content Security Policies**

## 🔐 Authentication & Authorization

### Session-Based Authentication
The API uses PHP sessions for authentication with the following security measures:

```php
// Session security configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
session_regenerate_id(true); // Prevent session fixation
```

### Role-Based Access Control
Users are assigned roles with specific permissions:

- **Junior Students**: Can search and contact for accommodations
- **Senior Students**: Can create posts and provide services
- **Service Providers**: Can manage service listings

### Password Security
- **Hashing Algorithm**: Argon2ID with strong parameters
- **Minimum Requirements**: 6+ characters with letters and numbers
- **Storage**: Never stored in plain text

```php
// Secure password hashing
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536, // 64 MB
    'time_cost' => 4,       // 4 iterations
    'threads' => 3,         // 3 threads
]);
```

## 🚫 Input Validation & Sanitization

### Comprehensive Input Validation
All user inputs are validated using the security utility functions:

```php
// Example: Email validation
$emailValidation = validateEmail($input['email']);
if (!$emailValidation['valid']) {
    secureErrorResponse($emailValidation['error'], 400);
}

// Example: Text sanitization
$cleanText = sanitizeInput($userInput, $allowHtml = false);
```

### XSS Prevention
- **HTML Encoding**: All user content is HTML-encoded before output
- **Content Sanitization**: Dangerous HTML tags and scripts are removed
- **Output Escaping**: Context-aware escaping for different output contexts

```php
// XSS protection implementation
function sanitizeInput($input, $allowHtml = false) {
    // Remove null bytes and control characters
    $input = str_replace(["\0", "\x0B"], '', $input);
    
    if (!$allowHtml) {
        $input = strip_tags($input);
    }
    
    // Convert special characters to HTML entities
    return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
```

### SQL Injection Prevention
- **Prepared Statements**: All database queries use parameterized statements
- **Type Binding**: Explicit type binding for all parameters
- **Input Validation**: Data types and ranges are validated before queries

```php
// Secure database query example
$sql = 'SELECT * FROM users WHERE email = ? AND role = ?';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $email, $role);
mysqli_stmt_execute($stmt);
```

## 🔒 CSRF Protection

### Token-Based CSRF Protection
CSRF tokens are generated and validated for state-changing operations:

```php
// Generate CSRF token
function generateCSRFToken() {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

// Validate CSRF token
function validateCSRFToken($token) {
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    return !empty($sessionToken) && hash_equals($sessionToken, $token);
}
```

### Client-Side Implementation
```javascript
// Include CSRF token in requests
const csrfToken = sessionStorage.getItem('csrf_token');
fetch('/api/endpoint', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
    },
    body: JSON.stringify(data)
});
```

## ⚡ Rate Limiting

### Endpoint-Specific Rate Limits
Different endpoints have tailored rate limits based on their sensitivity:

| Endpoint | Limit | Window | Scope |
|----------|-------|--------|-------|
| Registration | 5 attempts | 5 minutes | Per IP |
| Login | 10 attempts | 5 minutes | Per IP |
| Post Creation | 5 posts | 1 hour | Per User |
| Chatbot | 30 messages | 5 minutes | Per User |
| Search | 60 requests | 1 minute | Per IP |

### Rate Limiting Implementation
```php
function checkRateLimit($identifier, $maxRequests = 60, $timeWindow = 60) {
    $cacheFile = sys_get_temp_dir() . '/nearby_rate_limit_' . md5($identifier);
    $now = time();
    
    if (!file_exists($cacheFile)) {
        file_put_contents($cacheFile, json_encode(['count' => 1, 'reset' => $now + $timeWindow]));
        return true;
    }
    
    $data = json_decode(file_get_contents($cacheFile), true);
    
    if ($now > $data['reset']) {
        file_put_contents($cacheFile, json_encode(['count' => 1, 'reset' => $now + $timeWindow]));
        return true;
    }
    
    if ($data['count'] >= $maxRequests) {
        return false;
    }
    
    $data['count']++;
    file_put_contents($cacheFile, json_encode($data));
    return true;
}
```

## 🤖 AI Chatbot Security

### Input Filtering
The AI chatbot implements additional security measures:

```php
// Prompt injection prevention
$suspiciousPatterns = [
    '/ignore\s+previous\s+instructions/i',
    '/system\s*:/i',
    '/assistant\s*:/i',
    '/\[INST\]/i',
    '/\<\|system\|\>/i',
];

foreach ($suspiciousPatterns as $pattern) {
    if (preg_match($pattern, $message)) {
        secureErrorResponse('Message contains invalid content', 422);
    }
}
```

### Response Filtering
Bot responses are scanned for sensitive information:

```php
// Filter sensitive content from bot responses
if (preg_match('/password|token|secret|key|api/i', $botReply)) {
    error_log('[Security] Sensitive content detected in bot response');
    $botReply = 'I apologize, but I cannot provide that information. Please contact support.';
}
```

## 🔍 Error Handling Security

### Secure Error Responses
Error messages are designed to be informative without exposing sensitive information:

```php
function secureErrorResponse($message, $httpCode = 400, $logMessage = null) {
    http_response_code($httpCode);
    
    // Log detailed error internally
    if ($logMessage) {
        error_log("[NearBy Security] {$logMessage}");
    }
    
    // Return only user-friendly message
    echo json_encode([
        'success' => false,
        'message' => $message,
        'timestamp' => date('c')
    ]);
    exit;
}
```

### Information Disclosure Prevention
- **Generic Error Messages**: Avoid exposing system details
- **Database Errors**: Never expose SQL errors to users
- **File Paths**: System paths are not included in responses
- **Stack Traces**: Debug information is logged, not returned

## 🌐 Content Security Policy

### HTTP Security Headers
The API implements security headers to prevent various attacks:

```php
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'');
```

## 📊 Security Monitoring

### Logging & Monitoring
Security events are logged for monitoring and analysis:

```php
// Security event logging
error_log("[NearBy Security] Failed login attempt from IP: {$clientIP} for email: {$email}");
error_log("[NearBy Security] Rate limit exceeded for identifier: {$identifier}");
error_log("[NearBy Security] Suspicious chatbot input detected: " . substr($message, 0, 100));
```

### Audit Trail
- **Authentication Events**: Login/logout attempts
- **Data Modifications**: Post creation/updates
- **Security Violations**: Rate limit breaches, suspicious inputs
- **Error Patterns**: Repeated failures or attacks

## 🔧 Security Configuration

### PHP Configuration
Recommended PHP security settings:

```ini
; Hide PHP version
expose_php = Off

; Disable dangerous functions
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

; Session security
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
session.cookie_samesite = "Strict"

; File upload security
file_uploads = Off
allow_url_fopen = Off
allow_url_include = Off
```

### Database Security
- **Least Privilege**: Database user has minimal required permissions
- **Connection Security**: SSL/TLS encryption for database connections
- **Regular Updates**: Database software kept up to date

## 🚨 Incident Response

### Security Incident Handling
1. **Detection**: Monitor logs for suspicious activities
2. **Assessment**: Evaluate the scope and impact
3. **Containment**: Implement immediate protective measures
4. **Investigation**: Analyze the incident thoroughly
5. **Recovery**: Restore normal operations securely
6. **Lessons Learned**: Update security measures

### Emergency Contacts
- **Technical Lead**: 24cd3dsu4@mitsgwl.ac.in
- **Security Team**: security@nearby.platform
- **Emergency Phone**: +91 7566868709

## 📋 Security Checklist

### For Developers
- [ ] All user inputs are validated and sanitized
- [ ] Database queries use prepared statements
- [ ] Error messages don't expose sensitive information
- [ ] Rate limiting is implemented for all endpoints
- [ ] CSRF tokens are used for state-changing operations
- [ ] Security headers are properly configured
- [ ] Sensitive operations are logged

### For API Users
- [ ] Use HTTPS in production
- [ ] Store credentials securely
- [ ] Implement proper error handling
- [ ] Respect rate limits
- [ ] Validate server certificates
- [ ] Use secure session management
- [ ] Implement client-side input validation

## 🔄 Security Updates

### Regular Security Maintenance
- **Dependency Updates**: Regular updates of all dependencies
- **Security Patches**: Prompt application of security fixes
- **Vulnerability Scanning**: Regular security assessments
- **Code Reviews**: Security-focused code reviews
- **Penetration Testing**: Periodic security testing

### Reporting Security Issues
If you discover a security vulnerability:

1. **Do NOT** create a public GitHub issue
2. Email security details to: 24cd3dsu4@mitsgwl.ac.in
3. Include detailed reproduction steps
4. Allow reasonable time for response and fix
5. Coordinate disclosure timeline

## 📚 Security Resources

### Additional Reading
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [Web Application Security](https://developer.mozilla.org/en-US/docs/Web/Security)

### Security Tools
- **Static Analysis**: PHPStan, Psalm
- **Dependency Scanning**: Composer audit
- **Vulnerability Testing**: OWASP ZAP, Burp Suite
- **Code Quality**: SonarQube, CodeClimate

---

**Security is a shared responsibility. Stay vigilant and report any concerns immediately.**