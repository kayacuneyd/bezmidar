-- ===================================
-- DijitalMentor Database Schema
-- Last Updated: 2025-11-21
-- ===================================

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables if they exist (for clean install)
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS conversations;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS unlock_requests;
DROP TABLE IF EXISTS lesson_requests;
DROP TABLE IF EXISTS teacher_subjects;
DROP TABLE IF EXISTS teacher_profiles;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS users;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

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
    is_premium BOOLEAN DEFAULT 0 COMMENT 'Premium üyelik durumu',
    premium_expires_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Premium üyelik bitiş tarihi',
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_phone (phone),
    INDEX idx_role (role),
    INDEX idx_active (is_active),
    INDEX idx_approval (approval_status),
    INDEX idx_premium (is_premium)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TEACHER PROFILES TABLE
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
    cv_url VARCHAR(255) DEFAULT NULL COMMENT 'CV dosyası (premium)',
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
-- SUBJECTS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL COMMENT 'Türkçe isim',
    name_de VARCHAR(50) DEFAULT NULL COMMENT 'Almanca isim',
    slug VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(10) DEFAULT NULL COMMENT 'Emoji icon',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TEACHER SUBJECTS (Many-to-Many)
-- ===================================
CREATE TABLE IF NOT EXISTS teacher_subjects (
    teacher_id INT NOT NULL,
    subject_id INT NOT NULL,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
    
    PRIMARY KEY (teacher_id, subject_id),
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- LESSON REQUESTS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS lesson_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    subject_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    city VARCHAR(50) DEFAULT NULL,
    budget_range VARCHAR(50) DEFAULT NULL COMMENT 'Örn: 15-25€',
    status ENUM('active', 'matched', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- REVIEWS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    parent_id INT NOT NULL,
    rating TINYINT NOT NULL COMMENT '1-5',
    comment TEXT,
    is_approved BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_teacher (teacher_id),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- UNLOCK REQUESTS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS unlock_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    teacher_id INT NOT NULL,
    message TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_unlock_parent FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_unlock_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_unlock_pair (parent_id, teacher_id),
    INDEX idx_unlock_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- INSERT DEMO DATA
-- ===================================

-- Insert Subjects
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
('Bilgisayar', 'Informatik', 'bilgisayar', '💻', 12);

-- Insert Demo Teacher Users
INSERT INTO users (phone, password_hash, full_name, role, city, zip_code, approval_status, is_premium, is_verified) VALUES
('+491234567801', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmet Yılmaz', 'student', 'Berlin', '10115', 'approved', 0, 1),
('+491234567802', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ayşe Demir', 'student', 'München', '80331', 'approved', 0, 1),
('+491234567803', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mehmet Kaya', 'student', 'Aachen', '52062', 'pending', 0, 0),
('+491234567804', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Zeynep Çelik', 'student', 'Hamburg', '20095', 'approved', 0, 1),
('+491234567805', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Can Yıldız', 'student', 'Frankfurt', '60311', 'pending', 0, 0);

-- Insert Demo Parent Users
INSERT INTO users (phone, password_hash, full_name, role, approval_status) VALUES
('+491234567901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Demo Veli', 'parent', 'approved'),
('+491234567902', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test Parent', 'parent', 'approved');

-- Insert Teacher Profiles
INSERT INTO teacher_profiles (user_id, university, department, graduation_year, bio, city, zip_code, hourly_rate, rating_avg, review_count, experience_years) VALUES
(1, 'TU Berlin', 'Bilgisayar Mühendisliği', 2020, 'Bilgisayar mühendisliği mezunuyum. Matematik ve Fizik konularında 5 yıllık öğretmenlik deneyimim var.', 'Berlin', '10115', 25.00, 4.80, 12, 5),
(2, 'LMU München', 'Alman Dili ve Edebiyatı', 2019, 'Almanca ve İngilizce öğretmeniyim. İletişim odaklı öğretim yöntemleri kullanıyorum.', 'München', '80331', 20.00, 5.00, 5, 4),
(3, 'RWTH Aachen', 'Makine Mühendisliği', 2021, 'Matematik, Kimya ve Biyoloji derslerinde uzmanım.', 'Aachen', '52062', 18.00, 4.50, 3, 2),
(4, 'Universität Hamburg', 'Psikoloji', 2018, 'İngilizce ve Türkçe öğretmeniyim. Öğrenci merkezli yaklaşım benimsiyorum.', 'Hamburg', '20095', 22.00, 4.90, 8, 6),
(5, 'Goethe Universität', 'Ekonomi', 2022, 'Matematik ve ekonomi derslerinde yardımcı oluyorum.', 'Frankfurt', '60311', 20.00, 0.00, 0, 1);

-- Insert Teacher-Subject Relationships
INSERT INTO teacher_subjects (teacher_id, subject_id, proficiency_level) VALUES
(1, 1, 'expert'),    -- Ahmet - Matematik
(1, 5, 'advanced'),  -- Ahmet - Fizik
(2, 2, 'expert'),    -- Ayşe - Almanca
(2, 3, 'advanced'),  -- Ayşe - İngilizce
(3, 1, 'intermediate'), -- Mehmet - Matematik
(3, 6, 'advanced'),  -- Mehmet - Kimya
(3, 7, 'intermediate'), -- Mehmet - Biyoloji
(4, 3, 'expert'),    -- Zeynep - İngilizce
(4, 4, 'expert'),    -- Zeynep - Türkçe
(5, 1, 'advanced');  -- Can - Matematik

-- ===================================
-- MESSAGING SYSTEM TABLES
-- ===================================

-- Conversations table: Stores metadata for each conversation between a teacher and parent
CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    parent_id INT NOT NULL,
    last_message_text TEXT,
    last_message_at TIMESTAMP NULL,
    teacher_unread_count INT DEFAULT 0,
    parent_unread_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_conversation (teacher_id, parent_id),
    INDEX idx_teacher (teacher_id),
    INDEX idx_parent (parent_id),
    INDEX idx_updated (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages table: Stores individual messages within conversations
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    message_text TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    INDEX idx_sender (sender_id),
    INDEX idx_created (created_at),
    INDEX idx_unread (conversation_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- MIGRATION NOTES
-- ===================================
-- This schema includes all migrations:
-- - approval_status field for user approval workflow
-- - is_premium and premium_expires_at for premium membership
-- - cv_url for teacher CV uploads
-- - city and zip_code fields for location-based search
-- - unlock_requests table for iletişim talepleri
-- - Messaging system (conversations and messages tables) - Added 2025-11-21
--
-- Password for demo users: 'password' (hashed with bcrypt)
--
-- To reset database: DROP DATABASE and run this script again
-- ===================================

SELECT 'Database schema created successfully!' as Status;
