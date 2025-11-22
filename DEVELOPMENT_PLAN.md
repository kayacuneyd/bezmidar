# DİJİTALMENTOR GELİŞTİRME PLANI

## 📋 GENEL BAKIŞ

Bu dokümantasyon, Dijitalmentor projesine eklenecek 4 ana özellik grubunu ve implementasyon detaylarını içermektedir.

### Teknoloji Stack
- **Frontend**: SvelteKit (SSG)
- **Backend**: PHP 8.x (REST API)
- **Database**: MySQL/MariaDB
- **Authentication**: JWT
- **Deployment**: Hostinger

### Eklenecek Özellikler
1. **Gelişmiş Mesajlaşma & Onay Formu Sistemi**
2. **Ödül/Teşvik Sistemi** (Veli/Öğrenci ve Öğretmen için)
3. **Profil İyileştirmeleri** (NULL değerlerini gizleme)
4. **CV Yükleme Kısıtlaması** (Sadece premium üyeler)

---

## 1️⃣ MESAJLAŞMA & ONAY FORMU SİSTEMİ

### 1.1 Mevcut Durum
- ✅ Temel iki yönlü mesajlaşma zaten mevcut
- ✅ Hem öğretmen hem veli mesaj başlatabilir
- ❌ Onay formu sistemi yok
- ❌ Ders lokasyon seçimi yok
- ❌ Otomatik video konferans link oluşturma yok

### 1.2 Database Değişiklikleri

**Yeni Tablo: `lesson_agreements`**

```sql
-- Dosya: database/migration_add_lesson_agreements.sql

CREATE TABLE IF NOT EXISTS lesson_agreements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL COMMENT 'Formu gönderen kullanıcı',
    recipient_id INT NOT NULL COMMENT 'Formu alacak kullanıcı',

    -- Ders Bilgileri
    subject_id INT NOT NULL,
    lesson_location ENUM('student_home', 'turkish_center', 'online') NOT NULL,
    lesson_address VARCHAR(255) DEFAULT NULL COMMENT 'Öğrenci evi veya dernek adresi',

    -- Online için
    meeting_platform ENUM('google_meet', 'zoom', 'jitsi', 'other') DEFAULT NULL,
    meeting_link VARCHAR(500) DEFAULT NULL COMMENT 'Otomatik oluşturulan link',

    -- Ders Detayları
    hourly_rate DECIMAL(10,2) NOT NULL,
    hours_per_week TINYINT DEFAULT 1,
    start_date DATE DEFAULT NULL,
    notes TEXT DEFAULT NULL,

    -- Onay Durumu
    status ENUM('pending', 'accepted', 'rejected', 'cancelled') DEFAULT 'pending',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,

    INDEX idx_conversation (conversation_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.3 Backend API Endpoints

#### 1.3.1 Onay Formu Oluşturma
**Dosya**: `server/api/agreements/create.php`

**Özellikler**:
- Kullanıcı doğrulama (student veya parent)
- Konuşma erişim kontrolü
- Jitsi Meet link otomatik oluşturma
- Google Meet/Zoom için placeholder (API entegrasyonu gerekir)

**Request**:
```json
{
  "conversation_id": 123,
  "recipient_id": 456,
  "subject_id": 1,
  "lesson_location": "online",
  "meeting_platform": "jitsi",
  "hourly_rate": 25.00,
  "hours_per_week": 2,
  "start_date": "2025-12-01",
  "notes": "Haftalık 2 saat matematik dersi"
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 789,
    "meeting_link": "https://meet.jit.si/dijitalmentor-abc123"
  },
  "message": "Onay formu gönderildi"
}
```

#### 1.3.2 Onay Formu Yanıtlama
**Dosya**: `server/api/agreements/respond.php`

**Request**:
```json
{
  "agreement_id": 789,
  "status": "accepted"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Onay formu kabul edildi"
}
```

#### 1.3.3 Onay Formlarını Listeleme
**Dosya**: `server/api/agreements/list.php`

**Request**: `GET /agreements/list.php?conversation_id=123`

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 789,
      "subject_name": "Matematik",
      "subject_icon": "🔢",
      "lesson_location": "online",
      "meeting_link": "https://meet.jit.si/dijitalmentor-abc123",
      "hourly_rate": "25.00",
      "hours_per_week": 2,
      "status": "pending",
      "sender_name": "Ali Veli",
      "recipient_name": "Ayşe Öğretmen",
      "created_at": "2025-11-22 10:30:00"
    }
  ]
}
```

