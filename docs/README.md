# NearBy API Documentation

Welcome to the **NearBy API** - a comprehensive platform for student housing and local services. This API enables students, property owners, and service providers to connect seamlessly.

## 🚀 Quick Start

### 1. Access the Interactive Documentation
Visit the **Swagger UI** for interactive API testing:
- **Local**: [http://localhost/nearby/docs/api/](http://localhost/nearby/docs/api/)
- **Production**: [https://sumitrathor.rf.gd/nearby/docs/api/](https://sumitrathor.rf.gd/nearby/docs/api/)

### 2. Authentication
Most endpoints require authentication. Start by registering and logging in:

```bash
# Register a new user
curl -X POST http://localhost/nearby/api/auth/register.php \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john.doe@mitsgwl.ac.in",
    "password": "securePass123",
    "role": "junior",
    "user_category": "student"
  }'

# Login
curl -X POST http://localhost/nearby/api/auth/login.php \
  -H "Content-Type: application/json" \
  -c cookies.txt \
  -d '{
    "email": "john.doe@mitsgwl.ac.in",
    "password": "securePass123",
    "role": "junior"
  }'
```

### 3. Create Your First Post
```bash
# Create a room listing
curl -X POST http://localhost/nearby/api/posts/create.php \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "post_category": "room",
    "accommodation_type": "PG",
    "allowed_for": "Male",
    "rent_or_price": 8500,
    "location": "Bank Colony, Near MITS",
    "facilities": ["Wi-Fi", "Meals", "Laundry"],
    "description": "Comfortable PG with all amenities",
    "contact_phone": "+91 9876543210"
  }'
```

## 📚 API Overview

### Core Features
- **🔐 Authentication**: Secure registration and login with role-based access
- **🏠 Accommodations**: Create, search, and manage room listings
- **🛍️ Local Services**: Tiffin, gas, milk, and other service providers
- **🤖 AI Chatbot**: Intelligent assistance powered by Gemini AI
- **📞 Contact System**: Secure communication between users
- **🗺️ Map Integration**: Location-based search and discovery

### Base URLs
- **Local Development**: `http://localhost/nearby/api/`
- **Production**: `https://sumitrathor.rf.gd/nearby/api/`

## 🔗 API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register.php` | Register new user |
| POST | `/auth/login.php` | User login |
| POST | `/auth/google-login.php` | Google OAuth login |

### Posts Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/posts/create.php` | Create accommodation/service post |
| GET | `/posts/list.php` | Search and list posts |

### Communication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/contact/request.php` | Send contact request |
| POST | `/message-assistant-send.php` | Send message to AI chatbot |
| GET | `/message-assistant-history.php` | Get chat history |

### Location & Map
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/map/locations.php` | Get map locations |

## 🛡️ Security Features

### Input Validation & Sanitization
- **XSS Protection**: All user inputs are sanitized and HTML-encoded
- **SQL Injection Prevention**: Parameterized queries with proper type binding
- **Input Validation**: Comprehensive validation for all data types
- **Content Security**: Chatbot responses are filtered for sensitive information

### Rate Limiting
| Endpoint | Limit | Window |
|----------|-------|--------|
| Registration | 5 attempts | 5 minutes per IP |
| Login | 10 attempts | 5 minutes per IP |
| Post Creation | 5 posts | 1 hour per user |
| Chatbot | 30 messages | 5 minutes per user |

### Authentication & Sessions
- **Session Security**: Secure session handling with regeneration
- **CSRF Protection**: Cross-site request forgery tokens
- **Password Security**: Argon2ID hashing with strong parameters
- **Role-based Access**: Different permissions for juniors/seniors

## 📖 Detailed Examples

### [Authentication Examples](api/examples/authentication.md)
Complete examples for user registration, login, and session management.

### [Posts Management Examples](api/examples/posts.md)
Creating and searching accommodation and service listings with advanced filters.

### [AI Chatbot Examples](api/examples/chatbot.md)
Implementing the AI assistant for housing guidance and local service recommendations.

## 🔧 Integration Guide

### JavaScript/Frontend Integration
```javascript
// Base API client
class NearByAPI {
  constructor(baseUrl = '/nearby/api') {
    this.baseUrl = baseUrl;
  }
  
  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const config = {
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        ...options.headers
      },
      ...options
    };
    
    // Add CSRF token if available
    const csrfToken = sessionStorage.getItem('csrf_token');
    if (csrfToken) {
      config.headers['X-CSRF-Token'] = csrfToken;
    }
    
    const response = await fetch(url, config);
    const result = await response.json();
    
    if (!result.success) {
      throw new Error(result.message);
    }
    
    return result;
  }
  
  // Authentication methods
  async register(userData) {
    return this.request('/auth/register.php', {
      method: 'POST',
      body: JSON.stringify(userData)
    });
  }
  
  async login(credentials) {
    const result = await this.request('/auth/login.php', {
      method: 'POST',
      body: JSON.stringify(credentials)
    });
    
    // Store CSRF token
    if (result.csrf_token) {
      sessionStorage.setItem('csrf_token', result.csrf_token);
    }
    
    return result;
  }
  
  // Posts methods
  async createPost(postData) {
    return this.request('/posts/create.php', {
      method: 'POST',
      body: JSON.stringify(postData)
    });
  }
  
  async searchPosts(filters = {}) {
    const params = new URLSearchParams(filters);
    return this.request(`/posts/list.php?${params}`);
  }
  
  // Chatbot methods
  async sendMessage(message) {
    return this.request('/message-assistant-send.php', {
      method: 'POST',
      body: JSON.stringify({ message })
    });
  }
}

