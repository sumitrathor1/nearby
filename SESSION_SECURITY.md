# Session Security Implementation

## Overview
This document describes the comprehensive session security implementation in the NearBy application to prevent session hijacking, fixation attacks, and unauthorized access.

## What are Session Security Weaknesses?

Session security vulnerabilities allow attackers to:
- **Session Hijacking**: Steal session IDs and impersonate users
- **Session Fixation**: Force users to use attacker-controlled session IDs
- **Session Timeout Issues**: Access accounts indefinitely after users leave
- **XSS-based Session Theft**: Steal cookies through JavaScript

## Implementation Details

### 1. Secure Session Helper ([includes/helpers/session.php](includes/helpers/session.php))

**Core Functions:**

#### Session Initialization
- `secureSessionStart()` - Initialize session with security settings
- `configureSessionSecurity()` - Configure secure session parameters

#### Session Validation
- `validateSession()` - Check timeout and hijacking attempts
- `validateSessionFingerprint()` - Detect session hijacking
- `getClientIP()` - Get client IP address (handles proxies)

#### Session Management
- `regenerateSessionId()` - Regenerate session ID to prevent fixation
- `destroySession()` - Completely destroy session (logout)
- `setUserSession($userData)` - Set user session after login with regeneration
- `refreshSessionActivity()` - Update last activity time

#### Session Information
- `isAuthenticated()` - Check if user is authenticated
- `getSessionTimeoutRemaining()` - Get seconds until timeout
- `getSessionInfo()` - Get session information for monitoring
- `isSessionExpiringSoon()` - Check if session expires within 5 minutes
- `getSessionExpiryWarning()` - Get warning message if expiring soon

### 2. Security Configurations

#### Session Cookie Settings
```php
// HTTP Only - Prevents JavaScript access (XSS protection)
ini_set('session.cookie_httponly', 1);

// Secure - Only send over HTTPS (uncomment in production)
// ini_set('session.cookie_secure', 1);

// SameSite - CSRF protection
ini_set('session.cookie_samesite', 'Lax');

// Custom session name (less obvious)
ini_set('session.name', 'NEARBY_SESSID');
```

#### Session ID Settings
```php
// Use strict mode - Reject uninitialized session IDs
ini_set('session.use_strict_mode', 1);

// Use cookies only - No URL-based session IDs
ini_set('session.use_only_cookies', 1);

// Modern session ID hashing (48 characters)
ini_set('session.sid_length', 48);
ini_set('session.sid_bits_per_character', 6);
```

#### Garbage Collection
```php
// Session lifetime: 1 hour
ini_set('session.gc_maxlifetime', 3600);

// Garbage collection probability (1%)
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
```

### 3. Timeout Mechanisms

#### Absolute Timeout
Maximum session lifetime: **2 hours** (twice the SESSION_TIMEOUT)
```php
if (time() - $_SESSION['created_at'] > SESSION_TIMEOUT * 2) {
    destroySession();
    return false;
}
```

#### Inactivity Timeout
Maximum inactivity: **1 hour** (SESSION_TIMEOUT)
```php
if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
    destroySession();
    return false;
}
```

#### Session Regeneration Interval
Session ID regenerated every: **5 minutes** (SESSION_REGENERATE_INTERVAL)
```php
if (time() - $_SESSION['last_regeneration'] > SESSION_REGENERATE_INTERVAL) {
    regenerateSessionId();
}
```

### 4. Session Fingerprinting

**Tracked Parameters:**
- User Agent string
- IP Address (logged but allows changes for mobile users)
- Session creation time
- Last activity time
- Last regeneration time

**Hijacking Detection:**
```php
function validateSessionFingerprint() {
    $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // User agent must remain constant
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $currentUserAgent) {
        return false; // Potential hijacking
    }
    
    // IP changes are logged but allowed (mobile networks)
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $currentIP) {
        error_log("Session IP changed");
    }
    
    return true;
}
```

### 5. Login Session Handling

All login endpoints now use `setUserSession()` which:
1. Regenerates session ID (prevents fixation)
2. Resets all timing variables
3. Updates session fingerprint
4. Sets authenticated flag
5. Stores user data

**Implementation:**
```php
// After successful authentication
setUserSession([
    'id' => (int) $user['id'],
    'name' => $user['name'],
    'email' => $user['college_email'],
    'role' => $user['role'],
    'user_type' => $user['user_type'] ?? 'student',
]);
```