### 1.4 Frontend Komponentleri

#### 1.4.1 Onay Formu Komponenti
**Dosya**: `src/lib/components/AgreementForm.svelte`

**Özellikler**:
- Ders seçimi (subjects dropdown)
- Lokasyon seçimi (3 seçenek)
- Adres girişi (fiziksel lokasyonlar için)
- Platform seçimi (online için)
- Ücret ve saat girişi
- Başlangıç tarihi (opsiyonel)
- Notlar alanı

**Kullanım**:
```svelte
<AgreementForm
  conversationId={activeConversation.id}
  recipientId={activeConversation.other_user.id}
  subjects={subjects}
  on:success={handleAgreementSuccess}
/>
```

#### 1.4.2 Onay Formu Kartı
**Dosya**: `src/lib/components/AgreementCard.svelte`

**Özellikler**:
- Form detaylarını görüntüleme
- Durum badge'i (pending/accepted/rejected)
- Meeting link'i (varsa)
- Kabul/Red butonları (alıcı için)
- Responsive tasarım

**Kullanım**:
```svelte
<AgreementCard
  agreement={agreement}
  on:responded={handleAgreementResponded}
/>
```

#### 1.4.3 Mesajlaşma Sayfası Entegrasyonu
**Dosya**: `src/routes/panel/mesajlar/+page.svelte`

**Değişiklikler**:
- Agreement form toggle butonu
- Agreements listesi bölümü
- Subjects yükleme
- Agreement success/response handlers

---

## 2️⃣ ÖDÜL/TEŞVİK SİSTEMİ

### 2.1 Sistem Mantığı

**Veli/Öğrenci İçin**:
- 5 saat → %10 indirim kuponu (€5)
- 10 saat → %15 indirim + dijital materyaller (€10)
- 15+ saat → %20 indirim + 1 saat ücretsiz ders + premium (€20)

**Öğretmen İçin**:
- 20 saat → €10 Amazon hediye kartı
- 50 saat → €25 Amazon hediye kartı
- 100 saat → €50 Amazon hediye kartı

### 2.2 Database Değişiklikleri

**Yeni Tablolar:**

```sql
-- Dosya: database/migration_add_rewards.sql

-- Ders saati takibi
CREATE TABLE IF NOT EXISTS lesson_hours_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    agreement_id INT NOT NULL,
    hours_completed DECIMAL(5,2) NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (agreement_id) REFERENCES lesson_agreements(id) ON DELETE CASCADE,

    INDEX idx_user (user_id),
    INDEX idx_agreement (agreement_id),
    INDEX idx_completed (completed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ödüller
CREATE TABLE IF NOT EXISTS rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reward_type ENUM('parent_5h', 'parent_10h', 'parent_15h', 'teacher_voucher') NOT NULL,
    reward_title VARCHAR(200) NOT NULL,
    reward_description TEXT,
    reward_value DECIMAL(10,2) DEFAULT 0 COMMENT 'Ödül değeri (€)',

    hours_milestone INT NOT NULL COMMENT 'Kaç saat sonra verildi',
    awarded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_claimed BOOLEAN DEFAULT 0,
    claimed_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_user (user_id),
    INDEX idx_type (reward_type),
    INDEX idx_claimed (is_claimed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ödül basamakları konfigürasyonu
CREATE TABLE IF NOT EXISTS reward_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('student', 'parent') NOT NULL,
    hours_required INT NOT NULL,
    reward_type VARCHAR(50) NOT NULL,
    reward_title VARCHAR(200) NOT NULL,
    reward_description TEXT,
    reward_value DECIMAL(10,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,

    UNIQUE KEY uniq_role_hours (role, hours_required),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default milestone'ları ekle
INSERT INTO reward_milestones (role, hours_required, reward_type, reward_title, reward_description, reward_value) VALUES
('parent', 5, 'parent_5h', '5 Saat Ödülü', '%10 indirim kuponu - Sonraki ders ödemelerinde kullanabilirsiniz', 5.00),
('parent', 10, 'parent_10h', '10 Saat Ödülü', '%15 indirim kuponu + Dijital eğitim materyalleri', 10.00),
('parent', 15, 'parent_15h', '15+ Saat Ödülü', '%20 indirim kuponu + Ücretsiz 1 saat ders + Premium erişim', 20.00),
('student', 20, 'teacher_voucher', '20 Saat Hediye Çeki', '10€ Amazon Hediye Kartı', 10.00),
('student', 50, 'teacher_voucher', '50 Saat Hediye Çeki', '25€ Amazon Hediye Kartı', 25.00),
('student', 100, 'teacher_voucher', '100 Saat Hediye Çeki', '50€ Amazon Hediye Kartı', 50.00);
```