// Usage
const api = new NearByAPI();

// Register user
await api.register({
  name: "John Doe",
  email: "john@mitsgwl.ac.in",
  password: "secure123",
  role: "junior",
  user_category: "student"
});

// Login
await api.login({
  email: "john@mitsgwl.ac.in",
  password: "secure123",
  role: "junior"
});

// Search posts
const posts = await api.searchPosts({
  post_category: 'room',
  max_price: 10000
});
```

### PHP Integration
```php
<?php
// Include security utilities
require_once 'config/security.php';

// Example: Secure API endpoint
function handleApiRequest() {
    // Rate limiting
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!checkRateLimit('api_' . $clientIP, 60, 60)) {
        secureErrorResponse('Rate limit exceeded', 429);
    }
    
    // Validate input
    $input = json_decode(file_get_contents('php://input'), true);
    $messageValidation = validateText($input['message'] ?? '', 1, 1000);
    
    if (!$messageValidation['valid']) {
        secureErrorResponse($messageValidation['error'], 400);
    }
    
    // Process request...
    echo json_encode(['success' => true, 'data' => $result]);
}
?>
```

## 📊 Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { /* response data */ },
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "timestamp": "2024-01-15T10:30:00Z"
}
```

## 🚨 Error Handling

### HTTP Status Codes
- **200**: Success
- **400**: Bad Request (validation errors)
- **401**: Unauthorized (authentication required)
- **403**: Forbidden (insufficient permissions)
- **404**: Not Found
- **409**: Conflict (duplicate data)
- **422**: Unprocessable Entity (invalid content)
- **429**: Too Many Requests (rate limit exceeded)
- **500**: Internal Server Error

### Error Handling Best Practices
```javascript
async function handleApiCall(apiFunction) {
  try {
    const result = await apiFunction();
    return result;
  } catch (error) {
    switch (error.status) {
      case 401:
        // Redirect to login
        window.location.href = '/nearby/login.php';
        break;
      case 429:
        showError('Too many requests. Please wait before trying again.');
        break;
      case 500:
        showError('Server error. Please try again later.');
        break;
      default:
        showError(error.message || 'An unexpected error occurred.');
    }
    throw error;
  }
}
```

## 🔍 Testing

### Using Swagger UI
1. Visit the [interactive documentation](http://localhost/nearby/docs/api/)
2. Click "Authorize" to login (use the main application first)
3. Test endpoints directly in the browser

### Using cURL
```bash
# Test with session cookies
curl -X GET http://localhost/nearby/api/posts/list.php \
  -b cookies.txt \
  -H "Accept: application/json"
```

### Using Postman
1. Import the OpenAPI specification from `/docs/api/openapi.yaml`
2. Set up environment variables for base URL
3. Configure cookie-based authentication

## 📈 Performance Considerations

### Caching
- API responses can be cached based on content type
- Static resources (documentation) have long cache headers
- Database queries are optimized with proper indexing

### Rate Limiting
- Implement client-side rate limiting to avoid hitting limits
- Use exponential backoff for retry logic
- Monitor rate limit headers in responses

### Optimization Tips
```javascript
// Debounce search requests
const debouncedSearch = debounce(async (query) => {
  const results = await api.searchPosts({ location: query });
  displayResults(results.data);
}, 300);

// Batch multiple requests
const [posts, history] = await Promise.all([
  api.searchPosts(filters),
  api.getChatHistory(20)
]);
```

## 🤝 Contributing

### API Development Guidelines
1. **Security First**: Always validate and sanitize inputs
2. **Consistent Responses**: Follow the standard response format
3. **Error Handling**: Provide meaningful error messages
4. **Documentation**: Update OpenAPI spec for any changes
5. **Testing**: Test all endpoints thoroughly

### Adding New Endpoints
1. Create the PHP endpoint file
2. Add security validation using `config/security.php`
3. Update the OpenAPI specification
4. Add examples to the documentation
5. Test with Swagger UI

## 📞 Support

### Contact Information
- **Email**: 24cd3dsu4@mitsgwl.ac.in
- **Phone**: +91 7566868709
- **GitHub**: [https://github.com/sumitrathor1/nearby](https://github.com/sumitrathor1/nearby)

### Reporting Issues
1. Check existing documentation and examples
2. Test with the interactive Swagger UI
3. Provide detailed error messages and request/response data
4. Include steps to reproduce the issue

---

**Built with ❤️ for MITS students and the broader community**