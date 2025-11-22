# TODOs (geliştirme sonrası yapılacaklar)

- [ ] **Veritabanı migrasyonları**: `database/migration_add_lesson_agreements.sql` ve `database/migration_add_rewards.sql` sırasıyla çalıştır. `reward_milestones` insert'i `INSERT IGNORE`, tekrar çalışsa da sorun yok.
- [ ] **API deploy**: `server/api/agreements/*`, `server/api/rewards/*`, `server/api/upload/cv.php` dosyalarını Hostinger’a yükle. Dizini ve dosya izinlerini (644/755) kontrol et.
- [ ] **Dosya sistemi**: sunucuda `uploads/cvs` klasörünü oluştur, izin 755. Klasör `.gitignore` kapsamında kalsın.
- [ ] **Env/URL**: Frontend `PUBLIC_API_URL` değeri yeni endpoint’leri gösterecek şekilde tam URL olmalı (örn. `https://api.dijitalmentor.de/server/api`).
- [ ] **Test senaryoları**: 
  - Online ders için onay formu oluştur → Jitsi linki üretildi mi, link stabil mi.
  - Alıcı taraf kabul/ret/iptal akışı çalışıyor mu; gönderende iptal yetkisi var mı.
  - Kabul edilmiş anlaşmada saat ekle → RewardsPanel’de toplam saat artıyor mu, eşik gelince ödül oluşturuluyor mu, “Ödülü Al” çalışıyor mu.
  - Premium olmayan öğretmen CV yükleyemiyor, premium olan PDF ≤5MB yükleyebiliyor; cv_url güncelleniyor.
- [ ] **PHP modülü**: `random_bytes` / OpenSSL uzantısı aktif olmalı (Jitsi room id üretimi).
- [ ] **SPA fallback**: Statik host (Hostinger) için `static/.htaccess` dosyasındaki rewrite kuralını canlıya koy; hard refresh 404’lerini engeller.
- [ ] **Vercel fallback**: `vercel.json` eklendi. Vercel projesinde build komutu `npm run build`, output `build` (adapter-static). Rewrite: `/(.*) -> /index.html`. Deploy’da bu dosyanın kökten alındığını ve distDir’in `build` olarak ayarlandığını doğrula; aksi halde refresh 404 devam eder.
- [ ] **UI doğrulamaları**: 
  - Mesajlar sayfasında öğretmen rolüyle öğretmen profiline gidildiğinde “Mesaj Gönder” butonu görünmemeli (güncellendi, prod’da doğrula).
  - CV yükleme butonu premium olmayanlarda dosya seçtirmemeli, sadece “Premium gerekli” butonu ve modal açılmalı (güncellendi, prod’da kontrol et).
