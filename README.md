# DijitalMentor - Özel Ders Platformu

Almanya'daki Türk aileler için özel ders öğretmeni bulma platformu.

## 🚀 Hızlı Başlangıç

### Geliştirme Ortamı

```bash
# Bağımlılıkları yükle
npm install

# Geliştirme sunucusunu başlat
npm run dev

# Production build
npm run build
```

### Database Kurulumu

1. **Hostinger phpMyAdmin'e giriş yapın**
2. **`u553245641_dijitalmentor` database'ini seçin**
3. **`database/last_database.sql` dosyasını çalıştırın**

```sql
-- Veya manuel olarak:
mysql -u u553245641_dijitalmentor -p u553245641_dijitalmentor < database/last_database.sql
```

### API Kurulumu

1. **`api/` klasörünü Hostinger'a yükleyin:**
   - Konum: `public_html/server/api/`

2. **`api/config/database.php` dosyasını kontrol edin:**
   - Database bilgileri doğru mu?

3. **Test edin:**
   - https://dijitalmentor.de/server/api/subjects/list.php

### Environment Variables

**Lokal Geliştirme (`.env`):**
```env
PUBLIC_API_URL=https://api.dijitalmentor.de/server/api
PUBLIC_SITE_URL=https://dijitalmentor.de
PUBLIC_MOCK_MODE=false
```

> **Not:** `PUBLIC_API_URL` değerini mutlaka tam bir URL olarak tanımlayın. Vercel üzerinde göreli bir yol (örn. `/server/api`)
> kullanırsanız, frontend barındırıldığı hostta karşılığı olmadığı için 404 sayfasına düşer. `https://api.dijitalmentor.de/server/api`
> ya da kullandığınız API domainini eksiksiz yazın.

**Production (GitHub Actions):**
- `.github/workflows/deploy.yml` dosyasında ayarlı

## 📁 Proje Yapısı

```
dijitalmentor/
├── src/                    # SvelteKit kaynak kodları
│   ├── lib/
│   │   ├── components/    # Svelte bileşenleri
│   │   ├── stores/        # State yönetimi
│   │   └── utils/         # Yardımcı fonksiyonlar
│   └── routes/            # Sayfa rotaları
├── api/                   # PHP Backend API
│   ├── config/           # Database bağlantısı
│   ├── auth/             # Kimlik doğrulama
│   ├── teachers/         # Öğretmen endpoint'leri
│   ├── profile/          # Profil yönetimi
│   └── subjects/         # Ders konuları
├── database/             # SQL şemaları
└── static/               # Statik dosyalar
```

## 🎯 Özellikler

### Kullanıcı Yönetimi
- ✅ Kayıt olma (Öğretmen/Veli)
- ✅ Giriş yapma (JWT token)
- ✅ Profil düzenleme
- ✅ Fotoğraf yükleme
- ✅ Onay sistemi (öğretmenler için)

### Öğretmen Özellikleri
- ✅ Profil oluşturma
- ✅ Ders konuları seçme
- ✅ Saatlik ücret belirleme
- ✅ Bio ve deneyim bilgileri
- ✅ CV yükleme (premium)
- ✅ Ders taleplerine erişim (premium)

### Veli Özellikleri
- ✅ Öğretmen arama (şehir, ders, fiyat)
- ✅ Harita üzerinde görüntüleme
- ✅ Ders talebi oluşturma
- ✅ Öğretmen iletişim bilgileri (premium)

### Premium Üyelik
- 💰 **10€/Yıl** - Amazon Hediye Kartı ile
- 📧 **Aktivasyon:** hediye@dijitalmentor.de
- ✨ **Özellikler:**
  - Öğretmenler: Veli iletişim bilgileri + CV yükleme
  - Veliler: Öğretmen WhatsApp bilgileri

## 🔧 Deployment

### Otomatik Deployment (GitHub Actions)

```bash
git add .
git commit -m "Your message"
git push origin master
```

GitHub Actions otomatik olarak:
1. Build yapar
2. Hostinger'a deploy eder
3. 2-3 dakika içinde canlıya alır

### Manuel Deployment

```bash
# Build oluştur
npm run build

# FTP ile yükle
# build/ klasörünü public_html/ içine
```

## 📊 Database Şeması

### Ana Tablolar

- **users** - Kullanıcı bilgileri
- **teacher_profiles** - Öğretmen profilleri
- **subjects** - Ders konuları
- **teacher_subjects** - Öğretmen-Ders ilişkisi
- **lesson_requests** - Ders talepleri
- **reviews** - Değerlendirmeler

### Önemli Alanlar

- `approval_status` - Hesap onay durumu (pending/approved/rejected)
- `is_premium` - Premium üyelik durumu
- `premium_expires_at` - Premium bitiş tarihi

## 🐛 Sorun Giderme

### API Bağlantı Hatası

```bash
# URL'yi kontrol edin
curl https://dijitalmentor.de/server/api/subjects/list.php

# CORS hatası varsa .htaccess kontrol edin
```

### Database Bağlantı Hatası

```bash
# Database bilgilerini kontrol edin
# api/config/database.php
```

### Yeni Kullanıcılar Görünmüyor

1. `approval_status = 'approved'` mi kontrol edin
2. Öğretmen ise `teacher_profiles` oluşturuldu mu?
3. Database migration çalıştırıldı mı?

## 📝 Demo Kullanıcılar

**Öğretmen:**
- Telefon: +491234567801
- Şifre: password

**Veli:**
- Telefon: +491234567901
- Şifre: password

## 🔐 Güvenlik

- Şifreler bcrypt ile hash'leniyor
- JWT token ile kimlik doğrulama
- CORS koruması aktif
- SQL injection koruması (PDO prepared statements)

## 📞 Destek

Sorularınız için: info@dijitalmentor.de

---

**Son Güncelleme:** 21.11.2025  
**Versiyon:** 1.0.0
