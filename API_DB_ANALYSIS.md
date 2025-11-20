# API & Veritabanı Uyum Analizi

Bu doküman DijitalMentor projesinin mevcut SvelteKit frontend'i, PHP tabanlı API katmanı ve `database/last_database.sql` şeması arasındaki uyumu incelemek ve özellikle yeni kayıt olan kullanıcıların verilerinin `NULL` dönmesine neden olan problemleri ortaya koymak için hazırlandı.

## Genel Proje Yapısı
- Frontend: `src/` altında çalışan SvelteKit uygulaması. API istemcisi `src/lib/utils/api.js` dosyasında tanımlanıyor ve `.env` içindeki `PUBLIC_API_URL` değişkeni ile PHP API'ye bağlanıyor ( `.env:1` ).
- Backend: Aynı depo içinde iki farklı PHP API kopyası bulunuyor. `server/api` klasörü canlı ortamda kullanılan eski sürüm, `api/` klasörü ise yeni şema ile uyumlu olacak şekilde hazırlanan ama henüz devreye alınmamış sürüm.
- Veritabanı: `database/last_database.sql` MySQL şeması; `users`, `teacher_profiles`, `subjects`, `teacher_subjects`, `lesson_requests`, `reviews` tablolarını içeriyor ( `database/last_database.sql:17-104` ). Şemada `unlock_requests`, mesajlaşma vb. tablolar bulunmuyor.

## Tespit Edilen Tutarsızlıklar

1. **Kayıt formu ile API arasında eksik alanlar**
   - Frontend kayıt formu şehir ve posta kodu topluyor ( `src/routes/kayit/+page.svelte:7-118` ) ve bunları `/auth/register.php` endpoint'ine gönderiyor.
   - Canlıda kullanılan `server/api/auth/register.php` sadece `phone`, `password_hash`, `full_name`, `role` alanlarını insert ediyor ( `server/api/auth/register.php:13-54` ) ve `users.city`, `users.zip_code`, `users.approval_status`, `users.is_premium` gibi kolonları hiç set etmiyor.
   - Bu yüzden yeni kayıt olan kullanıcıların şehir/posta kodu bilgileri `NULL` kalıyor ve panelde ya da kartlarda boş görünüyor.

2. **Login cevabında eksik kullanıcı alanları**
   - Frontend panel bileşenleri kullanıcı objesinden `city`, `zip_code`, `approval_status`, `is_premium`, `bio` gibi alanları bekliyor (örneğin `src/routes/panel/+page.svelte:25-55` ve `src/routes/panel/ayarlar/+page.svelte:27-55`).
   - `server/api/auth/login.php` ise sadece `id`, `full_name`, `role`, `phone`, `avatar_url` döndürerek diğer tüm alanları yok sayıyor ( `server/api/auth/login.php:38-49` ). Login sonrası `localStorage`'a bu eksik obje yazıldığı için kullanıcıya ait tüm diğer alanlar `undefined/null` kalıyor.

3. **Öğretmen profili tohumlama eksikliği**
   - Kayıt sırasında öğretmen için `teacher_profiles` satırı yalnızca `user_id` ile oluşturuluyor ( `server/api/auth/register.php:51-53` ). Şema ise şehir, posta kodu, saatlik ücret, bio vb. alanları içeriyor ( `database/last_database.sql:45-67` ).
   - Bu nedenle `server/api/teachers/list.php`'in döndürdüğü `tp.city`, `tp.hourly_rate` gibi alanlar yeni öğretmenlerde `NULL` geliyor ve `src/lib/components/TeacherCard.svelte` veya profil sayfaları boş alan gösteriyor.

4. **Harita için gereken koordinat alanları API'de yok**
   - `MapView` bileşeni marker çizebilmek için her öğretmen nesnesinde `lat`/`lng` bekliyor ( `src/lib/components/MapView.svelte:48-68` ).
   - Yeni `api/teachers/list.php` sahte koordinatlar ekliyor olsa da canlıdaki `server/api/teachers/list.php` hiçbir koordinat üretmiyor, dolayısıyla harita boş kalıyor.

5. **Kimlik doğrulama yardımcı fonksiyonu eksik**
   - `server/api/requests/create.php` ve `server/api/requests/my_requests.php` gibi endpoint'ler `authenticate()` fonksiyonunu çağırıyor ( `server/api/requests/create.php:5` ), fakat `server/api/config/auth.php` içinde böyle bir fonksiyon tanımlı değil; yalnızca `requireAuth()` var. Bu durum çağrı anında fatal hata üretir.