### 2.3 Backend API Endpoints

#### 2.3.1 Saat Kaydetme & Ödül Kontrolü
**Dosya**: `server/api/rewards/track_hours.php`

**İşlevler**:
- Ders saati kaydı
- Toplam saat hesaplama
- Otomatik ödül kontrolü
- Yeni ödül oluşturma

**Request**:
```json
{
  "agreement_id": 789,
  "hours_completed": 2.5,
  "notes": "22 Kasım matematik dersi tamamlandı"
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "total_hours": 7.5,
    "new_rewards": [
      {
        "id": 101,
        "title": "5 Saat Ödülü",
        "description": "%10 indirim kuponu",
        "value": "5.00"
      }
    ]
  },
  "message": "Saat kaydı başarılı"
}
```

#### 2.3.2 Ödülleri Listeleme
**Dosya**: `server/api/rewards/list.php`

**Response**:
```json
{
  "success": true,
  "data": {
    "total_hours": 7.5,
    "rewards": [
      {
        "id": 101,
        "reward_title": "5 Saat Ödülü",
        "reward_description": "%10 indirim kuponu",
        "reward_value": "5.00",
        "hours_milestone": 5,
        "is_claimed": false,
        "awarded_at": "2025-11-22 15:30:00"
      }
    ],
    "next_milestone": {
      "hours_required": 10,
      "reward_title": "10 Saat Ödülü",
      "reward_description": "%15 indirim kuponu + Dijital eğitim materyalleri",
      "reward_value": "10.00"
    }
  }
}
```

#### 2.3.3 Ödül Talep Etme
**Dosya**: `server/api/rewards/claim.php`

**Request**:
```json
{
  "reward_id": 101
}
```

**Response**:
```json
{
  "success": true,
  "message": "Ödül talep edildi! Kod e-posta adresinize gönderildi."
}
```

### 2.4 Frontend Komponentleri

#### 2.4.1 Ödüller Paneli
**Dosya**: `src/lib/components/RewardsPanel.svelte`

**Özellikler**:
- Toplam saat göstergesi
- İlerleme çubuğu (sonraki ödüle)
- Kazanılan ödüller listesi
- Ödül talep etme butonu
- Talep edilmiş ödül badge'i

**Kullanım**:
```svelte
<RewardsPanel />
```

#### 2.4.2 Dashboard Entegrasyonu
**Dosya**: `src/routes/panel/+page.svelte`

Panel sayfasına RewardsPanel komponenti eklenir.

---

## 3️⃣ PROFİL İYİLEŞTİRMELERİ

### 3.1 Sorun
Profillerde `null` değerleri "null" stringi olarak görünüyor.

### 3.2 Çözüm

#### 3.2.1 Backend Güncelleme
**Dosya**: `server/api/teachers/detail.php`

```php
// NULL değerleri temizle
$nullableFields = ['university', 'department', 'graduation_year', 'bio',
                   'city', 'zip_code', 'video_intro_url', 'experience_years'];

foreach ($nullableFields as $field) {
    if ($teacher[$field] === null || $teacher[$field] === '') {
        $teacher[$field] = null;
    }
}
```

#### 3.2.2 Frontend Güncellemeleri

**Dosya**: `src/routes/profil/[id]/+page.svelte`

