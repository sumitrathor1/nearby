# Posts Management Examples

## Creating Posts

### Room/Accommodation Post
```bash
curl -X POST http://localhost/nearby/api/posts/create.php \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "post_category": "room",
    "accommodation_type": "PG",
    "allowed_for": "Male",
    "rent_or_price": 8500,
    "location": "Bank Colony, Near MITS Gate",
    "facilities": ["Wi-Fi", "Meals", "Laundry", "Power Backup", "CCTV"],
    "description": "Comfortable PG accommodation with all modern amenities. Located just 2 minutes walk from MITS main gate. Includes breakfast and dinner, high-speed Wi-Fi, and 24/7 power backup.",
    "contact_phone": "+91 9876543210"
  }'
```

### Service Post (Tiffin Service)
```bash
curl -X POST http://localhost/nearby/api/posts/create.php \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "post_category": "service",
    "service_type": "tiffin",
    "rent_or_price": 150,
    "location": "Thatipur, Gwalior",
    "availability_time": "7:00 AM - 9:00 PM",
    "description": "Homemade vegetarian meals delivered daily. Fresh ingredients, hygienic preparation. Lunch and dinner available. Special student rates.",
    "contact_phone": "+91 9876543210"
  }'
```

### JavaScript Post Creation
```javascript
async function createPost(postData) {
  try {
    const csrfToken = sessionStorage.getItem('csrf_token');
    
    const response = await fetch('/nearby/api/posts/create.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken || ''
      },
      credentials: 'include',
      body: JSON.stringify(postData)
    });
    
    const result = await response.json();
    
    if (result.success) {
      console.log('Post created successfully:', result.message);
      console.log('Post ID:', result.post_id);
      
      // Redirect to posts list or show success message
      showSuccess('Post created successfully!');
      loadUserPosts(); // Refresh the posts list
    } else {
      console.error('Failed to create post:', result.message);
      showError(result.message);
    }
  } catch (error) {
    console.error('Network error:', error);
    showError('Failed to create post. Please try again.');
  }
}

// Room post example
createPost({
  post_category: "room",
  accommodation_type: "Flat",
  allowed_for: "Family",
  rent_or_price: 12000,
  location: "Gole Ka Mandir, Gwalior",
  facilities: ["Wi-Fi", "Parking", "24/7 Water", "Elevator"],
  description: "Spacious 2BHK flat suitable for families. Well-ventilated rooms with attached bathrooms.",
  contact_phone: "+91 9876543210"
});

// Service post example
createPost({
  post_category: "service",
  service_type: "gas",
  rent_or_price: 850,
  location: "City Center, Gwalior",
  availability_time: "8:00 AM - 8:00 PM",
  description: "LPG gas cylinder delivery service. Quick delivery within 2 hours of booking.",
  contact_phone: "+91 9876543210"
});
```

## Searching Posts

### Basic Search
```bash
curl -X GET "http://localhost/nearby/api/posts/list.php" \
  -b cookies.txt
```

### Advanced Search with Filters
```bash
curl -X GET "http://localhost/nearby/api/posts/list.php?post_category=room&accommodation_type=PG&allowed_for=Male&min_price=5000&max_price=10000" \
  -b cookies.txt
```

### Service Search
```bash
curl -X GET "http://localhost/nearby/api/posts/list.php?post_category=service&service_type=tiffin&max_price=200" \
  -b cookies.txt
```

