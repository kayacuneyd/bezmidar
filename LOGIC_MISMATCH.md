# Proje Mantığına Aykırı Dosya/Klasör Notları

- **Beklenen API dizini farklı:** README yapısında PHP API'nin kökte `api/` altında olması gerektiği belirtiliyor, ancak kodlar `server/api/` altına yerleştirilmiş durumda. Bu iki dizin adı arasında seçim yapılmadığı için deploy/güncelleme adımlarında karışıklık riski var. 【F:README.md†L33-L70】【F:server/api/seed.php†L1-L29】
- **Seed scripti hatalı hedef dosyaya bakıyor:** `server/api/seed.php` veritabanı tohumlaması için `../../database/seed.sql` dosyasını okumaya çalışıyor, fakat depoda yalnızca `database/last_database.sql` bulunuyor; bu nedenle script çalıştırıldığında dosya bulunamadı hatası verecektir. 【F:server/api/seed.php†L10-L28】【F:database/last_database.sql†L1-L40】
- **Platforma özel geliştirme scripti:** `mobile-dev.sh` içindeki `ipconfig` ve `sed -i ''` çağrıları yalnızca macOS'a özgü; Linux tabanlı CI/hosting ortamında çalışmayacakları için depo içinde tutulması proje mantığına ters düşüyor. 【F:mobile-dev.sh†L1-L29】
- **Farklı bir markaya ait belge:** `BEZMIDAR_BRANDING_KIT.md` tamamen "Bezmidar" markasına ait içerik barındırıyor; DijitalMentor ürün kapsamı dışında kaldığından depo içerisinde bulunması karışıklığa yol açabilir. 【F:BEZMIDAR_BRANDING_KIT.md†L1-L55】

## Önerilen kesin aksiyonlar (yeni mimariye uyumlu)

- **API dizinini tekilleştir ve public API'yi sabitle:** README/CI komutlarını `server/api/` altındaki mevcut PHP API'ye göre güncelleyin ya da kodu köke `api/` klasörü olarak taşıyın; iki yolu birden tutmayın. Public endpoint'i `api.dijitalmentor.de` altında çalışacak şekilde yönlendirin ve CORS/URL tabanlı ayarları yeni domainden besleyin.【F:README.md†L33-L70】【F:server/api/seed.php†L1-L29】
- **Seed ve migration akışını public API'ye bağlayın:** `server/api/seed.php` içindeki hedefi gerçek bir SQL dosyasına yönlendirin (`database/last_database.sql`'e göre güncelleyin ya da eksik `database/seed.sql` dosyasını ekleyin). Verileri doğrudan API servisinin erişebildiği bir veritabanına tohumlayın ve bu adımı CI/CD hattında API dağıtımından önce otomatikleştirin.【F:server/api/seed.php†L10-L28】【F:database/last_database.sql†L1-L40】
- **SvelteKit'i Vercel için serverfull modda yapılandırın:** `adapter-static` yaklaşımından çıkıp Vercel'in varsayılan adaptörünü/edge seçeneğini kullanacak şekilde `svelte.config.js` ve dağıtım ayarlarını güncelleyin. Public API çağrılarını `api.dijitalmentor.de` hedefini kullanacak biçimde env değişkenleriyle soyutlayın; CDN/önbellek ayarlarını bu domainde test edin.【F:svelte.config.js†L1-L16】【F:vite.config.js†L1-L32】
- **Platform bağımlı scripti genelleyin:** `mobile-dev.sh`'yi Linux/CI ortamlarına uyumlu hale getirmek için komutları yeniden yazın araç klasörüne taşıyın; yeni Vercel + API akışında bu scriptin gereksinimini netleştirin.【F:mobile-dev.sh†L1-L29】
- **Marka dışı içeriği temizleyin:** `BEZMIDAR_BRANDING_KIT.md` dosyasını DijitalMentor dışı bir arşive alın veya depodan çıkarın; public API ve yeni frontend alan adıyla ilişkili olmayan bütün marka materyallerini ayrıştırın.【F:BEZMIDAR_BRANDING_KIT.md†L1-L55】


# LOGIC MISMATCH – Özet ve Aksiyon Listesi

Bu dosya, frontend beklentileri ile mevcut PHP API/DB davranışı arasındaki uyumsuzlukları ve çözüm adımlarını özetler. Kaynak: `API_DB_ANALYSIS.md`, mevcut kod incelemesi.

## Durum Özeti
- Frontend `city/zip_code/bio/premium` gibi alanları bekliyor; API bazılarını hiç döndürmüyor ya da kaydetmiyor.
- İki farklı API kopyası var (`server/api` = canlı, kökteki `api/` = legacy). Hangisine deploy edileceği karışık.
- Harita ve kartlar için `lat/lng` üretimi yetersiz/eksik.
- CORS ve env yönetimi paylaşımlı hostingte sorun çıkarıyor.

## Uyumsuzluklar ve Yapılacaklar
- [ ] Kayıt akışı: `server/api/auth/register.php` kullanıcıya `city`/`zip_code`/`email`/`approval_status` yazmalı, öğretmen için `teacher_profiles` satırını varsayılanlarla doldurmalı.
- [ ] Login cevabı: `server/api/auth/login.php` (ve mümkünse `/auth/me`) tüm gerekli alanları (city, zip_code, approval_status, is_premium, bio, hourly_rate, experience_years, teacher_city/zip, premium_dates) join edip dönmeli.
- [ ] Harita/lat-lng: `server/api/teachers/list.php` ve `detail.php` şehir/PLZ’den koordinat eklemeli; fallback/default boş kalmamalı.
- [ ] Auth helper: `authenticate()` çağrıları `requireAuth()` ile hizalanmalı ya da aynı imzayla eklenmeli; aksi halde fatal hata.
- [ ] Şema uyumu: `unlock_requests` ve diğer kullanılan tablolar `.sql` şemasına eklenip migration uygulanmalı.
- [ ] API konsolidasyonu: Tek kaynak olarak `server/api` belirlenmeli; legacy `api/` ya kaldırılmalı ya da aynı yere taşınmalı.
- [ ] CORS/env: Subdomain `api.dijitalmentor.de` için env (`DB_*`, `JWT_SECRET`, opsiyonel `ALLOWED_ORIGINS`) ve CORS whitelist’inin çalıştığı doğrulanmalı.

## Önceliklendirme
1) Veri kaybı ve login yanıtı (kayıt/login dosyaları)  
2) Auth helper ve fatal hatalar  
3) Harita için koordinat üretimi  
4) Şema uyumu (unlock_requests)  
5) API konsolidasyonu ve dokümantasyon

## Notlar
- Frontend `PUBLIC_API_URL` olarak `https://api.dijitalmentor.de` bekliyor; CORS whitelist buna göre güncellendi.  
- Geocoding için `config/helpers.php` içinde Nominatim fallback var; outbound kısıt varsa defaultlar kullanılacak.  
- Env dosyaları/`SetEnv` değerleri repo’ya konulmamalı; subdomain root’unda tutulmalı.