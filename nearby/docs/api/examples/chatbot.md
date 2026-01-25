# AI Chatbot Examples

## Sending Messages

### Basic Message
```bash
curl -X POST http://localhost/nearby/api/message-assistant-send.php \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "message": "I am looking for a PG near MITS with Wi-Fi and meals. What are my options?"
  }'
```

### JavaScript Implementation
```javascript
async function sendChatMessage(message) {
  try {
    const csrfToken = sessionStorage.getItem('csrf_token');
    
    const response = await fetch('/nearby/api/message-assistant-send.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken || ''
      },
      credentials: 'include',
      body: JSON.stringify({ message })
    });
    
    const result = await response.json();
    
    if (result.success) {
      console.log('Bot reply:', result.reply);
      displayMessage('user', message);
      displayMessage('bot', result.reply);
      return result.reply;
    } else {
      console.error('Chat error:', result.message);
      showError(result.message);
      return null;
    }
  } catch (error) {
    console.error('Network error:', error);
    showError('Failed to send message. Please try again.');
    return null;
  }
}

// Usage examples
sendChatMessage("I'm looking for a PG near MITS with Wi-Fi and meals. What are my options?");
sendChatMessage("What's the average rent for a single room in Bank Colony?");
sendChatMessage("Can you recommend good tiffin services in Thatipur area?");
sendChatMessage("I need gas cylinder delivery. Who provides this service?");
```

## Getting Chat History

### Retrieve Chat History
```bash
curl -X GET "http://localhost/nearby/api/message-assistant-history.php?limit=20" \
  -b cookies.txt
```

### JavaScript History Implementation
```javascript
async function getChatHistory(limit = 20) {
  try {
    const response = await fetch(`/nearby/api/message-assistant-history.php?limit=${limit}`, {
      method: 'GET',
      credentials: 'include'
    });
    
    const result = await response.json();
    
    if (result.success) {
      console.log('Chat history loaded:', result.data.length, 'messages');
      displayChatHistory(result.data);
      return result.data;
    } else {
      console.error('Failed to load history:', result.message);
      return [];
    }
  } catch (error) {
    console.error('History error:', error);
    return [];
  }
}

function displayChatHistory(messages) {
  const chatContainer = document.getElementById('chat-container');
  chatContainer.innerHTML = '';
  
  messages.forEach(message => {
    displayMessage(message.sender, message.message, message.created_at);
  });
  
  // Scroll to bottom
  chatContainer.scrollTop = chatContainer.scrollHeight;
}

function displayMessage(sender, message, timestamp = null) {
  const chatContainer = document.getElementById('chat-container');
  const messageDiv = document.createElement('div');
  messageDiv.className = `message ${sender}-message`;
  
  const time = timestamp ? new Date(timestamp).toLocaleTimeString() : new Date().toLocaleTimeString();
  
  messageDiv.innerHTML = `
    <div class="message-content">${escapeHtml(message)}</div>
    <div class="message-time">${time}</div>
  `;
  
  chatContainer.appendChild(messageDiv);
  chatContainer.scrollTop = chatContainer.scrollHeight;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}
```

## Complete Chat Interface

### HTML Structure
```html
<div id="chat-interface">
  <div id="chat-header">
    <h3>🤖 NearBy Assistant</h3>
    <p>Ask me about accommodations and local services!</p>
  </div>
  
  <div id="chat-container" class="chat-messages">
    <!-- Messages will be displayed here -->
  </div>
  
  <div id="chat-input-container">
    <input type="text" id="chat-input" placeholder="Ask about PGs, tiffin services, or local guidance..." maxlength="1000">
    <button id="send-button" onclick="sendMessage()">Send</button>
  </div>
  
  <div id="chat-suggestions">
    <button class="suggestion-btn" onclick="sendSuggestion('What are the best PG options near MITS?')">
      Best PGs near MITS
    </button>
    <button class="suggestion-btn" onclick="sendSuggestion('I need tiffin service recommendations')">
      Tiffin Services
    </button>
    <button class="suggestion-btn" onclick="sendSuggestion('How do I find gas cylinder delivery?')">
      Gas Delivery
    </button>
  </div>
</div>
```

### Complete JavaScript Chat Implementation
```javascript
class ChatBot {
  constructor() {
    this.chatContainer = document.getElementById('chat-container');
    this.chatInput = document.getElementById('chat-input');
    this.sendButton = document.getElementById('send-button');
    this.isTyping = false;
    
    this.init();
  }
  
  init() {
    // Load chat history
    this.loadHistory();
    
    // Set up event listeners
    this.chatInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        this.sendMessage();
      }
    });
    
    this.sendButton.addEventListener('click', () => this.sendMessage());
    
    // Show welcome message if no history
    setTimeout(() => {
      if (this.chatContainer.children.length === 0) {
        this.showWelcomeMessage();
      }
    }, 1000);
  }
  
  async loadHistory() {
    try {
      const response = await fetch('/nearby/api/message-assistant-history.php?limit=50', {
        credentials: 'include'
      });
      
      const result = await response.json();
      
      if (result.success && result.data.length > 0) {
        result.data.forEach(message => {
          this.displayMessage(message.sender, message.message, message.created_at, false);
        });
        this.scrollToBottom();
      }
    } catch (error) {
      console.error('Failed to load chat history:', error);
    }
  }
  
  showWelcomeMessage() {
    const welcomeMsg = `Hello! I'm your NearBy assistant. I can help you with:

🏠 Finding PGs, flats, and rooms near MITS
🍽️ Tiffin and mess service recommendations  
⚡ Gas, milk, and other local services
📍 Location guidance and transport info
💡 General housing tips for students