**Protected Login Endpoints:**
- [api/login.php](api/login.php)
- [api/auth/login.php](api/auth/login.php)
- [api/auth/google-login.php](api/auth/google-login.php)

### 6. Logout Handling

[logout.php](logout.php) now uses `destroySession()` which:
1. Clears all session variables
2. Deletes session cookie
3. Destroys server-side session file

```php
require_once __DIR__ . '/includes/helpers/session.php';
secureSessionStart();
destroySession();
header('Location: login.php');
```

### 7. Session Tracking Variables

**Automatically Set:**
```php
$_SESSION['created_at']         // Session creation timestamp
$_SESSION['last_activity']      // Last user activity timestamp
$_SESSION['last_regeneration']  // Last session ID regeneration
$_SESSION['user_agent']         // User agent string
$_SESSION['ip_address']         // Client IP address
$_SESSION['authenticated']      // Authentication flag
$_SESSION['user']               // User data array
```

## Protected Files

### Page Files
- [includes/header.php](includes/header.php)
- [junior-dashboard.php](junior-dashboard.php)
- [senior-dashboard.php](senior-dashboard.php)
- [details.php](details.php)
- [logout.php](logout.php)

### API Endpoints
- [api/login.php](api/login.php)
- [api/auth/login.php](api/auth/login.php)
- [api/auth/google-login.php](api/auth/google-login.php)
- [api/posts/create.php](api/posts/create.php)
- [api/posts/list.php](api/posts/list.php)
- [api/accommodations/create.php](api/accommodations/create.php)
- [api/post_accommodation.php](api/post_accommodation.php)
- [api/contact/request.php](api/contact/request.php)
- [api/message-assistant-send.php](api/message-assistant-send.php)
- [api/message-assistant-history.php](api/message-assistant-history.php)
- [api/add_guidance.php](api/add_guidance.php)

### Helper Files
- [includes/helpers/session.php](includes/helpers/session.php)
- [includes/helpers/csrf.php](includes/helpers/csrf.php) (updated to use secure sessions)
- [includes/helpers/authorization.php](includes/helpers/authorization.php) (updated to use secure sessions)

## Security Features

### 1. **HttpOnly Cookie**
Prevents JavaScript from accessing session cookies, protecting against XSS attacks.

### 2. **Secure Cookie (Production)**
Ensures cookies only sent over HTTPS. Uncomment in production:
```php
ini_set('session.cookie_secure', 1);
```

### 3. **SameSite Attribute**
Set to `Lax` for CSRF protection while allowing normal navigation.

### 4. **Strict Mode**
Rejects uninitialized session IDs, preventing session fixation.

### 5. **Cookie-Only Sessions**
Disables URL-based session IDs, preventing session ID leakage in URLs.

### 6. **Strong Session IDs**
48-character session IDs with high entropy (6 bits per character).

### 7. **Regular Regeneration**
Session IDs regenerated every 5 minutes and after login.

### 8. **Timeout Protection**
Both absolute and inactivity timeouts to prevent indefinite sessions.

### 9. **Fingerprint Validation**
Detects session hijacking through user agent changes.

### 10. **Proper Logout**
Complete session destruction including cookie deletion.

## Attack Prevention

### Session Fixation Attack
**Attack**: Attacker forces victim to use attacker's session ID
**Prevention**: 
- Strict mode rejects uninitialized IDs
- Session ID regenerated after login
- Cookie-only sessions prevent URL manipulation

### Session Hijacking Attack
**Attack**: Attacker steals session ID to impersonate user
**Prevention**:
- HttpOnly cookies prevent XSS theft
- Fingerprint validation detects hijacking
- Regular ID regeneration limits exposure window
- HTTPS (in production) prevents network sniffing

### Session Timeout Issues
**Attack**: Long-lived sessions allow unauthorized access
**Prevention**:
- 1-hour inactivity timeout
- 2-hour absolute timeout
- Garbage collection removes old sessions

### XSS-Based Session Theft
**Attack**: JavaScript steals session cookie
**Prevention**:
- HttpOnly flag blocks JavaScript access
- SameSite attribute provides additional protection

## Testing Session Security

### Test Case 1: Session Timeout (Inactivity)
```bash
# Log in
# Wait 61+ minutes without activity
# Try to access protected page

Expected: Redirected to login (session expired)
```

