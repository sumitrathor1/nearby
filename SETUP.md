# NearBy Project - Setup Guide

## 🚀 Quick Start

### 1. Database Setup
```sql
-- Import the complete schema
mysql -u root -p < database/schema.sql

-- Optional: Add sample guidance data
mysql -u root -p nearby < database/sample_guidance.sql
```

### 2. Environment Configuration
Edit `config/.env` with your actual values:
```env
# Database
LOCAL_DB_HOST=127.0.0.1
LOCAL_DB_USER=your_db_user
LOCAL_DB_PASS=your_db_password
LOCAL_DB_NAME=nearby

# API Keys
GEMINI_API_KEY=your_actual_gemini_api_key
```

### 3. File Permissions
```bash
chmod 755 config/.env
chmod 755 private/
```

### 4. Web Server Setup
- Ensure PHP 8.0+ with MySQLi extension
- Point document root to the `nearby/` directory
- Enable URL rewriting if needed

### 5. Test Installation
Visit `http://your-domain/api/test-config.php` to verify configuration.

## 🔧 Troubleshooting

### Common Issues:
- **Database connection fails**: Check `.env` credentials
- **Blank pages**: Check PHP error logs
- **API returns errors**: Verify database tables exist
- **Forms don't submit**: Check file permissions on PHP files

### Debug Mode:
Set `IS_LOCAL = true` in config for detailed error messages.

## 📁 Project Structure
```
nearby/
├── api/           # API endpoints
├── config/        # Configuration files
├── database/      # Schema and sample data
├── includes/      # Reusable components
├── assets/        # CSS, JS, images
├── admin/         # Admin panels
└── private/       # Database utilities
```

## ✅ Features Status
- ✅ User registration/login
- ✅ Accommodation listings
- ✅ Contact forms
- ✅ Feedback system
- ✅ Admin panels
- ✅ AI Chatbot integration
- ✅ Dynamic guidance loading
- 🔄 Second-hand marketplace (coming soon)