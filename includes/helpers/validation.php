<?php
// Input validation and sanitization helpers

class InputValidator {
    
    public static function validateEmail($email) {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if (strlen($email) > 160) {
            return false;
        }
        return $email;
    }
    
    public static function validatePassword($password) {
        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters');
        }
        if (!preg_match('/[A-Za-z]/', $password)) {
            throw new InvalidArgumentException('Password must contain letters');
        }
        if (!preg_match('/[0-9]/', $password)) {
            throw new InvalidArgumentException('Password must contain numbers');
        }
        return $password;
    }
    
    public static function sanitizeText($text, $maxLength = 255) {
        $text = trim($text);
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        return substr($text, 0, $maxLength);
    }
    
    public static function sanitizeHtml($html) {
        return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateName($name) {
        $name = self::sanitizeText($name, 120);
        if (strlen($name) < 2) {
            throw new InvalidArgumentException('Name must be at least 2 characters');
        }
        if (!preg_match('/^[a-zA-Z\s\-\.]+$/', $name)) {
            throw new InvalidArgumentException('Name contains invalid characters');
        }
        return $name;
    }
    
    public static function validateRole($role) {
        if (!in_array($role, ['junior', 'senior'], true)) {
            throw new InvalidArgumentException('Invalid role');
        }
        return $role;
    }
    
    public static function validateUserCategory($category) {
        $allowed = ['student', 'home_owner', 'room_owner', 'tiffin', 'gas', 'milk', 'sabji', 'other_service'];
        if (!in_array($category, $allowed, true)) {
            throw new InvalidArgumentException('Invalid user category');
        }
        return $category;
    }
}