```svelte
<!-- Üniversite -->
{#if teacher.university && teacher.department}
  {teacher.university} - {teacher.department}
{:else if teacher.university}
  {teacher.university}
{:else}
  <span class="text-gray-400 italic">Üniversite bilgisi mevcut değil</span>
{/if}

<!-- Mezuniyet yılı -->
{#if teacher.graduation_year}
  <p>Mezuniyet: {teacher.graduation_year}</p>
{/if}

<!-- Şehir -->
{#if teacher.city || teacher.zip_code}
  <p>{teacher.city || 'Şehir belirtilmemiş'}{#if teacher.zip_code}, PLZ: {teacher.zip_code}{/if}</p>
{/if}

<!-- Deneyim -->
{#if teacher.experience_years !== null && teacher.experience_years > 0}
  <p>{teacher.experience_years} yıl deneyim</p>
{/if}

<!-- Bio -->
{#if teacher.bio && teacher.bio.trim() !== ''}
  <div class="bio-section">
    <p>{teacher.bio}</p>
  </div>
{/if}
```

**Dosya**: `src/lib/components/TeacherCard.svelte`

```svelte
{#if teacher.university}
  <p class="text-sm text-gray-600 font-medium mt-1">{teacher.university}</p>
{/if}

{#if teacher.department}
  <p class="text-sm text-gray-500">{teacher.department}</p>
{:else if !teacher.university}
  <p class="text-sm text-gray-400 italic mt-1">Eğitim bilgisi belirtilmemiş</p>
{/if}
```

---

## 4️⃣ CV YÜKLEME KISITLAMASI

### 4.1 Kısıtlama Kuralları
- ✅ Sadece yıllık premium üyeler CV yükleyebilir
- ✅ Sadece PDF formatı kabul edilir
- ✅ Maksimum dosya boyutu: 5MB
- ✅ Premium süresi dolmuşsa yükleme yapılamaz

### 4.2 Backend API

#### 4.2.1 CV Yükleme Endpoint
**Dosya**: `server/api/upload/cv.php`

**Kontroller**:
1. Kullanıcı öğretmen mi? (role = 'student')
2. Premium üye mi? (is_premium = 1)
3. Premium süresi dolmamış mı? (premium_expires_at > NOW())
4. Dosya tipi PDF mi?
5. Dosya boyutu 5MB'dan küçük mü?

**Request**: Multipart form-data
```
cv: [PDF File]
```

**Response (Başarılı)**:
```json
{
  "success": true,
  "data": {
    "cv_url": "/uploads/cvs/cv_123_1732275600.pdf"
  },
  "message": "CV başarıyla yüklendi"
}
```

**Response (Premium Değil)**:
```json
{
  "success": false,
  "error": "Premium membership required",
  "message": "CV yükleme özelliği sadece premium üyeler içindir. Yıllık 10€ ile premium üye olabilirsiniz."
}
```

**Response (Premium Süresi Dolmuş)**:
```json
{
  "success": false,
  "error": "Premium membership expired",
  "message": "Premium üyeliğinizin süresi dolmuş. Yenilemek için hediye@dijitalmentor.de adresine mesaj gönderin."
}
```

### 4.3 Frontend Komponentleri

#### 4.3.1 CV Upload Komponenti
**Dosya**: `src/lib/components/CVUpload.svelte`

**Özellikler**:
- Premium üyelik kontrolü
- Dosya tipi/boyut validasyonu (client-side)
- Upload progress göstergesi
- Mevcut CV görüntüleme
- Premium modal (üye olmayanlar için)

**Premium Modal İçeriği**:
- Premium özellikleri listesi
- Ücret bilgisi (10€/yıl)
- E-posta adresi: hediye@dijitalmentor.de
- Nasıl üye olunur açıklaması

**Kullanım**:
```svelte
<CVUpload
  currentCvUrl={$authStore.user?.cv_url}
  on:uploaded={handleCVUploaded}
/>
```

#### 4.3.2 Ayarlar Sayfası Entegrasyonu
**Dosya**: `src/routes/panel/ayarlar/+page.svelte`

Sadece öğretmenlere (role='student') gösterilir:

