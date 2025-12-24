CREATE DATABASE IF NOT EXISTS nearby CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nearby;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    college_email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('junior', 'senior') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS guidance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(120) NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accommodation_id INT UNSIGNED NOT NULL,
    requester_id INT UNSIGNED NOT NULL,
    message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_contact_accommodation FOREIGN KEY (accommodation_id) REFERENCES accommodations(id) ON DELETE CASCADE,
    CONSTRAINT fk_contact_requester FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