### JavaScript Search Implementation
```javascript
async function searchPosts(filters = {}) {
  try {
    // Build query string from filters
    const queryParams = new URLSearchParams();
    
    Object.keys(filters).forEach(key => {
      if (filters[key] !== '' && filters[key] !== null && filters[key] !== undefined) {
        queryParams.append(key, filters[key]);
      }
    });
    
    const queryString = queryParams.toString();
    const url = `/nearby/api/posts/list.php${queryString ? '?' + queryString : ''}`;
    
    const response = await fetch(url, {
      method: 'GET',
      credentials: 'include'
    });
    
    const result = await response.json();
    
    if (result.success) {
      console.log('Found posts:', result.data.length);
      displayPosts(result.data);
      return result.data;
    } else {
      console.error('Search failed:', result.message);
      showError(result.message);
      return [];
    }
  } catch (error) {
    console.error('Search error:', error);
    showError('Search failed. Please try again.');
    return [];
  }
}

// Search examples
searchPosts({
  post_category: 'room',
  accommodation_type: 'PG',
  allowed_for: 'Male',
  min_price: 5000,
  max_price: 10000
});

searchPosts({
  post_category: 'service',
  service_type: 'tiffin',
  max_price: 200
});

// Location-based search
searchPosts({
  location: 'Bank Colony'
});

function displayPosts(posts) {
  const container = document.getElementById('posts-container');
  container.innerHTML = '';
  
  posts.forEach(post => {
    const postElement = createPostElement(post);
    container.appendChild(postElement);
  });
}

function createPostElement(post) {
  const div = document.createElement('div');
  div.className = 'post-card';
  
  const facilitiesHtml = post.facilities && post.facilities.length > 0 
    ? `<div class="facilities">${post.facilities.map(f => `<span class="facility-tag">${f}</span>`).join('')}</div>`
    : '';
  
  const priceHtml = post.rent_or_price 
    ? `<div class="price">₹${post.rent_or_price}${post.post_category === 'room' ? '/month' : ''}</div>`
    : '';
  
  const contactHtml = post.can_view_contact && post.contact_phone
    ? `<div class="contact"><strong>Contact:</strong> ${post.contact_phone}</div>`
    : '<div class="contact-login">Login to view contact details</div>';
  
  div.innerHTML = `
    <div class="post-header">
      <span class="post-category">${post.post_category}</span>
      ${post.accommodation_type ? `<span class="accommodation-type">${post.accommodation_type}</span>` : ''}
      ${post.service_type ? `<span class="service-type">${post.service_type}</span>` : ''}
      ${post.allowed_for ? `<span class="allowed-for">${post.allowed_for}</span>` : ''}
    </div>
    <div class="post-location">${post.location}</div>
    ${priceHtml}
    ${facilitiesHtml}
    <div class="post-description">${post.description}</div>
    ${post.availability_time ? `<div class="availability">Available: ${post.availability_time}</div>` : ''}
    <div class="post-meta">
      <span class="user-name">By: ${post.user_name}</span>
      <span class="post-date">${new Date(post.created_at).toLocaleDateString()}</span>
    </div>
    ${contactHtml}
  `;
  
  return div;
}
```

## Response Examples

### Successful Post Creation
```json
{
  "success": true,
  "message": "Post created successfully",
  "post_id": 123
}
```

### Posts Search Results
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "post_category": "room",
      "service_type": null,
      "accommodation_type": "PG",
      "allowed_for": "Male",
      "rent_or_price": 8500,
      "location": "Bank Colony, Near MITS Gate",
      "facilities": ["Wi-Fi", "Meals", "Laundry", "Power Backup", "CCTV"],
      "availability_time": null,
      "description": "Comfortable PG accommodation with all modern amenities...",
      "contact_phone": "+91 9876543210",
      "created_at": "2024-01-15T10:30:00Z",
      "user_name": "Amit Kumar",
      "user_type": "owner",
      "can_view_contact": true
    },
    {
      "id": 124,
      "post_category": "service",
      "service_type": "tiffin",
      "accommodation_type": null,
      "allowed_for": null,
      "rent_or_price": 150,
      "location": "Thatipur, Gwalior",
      "facilities": [],
      "availability_time": "7:00 AM - 9:00 PM",
      "description": "Homemade vegetarian meals delivered daily...",
      "contact_phone": null,
      "created_at": "2024-01-15T11:00:00Z",
      "user_name": "Priya Tiffin Service",
      "user_type": "service_provider",
      "can_view_contact": false
    }
  ]
}
```

## Validation Rules

### Room Posts
- `accommodation_type`: Must be one of `PG`, `Flat`, `Room`, `Hostel`
- `allowed_for`: Must be one of `Male`, `Female`, `Family`
- `rent_or_price`: Required, must be between 1 and 100,000
- `facilities`: Optional array, max 20 items, each max 50 characters

### Service Posts
- `service_type`: Must be one of `tiffin`, `gas`, `milk`, `sabji`, `other`
- `availability_time`: Required, max 120 characters
- `rent_or_price`: Optional, must be between 0 and 50,000

### Common Fields
- `location`: Required, 3-255 characters
- `description`: Required, 10-2000 characters, HTML allowed
- `contact_phone`: Required, must match pattern `^[0-9+][0-9\-\s]{6,}$`

## Error Examples

### Validation Errors
```json
{
  "success": false,
  "message": "Description: Text must be at least 10 characters",
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### Authentication Required
```json
{
  "success": false,
  "message": "Login required to create a post",
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### Rate Limit Exceeded
```json
{
  "success": false,
  "message": "Too many posts created. Please wait before creating another post.",
  "timestamp": "2024-01-15T10:30:00Z"
}
```