```svelte
{#if $authStore.user?.role === 'student'}
  <CVUpload
    currentCvUrl={$authStore.user?.cv_url}
    on:uploaded={handleCVUploaded}
  />
{/if}
```

---

## 📦 DEPLOYMENT PLANI

### Adım 1: Database Migration
```bash
# Hostinger phpMyAdmin'e giriş yap
# SQL sekmesinde sırayla çalıştır:

1. database/migration_add_lesson_agreements.sql
2. database/migration_add_rewards.sql
```

### Adım 2: Upload Klasörü Oluştur
```bash
# Hostinger File Manager veya FTP ile:
mkdir -p uploads/cvs
chmod 755 uploads/cvs
```

### Adım 3: Backend Dosyaları Yükle
```
server/api/agreements/
  ├── create.php
  ├── respond.php
  └── list.php

server/api/rewards/
  ├── track_hours.php
  ├── list.php
  └── claim.php

server/api/upload/
  └── cv.php

server/api/teachers/
  └── detail.php (GÜNCELLEME)
```

### Adım 4: Frontend Dosyaları Ekle/Güncelle

**Yeni Komponentler**:
```
src/lib/components/
  ├── AgreementForm.svelte
  ├── AgreementCard.svelte
  ├── RewardsPanel.svelte
  └── CVUpload.svelte
```

**Güncellenecek Dosyalar**:
```
src/routes/panel/mesajlar/+page.svelte
src/routes/panel/+page.svelte
src/routes/panel/ayarlar/+page.svelte
src/routes/profil/[id]/+page.svelte
src/lib/components/TeacherCard.svelte
```

### Adım 5: Build & Deploy
```bash
npm run build
git add .
git commit -m "feat: Add lesson agreements, rewards system, profile improvements, and premium CV upload"
git push origin master

# GitHub Actions otomatik deploy yapacak
```

---

## 🔧 TEKNIK DETAYLAR

### Video Konferans Entegrasyonu

#### Jitsi Meet (Ücretsiz - Otomatik)
```php
$roomId = 'dijitalmentor-' . uniqid();
$meetingLink = "https://meet.jit.si/$roomId";
```

#### Google Meet (Gelecek)
- Google Calendar API gerekir
- OAuth 2.0 credential gerekir
- API key maliyeti: Ücretsiz (quota limitleri var)

#### Zoom (Gelecek)
- Zoom API gerekir
- Ücretli Zoom hesabı gerekir
- Webhook integration için

### Email Bildirimleri

**TODO**: Ödül sistemi için email gönderme

```php
// server/api/rewards/claim.php içinde eklenecek

// SMTP konfigürasyonu
$to = $user['email'];
$subject = "Ödülünüz Hazır!";
$message = "...";

// PHPMailer veya mail() kullanılabilir
```

### Premium Üyelik Yönetimi

**Mevcut Durum**: Manuel aktivasyon
- Kullanıcı hediye@dijitalmentor.de'ye Amazon kart gönderiyor
- Admin manuel olarak database'de `is_premium=1` yapıyor

**Gelecek İyileştirme**:
- Email parsing ile otomatik aktivasyon
- Payment gateway entegrasyonu (Stripe/PayPal)

---

## 📊 DATABASE ŞEMA ÖZETİ

### Yeni Tablolar

```
lesson_agreements
├── id (PK)
├── conversation_id (FK → conversations)
├── sender_id (FK → users)
├── recipient_id (FK → users)
├── subject_id (FK → subjects)
├── lesson_location (ENUM)
├── lesson_address
├── meeting_platform (ENUM)
├── meeting_link
├── hourly_rate
├── hours_per_week
├── start_date
├── notes
├── status (ENUM)
├── created_at
└── responded_at

lesson_hours_tracking
├── id (PK)
├── user_id (FK → users)
├── agreement_id (FK → lesson_agreements)
├── hours_completed
├── completed_at
└── notes

rewards
├── id (PK)
├── user_id (FK → users)
├── reward_type (ENUM)
├── reward_title
├── reward_description
├── reward_value
├── hours_milestone
├── awarded_at
├── is_claimed
└── claimed_at

reward_milestones
├── id (PK)
├── role (ENUM)
├── hours_required
├── reward_type
├── reward_title
├── reward_description
├── reward_value
└── is_active
```

