-- =========================================================
-- FutClub Database Schema
-- Database: MySQL / MariaDB
-- Jalankan file ini di phpMyAdmin (XAMPP) untuk membuat DB
-- =========================================================

CREATE DATABASE IF NOT EXISTS futclub_db;
USE futclub_db;

-- =========================================================
-- Tabel users
-- Menyimpan data user hasil login Google (Firebase Auth)
-- =========================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firebase_uid VARCHAR(128) NOT NULL UNIQUE,   -- UID dari Firebase Auth (Google Login)
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    photo_url VARCHAR(255) DEFAULT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================================
-- Tabel sport_categories
-- Daftar kategori olahraga (Basket, Lari, Futsal, Padel, Tenis, dll)
-- =========================================================
CREATE TABLE sport_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon_name VARCHAR(50) DEFAULT NULL  -- nama file svg/vector drawable di Android, misal "ic_basketball"
);

-- Data awal kategori olahraga
INSERT INTO sport_categories (name, icon_name) VALUES
('Basket', 'ic_basketball'),
('Lari / Jogging', 'ic_running'),
('Futsal', 'ic_futsal'),
('Padel', 'ic_padel'),
('Tenis', 'ic_tennis'),
('Bulu Tangkis', 'ic_badminton'),
('Sepak Bola', 'ic_football'),
('Bersepeda', 'ic_cycling');

-- =========================================================
-- Tabel user_categories
-- Relasi many-to-many: user tertarik ke banyak kategori olahraga
-- =========================================================
CREATE TABLE user_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES sport_categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_category (user_id, category_id)
);

-- =========================================================
-- Tabel communities
-- Komunitas olahraga yang dibuat oleh admin
-- =========================================================
CREATE TABLE communities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    photo_url VARCHAR(255) DEFAULT NULL,
    whatsapp_link VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES sport_categories(id)
);

-- =========================================================
-- Tabel community_gallery
-- Maksimal 3 foto kegiatan per komunitas (validasi dilakukan di API)
-- =========================================================
CREATE TABLE community_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    community_id INT NOT NULL,
    photo_url VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (community_id) REFERENCES communities(id) ON DELETE CASCADE
);

-- =========================================================
-- Tabel community_members
-- User yang join ke komunitas tertentu
-- =========================================================
CREATE TABLE community_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    community_id INT NOT NULL,
    user_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (community_id) REFERENCES communities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_member (community_id, user_id)
);
