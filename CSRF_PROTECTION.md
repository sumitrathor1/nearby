# CSRF Protection Implementation

## Overview
This document describes the Cross-Site Request Forgery (CSRF) protection implemented across the NearBy application to prevent unauthorized actions on behalf of authenticated users.

## What is CSRF?
Cross-Site Request Forgery is an attack where a malicious website tricks a user's browser into performing unwanted actions on a trusted site where the user is authenticated. Without CSRF protection, attackers could:
- Create posts or accommodations without user consent
- Modify user data
- Perform actions using the victim's session

## Implementation Details

### 1. CSRF Helper Functions (`includes/helpers/csrf.php`)

**Core Functions:**
- `generateCSRFToken()` - Generates a cryptographically secure random token
- `getCSRFToken()` - Returns existing token or generates new one (1-hour lifetime)
- `validateCSRFToken()` - Validates token from POST data or headers
- `csrfField()` - Generates HTML hidden input with token
- `csrfMetaTag()` - Generates meta tag for JavaScript access
- `requireCSRFToken()` - Validates token or returns 403 error

**Token Storage:**
- Tokens are stored in PHP sessions
- Each token has a 1-hour expiration time
- Tokens are regenerated when expired

### 2. Server-Side Protection

**Protected Endpoints:**
All POST endpoints now validate CSRF tokens:

- `api/auth/login.php`
- `api/auth/register.php`
- `api/auth/google-login.php`
- `api/posts/create.php`
- `api/add_guidance.php`
- `api/accommodations/create.php`
- `api/contact/request.php`
- `api/message-assistant-send.php`
- `api/post_accommodation.php`
- `api/login.php`
- `api/register.php`

**Implementation Pattern:**
```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers/csrf.php';

// Validate CSRF token at the beginning of the request
requireCSRFToken();

// Rest of the endpoint logic...
```

### 3. Client-Side Integration

**HTML Forms:**
All forms include the CSRF token as a hidden field:

```php
<form id="myForm">
    <?= csrfField() ?>
    <!-- Other form fields -->
</form>
```

**Protected Forms:**
- Login form ([login.php](login.php))
- Registration form ([register.php](register.php))
- Create post modal ([includes/create-post-modal.php](includes/create-post-modal.php))

**JavaScript/AJAX:**
The `NearBy.fetchJSON()` utility in [assets/js/main.js](assets/js/main.js) automatically:
1. Reads CSRF token from meta tag
2. Adds token to request headers as `X-CSRF-Token`
3. Includes token in JSON body for POST/PUT/DELETE requests
4. Appends token to FormData submissions

**Meta Tag Inclusion:**
The header includes the CSRF meta tag for JavaScript access:
```php
<?= csrfMetaTag() ?>
```

### 4. Token Validation

**Validation Process:**
1. Server checks for token in POST data or `X-CSRF-Token` header
2. Compares with session token using timing-safe comparison
3. Verifies token hasn't expired (1-hour lifetime)
4. Returns 403 error with JSON response if invalid

**Error Response:**
```json
{
  "success": false,
  "error": "Invalid or missing CSRF token. Please refresh the page and try again."
}
```

## Security Features

1. **Cryptographically Secure Tokens**: Uses `random_bytes(32)` for token generation
2. **Timing-Safe Comparison**: Uses `hash_equals()` to prevent timing attacks
3. **Token Expiration**: Tokens expire after 1 hour
4. **XSS Protection**: All tokens are properly escaped when rendered
5. **Automatic Token Refresh**: New tokens generated when expired
6. **Session-Based Storage**: Tokens tied to user sessions

## Testing CSRF Protection

### Manual Testing

1. **Valid Request Test:**
   - Log in to the application
   - Submit a form or make an AJAX request
   - Should succeed with 200 status

2. **Missing Token Test:**
   - Use browser DevTools or Postman
   - Send POST request without `csrf_token` field
   - Should receive 403 error

3. **Invalid Token Test:**
   - Modify the CSRF token value before submission
   - Should receive 403 error

4. **Expired Token Test:**
   - Get a CSRF token
   - Wait more than 1 hour
   - Submit with old token
   - Should receive 403 error

### Example with cURL:

```bash
# This should fail (no token)
curl -X POST http://localhost/nearby/api/posts/create.php \
  -H "Content-Type: application/json" \
  -d '{"location":"Test","description":"Test"}'

# This should succeed (with valid token)
curl -X POST http://localhost/nearby/api/posts/create.php \
  -H "Content-Type: application/json" \
  -H "X-CSRF-Token: YOUR_VALID_TOKEN_HERE" \
  -d '{"location":"Test","description":"Test"}'
```

## Best Practices

1. **Always Include Token**: All state-changing requests must include CSRF token
2. **Use Helper Functions**: Use `csrfField()` and `csrfMetaTag()` instead of manual token handling
3. **Validate Early**: Call `requireCSRFToken()` at the beginning of POST endpoints
4. **Session Management**: Ensure sessions are properly started before token operations
5. **HTTPS**: Always use HTTPS in production to prevent token interception

## Troubleshooting

### "Invalid or missing CSRF token" Error

**Possible Causes:**
1. Session expired or cookies blocked
2. Token expired (>1 hour old)
3. Multiple tabs with different tokens
4. Form cached by browser with old token

**Solutions:**
1. Refresh the page to get a new token
2. Clear browser cookies/cache
3. Ensure cookies are enabled
4. Check session configuration

### Token Not Being Sent

**Check:**
1. Meta tag is present in HTML (`<meta name="csrf-token">`)
2. Form includes hidden input with token
3. JavaScript is properly loading
4. `NearBy.fetchJSON()` is being used for AJAX requests

## Maintenance

### Token Lifetime Configuration
To change token expiration time, modify the comparison in `csrf.php`:
```php
// Current: 1 hour (3600 seconds)
if (time() - $_SESSION['csrf_token_time'] < 3600)

// Change to 2 hours:
if (time() - $_SESSION['csrf_token_time'] < 7200)
```

### Adding CSRF to New Endpoints

When creating new POST endpoints:

1. **Include the helper:**
   ```php
   require_once __DIR__ . '/../includes/helpers/csrf.php';
   ```

2. **Validate token:**
   ```php
   requireCSRFToken();
   ```

3. **For forms:** Add `<?= csrfField() ?>`

4. **For AJAX:** Use `NearBy.fetchJSON()` (already handles tokens)

## References

- [OWASP CSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
- [PHP Session Security](https://www.php.net/manual/en/features.session.security.php)

## Summary

CSRF protection has been successfully implemented across all POST endpoints in the NearBy application. The implementation follows security best practices and provides both server-side validation and client-side token management. All forms and AJAX requests are now protected against CSRF attacks.

**Last Updated:** January 29, 2026
