# Mock Mode vs Production Mode

## Current Status: MOCK MODE ✅

Sistem şu anda **mock mode**'da çalışıyor. Bu demektir ki:
- ❌ Yeni kullanıcılar database'e kaydedilmiyor
- ❌ Değişiklikler kalıcı değil (server restart'ta sıfırlanıyor)
- ✅ Hızlı geliştirme ve test için ideal
- ✅ Database bağlantısı gerektirmiyor

## Production Mode'a Geçiş

### Adım 1: Database Kurulumu

1. **Hostinger phpMyAdmin'e giriş yapın**
2. **Database'i oluşturun veya güncelleyin:**
   ```bash
   # Yeni kurulum için:
   mysql -u u553245641_dijitalmentor -p u553245641_dijitalmentor < database/install-hostinger.sql
   
   # Mevcut database'i güncellemek için:
   mysql -u u553245641_dijitalmentor -p u553245641_dijitalmentor < database/migrate-approval-status.sql
   mysql -u u553245641_dijitalmentor -p u553245641_dijitalmentor < database/migrate-premium-features.sql
   ```

### Adım 2: API Dosyalarını Deploy Edin

1. **`api/` klasörünü Hostinger'a yükleyin:**
   ```
   public_html/
   ├── api/
   │   ├── config/
   │   ├── auth/
   │   ├── teachers/
   │   ├── profile/
   │   ├── subjects/
   │   └── requests/
   ```

2. **`api/config/database.php` dosyasını düzenleyin:**
   - `DB_PASS`: Hostinger database şifrenizi girin
   - `JWT_SECRET`: Güvenli bir random string girin

3. **`uploads/` klasörünü oluşturun:**
   ```bash
   mkdir -p public_html/uploads/avatars
   mkdir -p public_html/uploads/cv
   chmod 755 public_html/uploads
   ```

### Adım 3: Environment Variables

**Lokal Geliştirme (.env):**
```env
PUBLIC_API_URL=http://localhost:8000/api
PUBLIC_MOCK_MODE=true
```

**Production (GitHub Actions veya .env):**
```env
PUBLIC_API_URL=https://dijitalmentor.de/api
PUBLIC_MOCK_MODE=false
```

### Adım 4: Test

1. **API endpoint'lerini test edin:**
   - https://dijitalmentor.de/api/subjects/list.php
   - https://dijitalmentor.de/api/teachers/list.php

2. **Yeni kullanıcı kayıt edin:**
   - Kayıt formunu doldurun
   - Database'de yeni kullanıcının oluştuğunu kontrol edin

3. **Öğretmen listesini kontrol edin:**
   - Onaylı öğretmenlerin listede göründüğünü doğrulayın

## Şu Anki Durum

✅ **Mock Mode Aktif**
- `.env` dosyasında `PUBLIC_MOCK_MODE=true`
- Yeni kullanıcılar mock data'ya ekleniyor (geçici)
- Server restart'ta veriler sıfırlanıyor

❌ **Production Mode İçin Gerekli:**
1. Database kurulumu
2. API deployment
3. Environment variable değişikliği (`PUBLIC_MOCK_MODE=false`)

## Hızlı Geçiş

Production'a geçmek için:

```bash
# 1. Database'i güncelle (Hostinger'da)
# 2. API dosyalarını yükle
# 3. .env dosyasını güncelle:
echo "PUBLIC_MOCK_MODE=false" > .env
echo "PUBLIC_API_URL=https://dijitalmentor.de/api" >> .env

# 4. Uygulamayı yeniden başlat
npm run dev
```

## Sorun Giderme

**Yeni kullanıcılar görünmüyor:**
- ✅ `PUBLIC_MOCK_MODE=false` olduğundan emin olun
- ✅ API endpoint'lerinin çalıştığını test edin
- ✅ Browser console'da hata olup olmadığını kontrol edin

**Database bağlantı hatası:**
- ✅ `api/config/database.php` dosyasındaki bilgileri kontrol edin
- ✅ Hostinger database'inin aktif olduğunu doğrulayın

**CORS hatası:**
- ✅ `api/.htaccess` dosyasının yüklendiğinden emin olun
- ✅ CORS header'larının doğru olduğunu kontrol edin