What would you like to know?`;
    
    this.displayMessage('bot', welcomeMsg, null, true);
  }
  
  async sendMessage() {
    const message = this.chatInput.value.trim();
    
    if (!message || this.isTyping) return;
    
    // Clear input
    this.chatInput.value = '';
    
    // Display user message
    this.displayMessage('user', message, null, true);
    
    // Show typing indicator
    this.showTypingIndicator();
    
    try {
      const response = await fetch('/nearby/api/message-assistant-send.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': sessionStorage.getItem('csrf_token') || ''
        },
        credentials: 'include',
        body: JSON.stringify({ message })
      });
      
      const result = await response.json();
      
      this.hideTypingIndicator();
      
      if (result.success) {
        this.displayMessage('bot', result.reply, result.created_at, true);
      } else {
        this.displayMessage('bot', `Sorry, I encountered an error: ${result.message}`, null, true);
      }
    } catch (error) {
      this.hideTypingIndicator();
      this.displayMessage('bot', 'Sorry, I\'m having trouble connecting. Please try again.', null, true);
      console.error('Chat error:', error);
    }
  }
  
  displayMessage(sender, message, timestamp = null, animate = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender}-message`;
    
    if (animate) {
      messageDiv.style.opacity = '0';
      messageDiv.style.transform = 'translateY(20px)';
    }
    
    const time = timestamp 
      ? new Date(timestamp).toLocaleTimeString() 
      : new Date().toLocaleTimeString();
    
    // Format message with line breaks
    const formattedMessage = message.replace(/\n/g, '<br>');
    
    messageDiv.innerHTML = `
      <div class="message-content">${this.escapeHtml(formattedMessage)}</div>
      <div class="message-time">${time}</div>
    `;
    
    this.chatContainer.appendChild(messageDiv);
    
    if (animate) {
      // Animate in
      setTimeout(() => {
        messageDiv.style.transition = 'all 0.3s ease';
        messageDiv.style.opacity = '1';
        messageDiv.style.transform = 'translateY(0)';
      }, 50);
    }
    
    this.scrollToBottom();
  }
  
  showTypingIndicator() {
    this.isTyping = true;
    this.sendButton.disabled = true;
    this.sendButton.textContent = 'Typing...';
    
    const typingDiv = document.createElement('div');
    typingDiv.className = 'message bot-message typing-indicator';
    typingDiv.id = 'typing-indicator';
    typingDiv.innerHTML = `
      <div class="message-content">
        <div class="typing-dots">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    `;
    
    this.chatContainer.appendChild(typingDiv);
    this.scrollToBottom();
  }
  
  hideTypingIndicator() {
    this.isTyping = false;
    this.sendButton.disabled = false;
    this.sendButton.textContent = 'Send';
    
    const typingIndicator = document.getElementById('typing-indicator');
    if (typingIndicator) {
      typingIndicator.remove();
    }
  }
  
  scrollToBottom() {
    setTimeout(() => {
      this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
    }, 100);
  }
  
  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
}

// Suggestion functions
function sendSuggestion(message) {
  const chatInput = document.getElementById('chat-input');
  chatInput.value = message;
  
  // Focus and trigger send
  chatInput.focus();
  if (window.chatBot) {
    window.chatBot.sendMessage();
  }
}

// Initialize chat when page loads
document.addEventListener('DOMContentLoaded', () => {
  window.chatBot = new ChatBot();
});
```

## Response Examples

### Successful Message Response
```json
{
  "success": true,
  "reply": "Based on your requirements for a PG near MITS with Wi-Fi and meals, I'd recommend checking these areas:\n\n🏠 **Bank Colony** - Very close to MITS (2-3 min walk)\n- Average rent: ₹7,000-₹9,000/month\n- Most PGs include Wi-Fi and meals\n- Good connectivity and safety\n\n🏠 **Thatipur** - Slightly farther but more options\n- Average rent: ₹6,000-₹8,500/month\n- Many tiffin services available\n- Better transport connectivity\n\nWould you like me to help you with specific price ranges or other facilities?",
  "created_at": "2024-01-15T10:30:00Z"
}
```

### Chat History Response
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "sender": "user",
      "message": "I'm looking for a PG near MITS with Wi-Fi and meals",
      "created_at": "2024-01-15T10:25:00Z"
    },
    {
      "id": 2,
      "sender": "bot",
      "message": "Based on your requirements for a PG near MITS...",
      "created_at": "2024-01-15T10:25:30Z"
    }
  ]
}
```

## Security Features

### Input Validation
- Messages are limited to 1000 characters
- HTML tags are stripped and content is sanitized
- Suspicious patterns (prompt injection attempts) are blocked

### Rate Limiting
- 30 messages per 5 minutes per user
- Prevents spam and abuse

### Content Filtering
- Bot responses are scanned for sensitive information
- Potentially harmful content is replaced with safe alternatives

## Common Use Cases

### Housing Queries
```javascript
// PG search
sendChatMessage("Show me PGs in Bank Colony under ₹8000 with meals");

// Room requirements
sendChatMessage("I need a single room with Wi-Fi and parking near MITS");

// Comparison
sendChatMessage("Compare PG vs flat rental costs in Gwalior");
```

### Service Queries
```javascript
// Tiffin services
sendChatMessage("Which tiffin service delivers to Thatipur area?");

// Local services
sendChatMessage("I need milk delivery and gas cylinder service");

// Transport
sendChatMessage("How to reach City Center from MITS by bus?");
```

### General Guidance
```javascript
// New student help
sendChatMessage("I'm a new student at MITS. What should I know about accommodation?");

// Safety tips
sendChatMessage("Safety tips for students living in PGs");

// Cost planning
sendChatMessage("What's the monthly budget for a student in Gwalior?");
```