### Güncellenecek Tablolar

```
teacher_profiles
└── cv_url (mevcut alan, yeni validation)

users
├── is_premium (mevcut)
└── premium_expires_at (mevcut)
```

---

## 🔒 GÜVENLİK KONTROLLER

### API Endpoint'leri
- ✅ JWT token doğrulama
- ✅ Role-based access control
- ✅ Resource ownership kontrolü
- ✅ Input validation
- ✅ SQL injection koruması (prepared statements)

### File Upload
- ✅ File type validation (MIME type)
- ✅ File size limit (5MB)
- ✅ Unique filename generation
- ✅ Secure directory permissions (755)
- ✅ Premium membership verification

### Frontend
- ✅ Client-side validation
- ✅ Premium status kontrolü
- ✅ XSS koruması (Svelte auto-escaping)

---

## 📈 PERFORMANS ÖNERİLERİ

### Database
- ✅ Index'ler eklenmiş (conversation_id, user_id, status, created_at)
- ✅ Foreign key constraints
- ⚠️ Gelecek: Query optimization (EXPLAIN kullanarak)

### Frontend
- ✅ Component lazy loading
- ✅ Image optimization (avatar resizing)
- ⚠️ Gelecek: Pagination (mesajlar, ödüller için)

### API
- ✅ Minimal data fetching
- ⚠️ Gelecek: Response caching
- ⚠️ Gelecek: Rate limiting

---

## 🐛 TEST PLANI

### Manuel Test Senaryoları

#### Mesajlaşma & Onay Formu
1. [ ] Öğretmen olarak veli ile konuşma başlat
2. [ ] Veli olarak onay formu gönder
3. [ ] Öğretmen olarak onay formu kabul et
4. [ ] Online ders için Jitsi link oluşturuldu mu kontrol et
5. [ ] Fiziksel ders için adres girişi çalışıyor mu test et

#### Ödül Sistemi
1. [ ] 5 saat ders kaydet, ödül otomatik verildi mi?
2. [ ] Ödül talep et butonu çalışıyor mu?
3. [ ] İlerleme çubuğu doğru gösteriyor mu?
4. [ ] Email bildirimi gönderildi mi? (TODO)

#### CV Upload
1. [ ] Premium olmayan kullanıcı yüklemeye çalışınca modal açılıyor mu?
2. [ ] Premium kullanıcı PDF yükleyebiliyor mu?
3. [ ] JPG/PNG yüklemeye çalışınca hata veriyor mu?
4. [ ] 6MB dosya yüklemeye çalışınca hata veriyor mu?
5. [ ] Premium süresi dolmuş kullanıcı yükleyemiyor mu?

#### Profil İyileştirmeleri
1. [ ] NULL değerleri gizleniyor mu?
2. [ ] "Bilgi mevcut değil" mesajı gösteriliyor mu?
3. [ ] Dolu alanlar normal gösteriliyor mu?

---

## 📞 DESTEK & İLETİŞİM

### Özellik İstekleri
- GitHub Issues: [Repository link]
- Email: [Developer email]

### Bug Raporlama
- Format: [Bug başlığı] - [Hangi sayfada] - [Ne olması gerekiyor]
- Ekran görüntüsü ekleyin
- Browser/OS bilgisi verin

---

## 📝 NOTLAR

### Önemli Hatırlatmalar
- [ ] Database migration'ları production'a atmadan önce backup alın
- [ ] uploads/cvs klasörü .gitignore'da olduğundan emin olun
- [ ] Email SMTP ayarlarını production'da yapın
- [ ] Premium üyelik için payment gateway entegrasyonu düşünün

### Gelecek Geliştirmeler
- [ ] Push notification desteği
- [ ] Mobil uygulama
- [ ] Video call embed (iframe içinde)
- [ ] Otomatik ders hatırlatıcı
- [ ] Ödeme sistemi entegrasyonu
- [ ] Multi-language support

---

**Hazırlayan**: Claude Code
**Tarih**: 22 Kasım 2025
**Versiyon**: 1.0
