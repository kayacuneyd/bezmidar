## DijitalMentor Yol Haritası (Teknik & İçerik Odaklı)

Bu yol haritası, **NEW_UPDATES_STRATEGIES.md** dokümanındaki öneriler içinden
özellikle site üzerinde teknik / içerik tarafında yapılabilecek işleri baz alır.
Pazarlama kanalı (reklam, sosyal medya, influencer vb.) çalışmaları kapsam dışıdır.

---

## Faz 1 – Konumlandırma ve Metin İyileştirmeleri

**Amaç:** Platformun yalnızca “özel ders” değil, **abi/abla mentorluk** sunduğunu
netleştirmek; güven ve rol model temalarını ana sayfa ve temel sayfalara yansıtmak.

- [x] Ana sayfa hero metnini “mentorluk + rol model” vurgusuyla güncelle
- [x] “Neden DijitalMentor?” kartlarını güven, rol model, kültürel bağ ve yakınlık
      temasına göre yeniden yaz
- [x] “Nasıl Çalışır?” sayfasındaki adımları mentorluk ve ebeveyn bakış açısı ile
      uyumlu hale getir
- [x] “Hakkımızda” sayfasında misyonu Almanya’daki Türk ailelere ve “abi/abla
      mentorluk” modeline göre yeniden çerçevele
- [ ] SSS’de ebeveynlerin tipik sorularına (güvenlik, kimlik doğrulama, süreç,
      ücretler) yönelik birkaç ek madde ekle

---

## Faz 2 – Güven, Şeffaflık ve Sosyal Kanıt

**Amaç:** Velilerin “çocuğumu kime emanet ediyorum?” sorusuna net, şeffaf ve
güven verici cevaplar göstermek.

- [ ] Ana sayfaya /ara veya /hakkimizda üzerinden erişilen bir “Güven” bloğu ekle:
  - “Üniversite belgesi doğrulama” sürecini metinle anlat
  - Onaylı mentor rozetini görsel olarak açıklayan mini bileşen ekle
- [ ] Öğretmen profil kartı ve profil sayfasına:
  - Onaylı / beklemede rozetlerini net metin ve tooltip ile göster
  - “Mentorun hikayesi” (kısa bio) alanının görünürlüğünü artır
- [ ] Yorum / değerlendirme alanı için UI hazırlığı (şimdilik statik placeholder);
      backend hazır olduğunda gerçek yorumları bağlamak kolay olsun

---

## Faz 3 – Kullanıcı Deneyimi ve Dönüşüm Optimizasyonu

**Amaç:** Velilerin öğretmen bulma ve talep oluşturma akışını mümkün olduğunca
az adımla ve net yönlendirmelerle tamamlamasını sağlamak.

- [ ] Ana sayfada CTA’ları (Öğretmen Ara, Kayıt Ol) sayfa boyunca tekrar eden
      “sekonder” butonlar olarak yerleştir
- [ ] /ara sayfasında:
  - Filtre alanlarının üzerine kısa açıklama ekle (örn. “Şehir seçerek size en
    yakın mentorları bulalım”)
  - Filtre değiştiğinde öğretmen listesinin güncellendiğini gösteren loading
    veya küçük bir bilgi yazısı ekle
- [ ] /ders-talepleri akışında:
  - “Yeni Talep Oluştur” sayfasındaki form alanları için daha açıklayıcı
    placeholder ve kısa ipuçları ekle
  - Talep oluşturma sonrası başarı ekranını, bir sonraki adımı net anlatacak
    şekilde düzenle (örneğin “Öğretmenler başvurdukça SMS / e‑posta ile haber
    vereceğiz” gibi)

---

## Faz 4 – İçerik Yapısı ve Blog

**Amaç:** SEO ve güveni destekleyen, ebeveynlere değer sunan içerik altyapısını
kurmak.

- [ ] Blog yapısını netleştir:
  - Kategori örnekleri: “Almanya’da Okul Sistemi”, “Mentorluk Hikayeleri”,
    “Veliler İçin Pratik İpuçları”
  - Blog ana sayfasına kategori filtreleri ve kısa özetler ekle
- [ ] İlk blog yazılarını planla (dokümandaki 3–4 başlık referans alınabilir)
- [ ] Hakkımızda / blog / SSS sayfalarını iç linklerle birbirine bağla

---

## Faz 5 – Veri, Analitik ve Uzun Vadeli Teknik Yol

**Amaç:** Kullanıcı davranışını ölçmek ve ileride mobil uygulama / gelişmiş
özelliklere hazırlık yapmak.

- [ ] Önemli olaylar için (kayıt, ilk talep oluşturma, mesajlaşma başlangıcı,
      onay formu gönderme) frontend tarafında event tracking altyapısı oluştur
      (örneğin data‑layer ya da basit bir logging servisi)
- [ ] Mesajlaşma ve onay formu sisteminde:
  - Durum/kapsam metriklerini (toplam anlaşma sayısı, kabul oranları vb.)
    hesaplamaya uygun basit SQL görünümleri veya endpoint’ler tasarla
- [ ] Uzun vadede:
  - Mobil uygulama için API’lerin kimlik doğrulama ve versiyonlama standartlarını
    belirle
  - Platform içi takvim / ders planlama özelliği için veri modelini taslak
    halinde dokümante et

---

## Önceliklendirme Önerisi

1. **Faz 1 – Konumlandırma ve metinler**  
   Hemen uygulanabilir, geliştirme maliyeti düşük, dönüşüme etkisi yüksek.

2. **Faz 2 – Güven ve şeffaflık**  
   Velinin karar verme sürecinde kritik; küçük arayüz eklemeleriyle başlanabilir.

3. **Faz 3 – UX / dönüşüm optimizasyonu**  
   Akışlar stabil hale geldikçe, form ve filtre deneyimi iyileştirilebilir.

4. **Faz 4 – İçerik ve blog**  
   Orta vadeli; SEO ve marka algısı için önemli.

5. **Faz 5 – Analitik ve ileri seviye teknik yol**  
   Platform ürün/pazar uyumu kazandıkça uygulanması gereken adımlar.