6. **Şemada olmayan tabloları kullanan endpoint'ler**
   - `server/api/unlock/request.php` `unlock_requests` tablosuna yazmaya çalışıyor ( `server/api/unlock/request.php:40-52` ), ancak mevcut SQL şemasında böyle bir tablo tanımlı değil. Bu endpoint çalıştırıldığında “table doesn't exist” hatası alınır.

7. **Çift API kaynağı ve farklı token formatları**
   - Frontend `.env` ayarıyla `server/api`yi hedefliyor ( `.env:1` ), bu sürüm JWT üretip `requireAuth()` beklerken, kökteki `api/` klasörü `mock-token-{id}` formatına dayanıyor ( `api/config/database.php:37-61` ).
   - İki farklı sürümün aynı depoda bulunması, hangi endpoint'in güncel olduğuna dair belirsizlik yaratıyor ve değişikliklerin yanlış klasöre uygulanmasına yol açabiliyor.

## Önerilen Çözüm Adımları

1. **Kayıt endpoint'ini güncelleyin**
   - Request payload'ından `city`, `zip_code`, `email`, `approval_status` ve rolüne göre başlangıç `is_premium`/`is_verified` değerlerini okuyup `users` tablosuna yazın.
   - Öğretmen kaydında `teacher_profiles` tablosunu da şehir, posta kodu ve diğer varsayılanlarla güncelleyin.

2. **Kimlik doğrulama cevabını genişletin**
   - `server/api/auth/login.php` (ve başarıyla tamamlanan kayıt cevabı) içinde kullanıcı tablosundaki tüm gerekli alanları, gerekiyorsa `teacher_profiles` join'i ile birlikte döndürün. Hassas verileri (örn. `password_hash`) çıkardıktan sonra frontende beklendiği gibi `city`, `zip_code`, `approval_status`, `is_premium`, `bio`, `hourly_rate`, `experience_years` vb. alanları sağlayın.
   - Opsiyonel olarak `/auth/me.php` benzeri bir endpoint ekleyip frontend'in oturum açtıktan sonra “me” bilgilerini yeniden çekmesine izin verin.

3. **Harita ve kart verileri için koordinat/konum üretin**
   - `server/api/teachers/list.php` ve `detail.php` içinde şehir/PLZ bilgisinden türetilen (örn. statik bir dictionary kullanarak) `lat` ve `lng` değerleri ekleyin. Bu mümkün değilse dahi en azından frontende boş gelmeyecek default koordinatlar gönderin.

4. **Eksik yardımcı fonksiyonları düzeltin**
   - `authenticate()` çağrılarını `requireAuth()` ile değiştirin veya `requireAuth()`'a benzer şekilde kullanıcı nesnesi döndüren bir `authenticate()` fonksiyonu ekleyin. Bu değişiklik talepleri oluşturma/listeleme akışlarını çalışabilir hale getirecek.

5. **Şemayı API ile hizalayın**
   - `unlock_requests` (ve ileride planlanan mesajlaşma tablolari) için `database/last_database.sql` içine `CREATE TABLE` komutları eklenip yeniden deploy edilmeli.
   - Şema güncellemesi sonrasında Hostinger veritabanına migration uygulanmalı.

6. **Tek bir API kaynağına konsolide olun**
   - `server/api` klasörünü tek gerçek kaynak olarak belirleyip `api/` klasöründeki kodu ya tamamen kaldırın ya da aynı yerde güncelleyip deploy edin. Böylece `api/config/database.php` ve `server/api/config/db.php` arasında çelişen yapılandırmalar ortadan kalkar.

7. **Mevcut veriyi geriye dönük düzeltin**
   - Halihazırda kayıt olmuş kullanıcıların şehir/posta kodu kolonları `NULL` olduğu için, mümkünse manual olarak `teacher_profiles.city` veya başvuru formundan toplanan bilgilerle `users.city`/`zip_code` kolonlarını güncelleyin. Aksi durumda frontend bu kullanıcılar için hep boş değer gösterecektir.

Bu adımlar tamamlandığında frontend'in beklediği kullanıcı objesi ile veritabanı/ API katmanı uyumu sağlanacak, yeni kayıt olan kullanıcıların verileri `NULL` görünmeyecek ve ilerideki özellikler (harita, premium filtreleri, kilit açma talepleri vb.) sorunsuz çalışacaktır.
