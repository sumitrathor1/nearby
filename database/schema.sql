CREATE DATABASE IF NOT EXISTS nearby CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nearby;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    college_email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('junior', 'senior') NOT NULL,
    user_type ENUM('student', 'owner', 'service_provider') NOT NULL DEFAULT 'student',
    user_category ENUM('student', 'home_owner', 'room_owner', 'tiffin', 'gas', 'milk', 'sabji', 'other_service') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS accommodations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    type ENUM('PG', 'Flat', 'Room', 'Hostel') NOT NULL,
    allowed_for ENUM('Male', 'Female', 'Family') NOT NULL,
    rent INT UNSIGNED NOT NULL,
    location VARCHAR(255) NOT NULL,
    facilities TEXT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_accommodations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    post_category ENUM('room', 'service') NOT NULL,
    service_type ENUM('tiffin', 'gas', 'milk', 'sabji', 'other') DEFAULT NULL,
    accommodation_type ENUM('PG', 'Flat', 'Room', 'Hostel') DEFAULT NULL,
    allowed_for ENUM('Male', 'Female', 'Family') DEFAULT NULL,
    rent_or_price INT UNSIGNED DEFAULT NULL,
    location VARCHAR(255) NOT NULL,
    facilities TEXT DEFAULT NULL,
    availability_time VARCHAR(120) DEFAULT NULL,
    description TEXT NOT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_post_category (post_category),
    INDEX idx_post_service_type (service_type),
    INDEX idx_post_location (location(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS guidance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(120) NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accommodation_id INT UNSIGNED NOT NULL,
    requester_id INT UNSIGNED NOT NULL,
    message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_contact_accommodation FOREIGN KEY (accommodation_id) REFERENCES accommodations(id) ON DELETE CASCADE,
    CONSTRAINT fk_contact_requester FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chatbot_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    sender ENUM('user', 'bot') NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_chatbot_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_chatbot_user_time (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS feedback (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) DEFAULT NULL,
    email VARCHAR(160) DEFAULT NULL,
    feedback_type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) DEFAULT NULL,
    email VARCHAR(160) NOT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    category ENUM('fridge', 'cooler', 'almirah', 'washing-machine', 'furniture', 'other') NOT NULL,
    price INT UNSIGNED NOT NULL,
    condition_status ENUM('new-like', 'good', 'old') NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_product_category (category),
    INDEX idx_product_price (price),
    INDEX idx_product_location (location(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
