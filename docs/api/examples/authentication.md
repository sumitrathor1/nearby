# Authentication Examples

## User Registration

### Student Registration
```bash
curl -X POST http://localhost/nearby/api/auth/register.php \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Rahul Sharma",
    "email": "rahul.sharma@mitsgwl.ac.in",
    "password": "securePass123",
    "role": "junior",
    "user_category": "student"
  }'
```

### Service Provider Registration
```bash
curl -X POST http://localhost/nearby/api/auth/register.php \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Amit Tiffin Service",
    "email": "amit@gmail.com",
    "password": "tiffinPass456",
    "role": "senior",
    "user_category": "tiffin"
  }'
```

### JavaScript Example
```javascript
async function registerUser(userData) {
  try {
    const response = await fetch('/nearby/api/auth/register.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(userData)
    });
    
    const result = await response.json();
    
    if (result.success) {
      console.log('Registration successful:', result.message);
      // Redirect to login page
      window.location.href = '/nearby/login.php';
    } else {
      console.error('Registration failed:', result.message);
      showError(result.message);
    }
  } catch (error) {
    console.error('Network error:', error);
    showError('Registration failed. Please try again.');
  }
}

// Usage
registerUser({
  name: "Priya Patel",
  email: "priya.patel@mitsgwl.ac.in",
  password: "mySecurePass789",
  role: "junior",
  user_category: "student"
});
```

## User Login

### Basic Login
```bash
curl -X POST http://localhost/nearby/api/auth/login.php \
  -H "Content-Type: application/json" \
  -c cookies.txt \
  -d '{
    "email": "rahul.sharma@mitsgwl.ac.in",
    "password": "securePass123",
    "role": "junior"
  }'
```

### JavaScript Login with Session Handling
```javascript
async function loginUser(credentials) {
  try {
    const response = await fetch('/nearby/api/auth/login.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'include', // Important for session cookies
      body: JSON.stringify(credentials)
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Store CSRF token for future requests
      if (result.csrf_token) {
        sessionStorage.setItem('csrf_token', result.csrf_token);
      }
      
      console.log('Login successful:', result.message);
      
      // Redirect based on role
      window.location.href = result.redirect;
    } else {
      console.error('Login failed:', result.message);
      showError(result.message);
    }
  } catch (error) {
    console.error('Network error:', error);
    showError('Login failed. Please try again.');
  }
}

// Usage
loginUser({
  email: "rahul.sharma@mitsgwl.ac.in",
  password: "securePass123",
  role: "junior"
});
```

## Response Examples

### Successful Registration Response
```json
{
  "success": true,
  "message": "Registration successful. You can login now."
}
```

### Successful Login Response
```json
{
  "success": true,
  "message": "Login successful",
  "redirect": "junior-dashboard.php",
  "csrf_token": "abc123def456ghi789jkl012mno345pqr678stu901vwx234yz"
}
```

### Error Responses
```json
{
  "success": false,
  "message": "Email already registered",
  "timestamp": "2024-01-15T10:30:00Z"
}
```

```json
{
  "success": false,
  "message": "Invalid email, password, or role",
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### Rate Limit Response
```json
{
  "success": false,
  "message": "Too many login attempts. Please try again later.",
  "timestamp": "2024-01-15T10:30:00Z"
}
```

## Security Considerations

### Password Requirements
- Minimum 6 characters
- Must contain at least one letter and one number
- Examples of valid passwords: `pass123`, `myPass1`, `secure2024`

### Email Validation
- Students must use `@mitsgwl.ac.in` domain
- Service providers can use any valid email
- Email is automatically converted to lowercase

### Rate Limiting
- **Registration**: 5 attempts per 5 minutes per IP address
- **Login**: 10 attempts per 5 minutes per IP address
- Exceeded limits result in 429 status code

### Session Security
- Sessions are regenerated on successful login
- CSRF tokens are provided for additional security
- Sessions expire after inactivity

## Error Handling Best Practices

```javascript
function handleApiError(response, result) {
  switch (response.status) {
    case 400:
      showError(`Invalid input: ${result.message}`);
      break;
    case 401:
      showError('Authentication failed. Please check your credentials.');
      break;
    case 409:
      showError('Email already registered. Please use a different email or login.');
      break;
    case 429:
      showError('Too many attempts. Please wait before trying again.');
      break;
    case 500:
      showError('Server error. Please try again later.');
      break;
    default:
      showError(`Unexpected error: ${result.message}`);
  }
}

function showError(message) {
  // Display error to user (implement based on your UI framework)
  console.error(message);
  alert(message); // Replace with proper UI notification
}
```