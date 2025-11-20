-- ===================================
-- DIJITALMENTOR DATABASE INSTALLATION
-- ===================================
-- This file contains the complete database schema and initial data
-- Run this file once to set up your database
-- Character set: utf8mb4 for Turkish character support

-- CREATE DATABASE IF NOT EXISTS u553245641_dijitalmentor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE u553245641_dijitalmentor;

-- ===================================
-- USERS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL UNIQUE COMMENT 'Format: +49xxxxxxxxxx',
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('student', 'parent', 'admin') NOT NULL DEFAULT 'student',
    avatar_url VARCHAR(255) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    city VARCHAR(50) DEFAULT NULL,
    zip_code VARCHAR(10) DEFAULT NULL,
    is_verified BOOLEAN DEFAULT 0 COMMENT 'Öğrenci belgesi onayı',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved' COMMENT 'Hesap onay durumu',
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_phone (phone),
    INDEX idx_role (role),
    INDEX idx_active (is_active),
    INDEX idx_approval (approval_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TEACHER PROFILES
-- ===================================
CREATE TABLE IF NOT EXISTS teacher_profiles (
    user_id INT PRIMARY KEY,
    university VARCHAR(100) DEFAULT NULL,
    department VARCHAR(100) DEFAULT NULL,
    graduation_year YEAR DEFAULT NULL,
    bio TEXT,
    city VARCHAR(50) DEFAULT NULL,
    zip_code VARCHAR(10) DEFAULT NULL COMMENT 'PLZ (örn: 70806)',
    address_detail VARCHAR(255) DEFAULT NULL,
    hourly_rate DECIMAL(10, 2) DEFAULT 20.00,
    video_intro_url VARCHAR(255) DEFAULT NULL COMMENT 'Tanıtım videosu',
    experience_years TINYINT DEFAULT 0,
    total_students INT DEFAULT 0 COMMENT 'Toplam öğrenci sayısı',
    rating_avg DECIMAL(3,2) DEFAULT 0.00 COMMENT '0.00 - 5.00',
    review_count INT DEFAULT 0,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_city_zip (city, zip_code),
    INDEX idx_rate (hourly_rate),
    INDEX idx_rating (rating_avg),
    FULLTEXT idx_bio (bio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- SUBJECTS
-- ===================================
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL COMMENT 'Türkçe isim',
    name_de VARCHAR(50) DEFAULT NULL COMMENT 'Almanca isim',
    slug VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT NULL COMMENT 'Emoji veya icon class',
    sort_order INT DEFAULT 0,
    
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TEACHER-SUBJECT RELATIONSHIP
-- ===================================
CREATE TABLE IF NOT EXISTS teacher_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    subject_id INT NOT NULL,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
    
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_teacher_subject (teacher_id, subject_id),
    INDEX idx_teacher (teacher_id),
    INDEX idx_subject (subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- UNLOCK REQUESTS (Contact Requests)
-- ===================================
CREATE TABLE IF NOT EXISTS unlock_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    teacher_id INT NOT NULL,
    status ENUM('pending', 'approved', 'viewed') DEFAULT 'pending',
    message TEXT COMMENT 'Velinin mesajı',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    viewed_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_teacher_status (teacher_id, status),
    INDEX idx_parent (parent_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- REVIEWS
-- ===================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    teacher_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    is_approved BOOLEAN DEFAULT 0 COMMENT 'Admin onayı',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_teacher_approved (teacher_id, is_approved),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- FAVORITES
-- ===================================
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    teacher_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (parent_id, teacher_id),
    INDEX idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- LESSON REQUESTS (Veli Ders Talepleri)
-- ===================================
CREATE TABLE IF NOT EXISTS lesson_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    subject_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    city VARCHAR(100),
    budget_range VARCHAR(50),
    status ENUM('active', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES users(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- INITIAL DATA - SUBJECTS
-- ===================================
INSERT INTO subjects (name, name_de, slug, icon, sort_order) VALUES
('Matematik', 'Mathematik', 'matematik', '📐', 1),
('Almanca', 'Deutsch', 'almanca', '🇩🇪', 2),
('İngilizce', 'Englisch', 'ingilizce', '🇬🇧', 3),
('Türkçe', 'Türkisch', 'turkce', '🇹🇷', 4),
('Fizik', 'Physik', 'fizik', '⚛️', 5),
('Kimya', 'Chemie', 'kimya', '🧪', 6),
('Biyoloji', 'Biologie', 'biyoloji', '🧬', 7),
('Tarih', 'Geschichte', 'tarih', '📜', 8),
('Coğrafya', 'Geographie', 'cografya', '🌍', 9),
('Müzik', 'Musik', 'muzik', '🎵', 10),
('Resim', 'Kunst', 'resim', '🎨', 11),
('Bilgisayar', 'Informatik', 'bilgisayar', '💻', 12)
ON DUPLICATE KEY UPDATE name=name;

-- ===================================
-- DEMO DATA - USERS (Teachers)
-- ===================================
INSERT INTO users (id, phone, password_hash, full_name, role, approval_status, city, zip_code, is_verified, is_active, avatar_url) VALUES
(101, '+4915111111111', '$2y$10$rJYQXQxQxQxQxQxQxQxQxuQxQxQxQxQxQxQxQxQxQxQxQxQ', 'Ahmet Yılmaz', 'student', 'approved', 'Berlin', '10115', 1, 1, 'https://randomuser.me/api/portraits/men/32.jpg'),
(102, '+4915122222222', '$2y$10$rJYQXQxQxQxQxQxQxQxQxuQxQxQxQxQxQxQxQxQxQxQxQxQ', 'Ayşe Demir', 'student', 'approved', 'München', '80331', 1, 1, 'https://randomuser.me/api/portraits/women/44.jpg'),
(103, '+4915133333333', '$2y$10$rJYQXQxQxQxQxQxQxQxQxuQxQxQxQxQxQxQxQxQxQxQxQxQ', 'Mehmet Kaya', 'student', 'pending', 'Aachen', '52062', 1, 1, 'https://randomuser.me/api/portraits/men/85.jpg'),
(104, '+4915144444444', '$2y$10$rJYQXQxQxQxQxQxQxQxQxuQxQxQxQxQxQxQxQxQxQxQxQxQ', 'Zeynep Çelik', 'student', 'approved', 'Hamburg', '20095', 1, 1, 'https://randomuser.me/api/portraits/women/68.jpg'),
(105, '+4915155555555', '$2y$10$rJYQXQxQxQxQxQxQxQxQxuQxQxQxQxQxQxQxQxQxQxQxQxQ', 'Can Yıldız', 'student', 'pending', 'Frankfurt', '60311', 0, 1, 'https://randomuser.me/api/portraits/men/12.jpg')
ON DUPLICATE KEY UPDATE full_name=full_name;

-- ===================================
-- DEMO DATA - USERS (Parents)
-- ===================================
INSERT INTO users (id, phone, password_hash, full_name, role, approval_status, is_active) VALUES
(201, '+4916111111111', '$2y$10$rJYQXQxQxQxQxQxQxQxQxuQxQxQxQxQxQxQxQxQxQxQxQxQ', 'Fatma Öztürk', 'parent', 'approved', 1),
(202, '+4916122222222', '$2y$10$rJYQXQxQxQxQxQxQxQxQxuQxQxQxQxQxQxQxQxQxQxQxQxQ', 'Mustafa Arslan', 'parent', 'approved', 1)
ON DUPLICATE KEY UPDATE full_name=full_name;

-- ===================================
-- DEMO DATA - TEACHER PROFILES
-- ===================================
INSERT INTO teacher_profiles (user_id, university, department, graduation_year, bio, city, zip_code, hourly_rate, experience_years, rating_avg, review_count) VALUES
(101, 'TU Berlin', 'Bilgisayar Mühendisliği', 2024, 'Merhaba! Ben Ahmet. Berlin Teknik Üniversitesi\'nde son sınıf öğrencisiyim. Matematik ve Fizik derslerinde yardımcı olabilirim.', 'Berlin', '10115', 25.00, 3, 4.8, 12),
(102, 'LMU München', 'Alman Dili ve Edebiyatı', 2023, 'Almanca öğrenmek isteyenlere yardımcı oluyorum. Hem gramer hem de konuşma pratiği yapabiliriz.', 'München', '80331', 20.00, 2, 5.0, 5),
(103, 'RWTH Aachen', 'Makine Mühendisliği', 2025, 'Sayısal derslerde zorlanan öğrencilere pratik yöntemlerle ders anlatıyorum.', 'Aachen', '52062', 18.00, 1, 4.5, 3),
(104, 'Universität Hamburg', 'Psikoloji', 2024, 'Öğrencilerin sadece derslerine değil, motivasyonlarına da katkı sağlamayı hedefliyorum.', 'Hamburg', '20095', 22.00, 4, 4.9, 8),
(105, 'Goethe Universität Frankfurt', 'Hukuk', 2026, 'Tarih ve Coğrafya derslerinde yardımcı olabilirim.', 'Frankfurt', '60311', 15.00, 0, 0.0, 0)
ON DUPLICATE KEY UPDATE university=university;

-- ===================================
-- DEMO DATA - TEACHER SUBJECTS
-- ===================================
INSERT INTO teacher_subjects (teacher_id, subject_id, proficiency_level) VALUES
(101, 1, 'expert'),
(101, 5, 'advanced'),
(102, 2, 'expert'),
(102, 3, 'advanced'),
(103, 6, 'intermediate'),
(103, 7, 'intermediate'),
(103, 1, 'advanced'),
(104, 3, 'expert'),
(104, 4, 'expert'),
(105, 8, 'intermediate'),
(105, 9, 'intermediate')
ON DUPLICATE KEY UPDATE proficiency_level=proficiency_level;

-- ===================================
-- DEMO DATA - REVIEWS
-- ===================================
INSERT INTO reviews (parent_id, teacher_id, rating, comment, is_approved, created_at) VALUES
(201, 101, 5, 'Ahmet hoca çok ilgili, oğlumun matematik notları yükseldi.', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(202, 101, 4, 'Ders anlatımı güzel ama bazen hızlı gidiyor.', 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(201, 102, 5, 'Kızım Ayşe ablasını çok seviyor, Almancası gelişti.', 1, DATE_SUB(NOW(), INTERVAL 1 WEEK)),
(202, 104, 5, 'Zeynep hanım çok kibar ve sabırlı.', 1, DATE_SUB(NOW(), INTERVAL 3 DAY))
ON DUPLICATE KEY UPDATE rating=rating;

-- ===================================
-- INSTALLATION COMPLETE
-- ===================================
SELECT 'Database installation completed successfully!' as Status;
