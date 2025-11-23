<script>
  import Button from '$lib/components/Button.svelte';
</script>

<svelte:head>
  <title>DijitalMentor - Gurbet Çocuklarına Mentor Desteği</title>
  <meta
    name="description"
    content="Almanya'daki Türk ailelerini, çocuklarına hem ders hem de mentorluk verebilen üniversiteli rol modellerle buluşturan platform."
  />
</svelte:head>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 text-white">
  <div class="container mx-auto px-4 py-20 md:py-28">
    <div class="max-w-4xl mx-auto text-center">
      <h1 class="text-hero mb-6 leading-tight">
        Çocuğunuzun dilinden anlayan üniversiteli mentorlar
      </h1>
      <p class="text-xl md:text-2xl mb-4 text-primary-100 font-medium">
        ODTÜ, Boğaziçi, İTÜ’lü rol modellerle tanışın
      </p>
      <p class="text-lg mb-10 text-primary-200 max-w-2xl mx-auto">
        Çocuğunuz Almanya’daki okul sisteminde yalnız kalmasın; hem derslerinde destek alsın
        hem de “Ben de yapabilirim” diyebileceği bir yol arkadaşı olsun.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <Button variant="secondary" size="lg" href="/ara">
          🔍 Öğretmen Ara
        </Button>
        <a
          href="/kayit?role=student"
          class="px-8 py-4 text-lg rounded-xl font-semibold border-2 border-white text-white hover:bg-white hover:text-primary-600 transition-all duration-200"
        >
          ✨ Öğretmen Ol
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Features Grid -->
<section class="py-20 bg-gray-50">
  <div class="container mx-auto px-4">
    <h2 class="text-page text-center mb-4 text-gray-900">Neden DijitalMentor?</h2>
    <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
      Almanya’daki Türk aileleri için hem akademik destek hem de rol model mentorluk sağlayan güvenilir platform.
    </p>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
      <div class="bg-white p-8 rounded-card shadow-card hover:shadow-card-hover transition-shadow text-center border border-gray-100">
        <div class="text-5xl mb-4">✅</div>
        <h3 class="text-card mb-3 text-gray-900">Onaylı Mentorlar</h3>
        <p class="text-gray-600">
          Üniversite belgesi kontrol edilmiş, geçmişi doğrulanmış ve yalnızca onaylanan mentorlar listelenir.
        </p>
      </div>
      
      <div class="bg-white p-8 rounded-card shadow-card hover:shadow-card-hover transition-shadow text-center border border-gray-100">
        <div class="text-5xl mb-4">💰</div>
        <h3 class="text-card mb-3 text-gray-900">Erişilebilir Ücretler</h3>
        <p class="text-gray-600">
          Saatte ortalama €15–25 aralığında, ailenizin bütçesine uygun esnek ücretlendirme.
        </p>
      </div>
      
      <div class="bg-white p-8 rounded-card shadow-card hover:shadow-card-hover transition-shadow text-center border border-gray-100">
        <div class="text-5xl mb-4">🇹🇷</div>
        <h3 class="text-card mb-3 text-gray-900">Kültürel Bağ ve Özgüven</h3>
        <p class="text-gray-600">
          Çocuğunuzun yaşadığı zorlukları yaşamış, aynı dili ve kültürü paylaşan rol modeller.
        </p>
      </div>
      
      <div class="bg-white p-8 rounded-card shadow-card hover:shadow-card-hover transition-shadow text-center border border-gray-100">
        <div class="text-5xl mb-4">📍</div>
        <h3 class="text-card mb-3 text-gray-900">Size En Yakın Mentor</h3>
        <p class="text-gray-600">
          PLZ koduna göre hem yüz yüze ders için yakınınızdaki hem de online ders için uygun mentorları görüntüleyin.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Success Stories Section -->
<section class="py-20 bg-white">
  <div class="container mx-auto px-4">
    <h2 class="text-page text-center mb-4 text-gray-900">Başarı Hikayeleri</h2>
    <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
      DijitalMentor ile başarıya ulaşan öğrencilerimizin ve velilerimizin deneyimleri.
    </p>
    
    <div class="grid md:grid-cols-3 gap-8">
      <!-- Story 1 -->
      <div class="bg-gray-50 p-8 rounded-card border border-gray-100">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-2xl mr-4">🎓</div>
          <div>
            <h4 class="font-bold text-gray-900">Ayşe Y.</h4>
            <p class="text-sm text-gray-500">Veli, Berlin</p>
          </div>
        </div>
        <p class="text-gray-600 italic">
          "Oğlum Gymnasium'a geçişte çok zorlanıyordu. Mentor abisi sayesinde hem notları düzeldi hem de kendine güveni geldi. Artık 'Ben de mühendis olabilirim' diyor."
        </p>
      </div>

      <!-- Story 2 -->
      <div class="bg-gray-50 p-8 rounded-card border border-gray-100">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-2xl mr-4">📚</div>
          <div>
            <h4 class="font-bold text-gray-900">Mehmet K.</h4>
            <p class="text-sm text-gray-500">Öğrenci, 10. Sınıf</p>
          </div>
        </div>
        <p class="text-gray-600 italic">
          "Matematik derslerinden korkuyordum ama mentorum bana çok pratik yollar gösterdi. Şimdi en sevdiğim ders matematik oldu."
        </p>
      </div>

      <!-- Story 3 -->
      <div class="bg-gray-50 p-8 rounded-card border border-gray-100">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-2xl mr-4">🌟</div>
          <div>
            <h4 class="font-bold text-gray-900">Zeynep T.</h4>
            <p class="text-sm text-gray-500">Veli, Köln</p>
          </div>
        </div>
        <p class="text-gray-600 italic">
          "Kızım için sadece bir öğretmen değil, aynı zamanda bir yol arkadaşı bulduk. Üniversite hayalleri kurmaya başladı."
        </p>
      </div>
    </div>
  </div>
</section>

<!-- How It Works -->
<section class="py-20 bg-white">
  <div class="container mx-auto px-4">
    <h2 class="text-page text-center mb-4 text-gray-900">Nasıl Çalışır?</h2>
    <p class="text-center text-gray-600 mb-12">Üç basit adımda öğretmeninizi bulun</p>
    
    <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
      {#each [
        { num: 1, title: 'Öğretmen Ara', desc: 'Şehir, ders ve fiyata göre filtrele', icon: '🔍' },
        { num: 2, title: 'Profil İncele', desc: 'Üniversite, deneyim ve yorumları gör', icon: '📋' },
        { num: 3, title: 'İletişime Geç', desc: 'WhatsApp ile direkt mesaj gönder', icon: '💬' }
      ] as step}
        <div class="text-center">
          <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold text-primary-600 shadow-sm">
            {step.num}
          </div>
          <div class="text-4xl mb-3">{step.icon}</div>
          <h3 class="text-small-heading mb-2 text-gray-900">{step.title}</h3>
          <p class="text-gray-600">{step.desc}</p>
        </div>
      {/each}
    </div>
    
    <div class="text-center mt-12">
      <Button variant="primary" size="lg" href="/nasil-calisir">
        Detaylı Bilgi
      </Button>
    </div>
  </div>
</section>