### Test Case 2: Session Hijacking Detection
```bash
# Log in with Browser A
# Copy session cookie to Browser B (different user agent)
# Try to access protected page in Browser B

Expected: Session destroyed, must re-login
```

### Test Case 3: Session Fixation Prevention
```bash
# Get session ID before login
# Log in
# Check session ID after login

Expected: Session ID changed after login
```

### Test Case 4: Session Regeneration
```bash
# Log in
# Check session ID
# Wait 6+ minutes
# Make request
# Check session ID again

Expected: Session ID changed
```

### Test Case 5: Proper Logout
```bash
# Log in
# Click logout
# Use browser back button

Expected: Cannot access protected pages
```

### Test Case 6: Cookie Security
```bash
# Inspect session cookie in browser dev tools

Expected:
- HttpOnly: true
- SameSite: Lax
- Secure: true (production only)
```

## Configuration

### Adjusting Timeouts

Edit [includes/helpers/session.php](includes/helpers/session.php):

```php
// Session timeout (inactivity) - default 1 hour
define('SESSION_TIMEOUT', 3600); // seconds

// Session regeneration interval - default 5 minutes
define('SESSION_REGENERATE_INTERVAL', 300); // seconds
```

### Enabling HTTPS-Only Cookies

For production, uncomment in [includes/helpers/session.php](includes/helpers/session.php):
```php
ini_set('session.cookie_secure', 1);
```

### Stricter IP Validation

To block IP changes (affects mobile users), modify `validateSessionFingerprint()`:
```php
if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $currentIP) {
    return false; // Block IP changes
}
```

## Monitoring

### Get Session Information
```php
$info = getSessionInfo();
// Returns:
// - status: active/not_started
// - session_id
// - created_at
// - last_activity
// - timeout_remaining
// - authenticated
// - user_id
// - ip_address
```

### Check Expiry Warning
```php
$warning = getSessionExpiryWarning();
if ($warning) {
    // Display warning to user
    echo $warning; // "Your session will expire in X minute(s)"
}
```

### Refresh Activity on AJAX
```php
// For long-running pages with AJAX calls
refreshSessionActivity();
```

## Best Practices

1. **Always use `secureSessionStart()`** instead of `session_start()`
2. **Use `setUserSession()`** after successful login
3. **Use `destroySession()`** for logout
4. **Call `refreshSessionActivity()`** on AJAX requests to prevent timeout
5. **Enable HTTPS** in production for maximum security
6. **Monitor session logs** for suspicious activity
7. **Test timeout behavior** thoroughly
8. **Consider stricter IP validation** based on your use case

## Migration Guide

### Old Code:
```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

### New Code:
```php
require_once __DIR__ . '/includes/helpers/session.php';
secureSessionStart();
```

### Old Login:
```php
$_SESSION['user'] = $userData;
```

### New Login:
```php
setUserSession($userData);
```

### Old Logout:
```php
session_destroy();
```

### New Logout:
```php
destroySession();
```

## Common Issues

### Issue: Session Timeout Too Short
**Solution**: Increase `SESSION_TIMEOUT` constant

### Issue: Mobile Users Losing Sessions
**Solution**: Current implementation allows IP changes. If experiencing issues, check error logs.

### Issue: Session Not Starting
**Solution**: Check file permissions on session save path:
```bash
php -i | grep session.save_path
```

### Issue: Cookie Not Being Set
**Solution**: 
1. Ensure no output before `secureSessionStart()`
2. Check `headers_sent()` for debugging
3. Verify domain/path settings

## References

- [OWASP Session Management Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html)
- [PHP Session Security](https://www.php.net/manual/en/features.session.security.php)
- [CWE-384: Session Fixation](https://cwe.mitre.org/data/definitions/384.html)
- [CWE-613: Insufficient Session Expiration](https://cwe.mitre.org/data/definitions/613.html)

## Summary

Session security has been comprehensively implemented:
- ✅ Secure session configuration (HttpOnly, SameSite, Strict Mode)
- ✅ Session timeout (1-hour inactivity, 2-hour absolute)
- ✅ Session regeneration (every 5 minutes + after login)
- ✅ Session fingerprinting (User Agent tracking)
- ✅ Proper logout with complete cleanup
- ✅ 21 files updated to use secure sessions
- ✅ All login endpoints regenerate session IDs
- ✅ CSRF and Authorization helpers integrated

**Last Updated:** January 29, 2026
