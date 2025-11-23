<script>
  import Button from '$lib/components/Button.svelte';
</script>

<svelte:head>
  <title>DijitalMentor - Gurbet Çocuklarına Kendi Dilinden Mentorlar</title>
  <meta
    name="description"
    content="DijitalMentor, Almanya’daki Türk diasporasına hem akademik destek hem de rol model mentorluk sunan güvenilir topluluk platformudur."
  />
  <!-- JSON-LD Structured Data -->
  {@html `<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "DijitalMentor",
      "url": "https://dijitalmentor.de",
      "logo": "https://dijitalmentor.de/logo.png",
      "sameAs": [
        "https://www.facebook.com/dijitalmentor",
        "https://www.instagram.com/dijitalmentor"
      ],
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+49-123-456-7890",
        "contactType": "customer service"
      }
    }
  </script>`}
</svelte:head>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 text-white overflow-hidden">
  <div class="container mx-auto px-4 py-12 md:py-20">
    <div class="flex flex-col md:flex-row items-center gap-12">
      <div class="w-full md:w-1/2 text-center md:text-left">
        <h1 class="text-hero mb-4 leading-tight">
          Gurbet çocuklarına kendi dilinden mentorlar
        </h1>
        <p class="text-xl md:text-2xl mb-4 text-primary-100 font-medium">
          Türkiye ve Avrupa’nın seçkin üniversitelerinden rol modellerle eğitimde güvenli bir köprü kuruyoruz.
        </p>
        <p class="text-lg mb-6 text-primary-200 max-w-lg mx-auto md:mx-0">
          DijitalMentor’da veliler ders desteği değil, aidiyet hissi sunan bir topluluk deneyimi kazanır.
          Öğrenciler hem akademik hem kişisel gelişim için ilham verici mentorlarla büyür.
        </p>
        <ul class="text-left text-base md:text-lg text-primary-100 space-y-2 mb-8 max-w-md">
          <li>✅ Onaylı, kimliği ve üniversite belgeleri doğrulanmış mentorlar</li>
          <li>🤝 Rol model desteği ve kültürel bağları güçlendiren programlar</li>
          <li>💸 Komisyonsuz ödeme; ders planı ve ücretleri doğrudan mentorla belirleyin</li>
          <li>📍 Yerel ve online mentorlar; PLZ filtreleriyle en yakınını bulun</li>
        </ul>
        <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
          <Button variant="secondary" size="lg" href="/ara">
            🔍 Öğretmen Bul
          </Button>
          <a
            href="/kayit?role=student"
            class="px-8 py-4 text-lg rounded-xl font-semibold border-2 border-white text-white hover:bg-white hover:text-primary-600 transition-all duration-200"
          >
            ✨ Öğretmen Ol
          </a>
        </div>
      </div>
      <div class="w-full md:w-1/2">
        <img 
          src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80" 
          alt="Üniversite öğrencileri kütüphanede" 
          class="rounded-3xl shadow-2xl transform rotate-2 hover:rotate-0 transition duration-500 border-4 border-white/20"
        />
      </div>
    </div>
  </div>
</section>

<!-- Features Grid -->
<section class="py-20 bg-gray-50">
  <div class="container mx-auto px-4">
    <h2 class="text-page text-center mb-4 text-gray-900">Neden DijitalMentor?</h2>
    <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
      Avrupa Türk Diasporası için hem akademik destek hem de rol model mentorluk sağlayan güvenilir platform.
    </p>
    
      <div class="grid md:grid-cols-2 gap-8">
        {#each [
          {
            title: 'Onaylı Mentorlar',
            icon: '🛡️',
            text: 'Kimlik ve üniversite belgeleri doğrulanmış mentorlarımız, güvenli bir eğitim deneyimi sunar.'
          },
          {
            title: 'Rol Model Desteği',
            icon: '🤝',
            text: 'Türkçe konuşan mentorlar hem ders hem de özgüven geliştiren rol model olarak öğrencilerin yanında.'
          },
          {
            title: 'Projeye Odaklı Topluluk',
            icon: '🌐',
            text: 'Dernekler, kurumlar ve ailelerle birlikte güvenli buluşmalar, topluluk etkinlikleri ve eğitim pusulası.'
          },
          {
            title: 'Şeffaf Ücret ve Planlama',
            icon: '💶',
            text: 'Ders, ücret ve planlamayı mentorunuzla konuşun; platform hiçbir komisyon almaz.'
          }
        ] as feature}
          <div class="bg-white p-8 rounded-card shadow-card hover:shadow-card-hover transition text-center border border-gray-100">
            <div class="text-4xl mb-3">{feature.icon}</div>
            <h3 class="text-card mb-3 text-gray-900">{feature.title}</h3>
            <p class="text-gray-600">{feature.text}</p>
          </div>
        {/each}
      </div>
    </div>
  </section>

  <!-- Innovation cards -->
  <section class="py-20 bg-gradient-to-br from-indigo-50 via-white to-white">
    <div class="container mx-auto px-4">
      <h2 class="text-page text-center mb-4 text-gray-900">Yeniliklerimiz</h2>
      <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
        Dijital Ders Karnesi, Eğitim Pusulası ve Topluluk Soru-Cevap gibi araçlar sayesinde çocuğunuzun gelişimini yakından takip edersiniz.
      </p>
      <div class="grid lg:grid-cols-3 gap-8">
        <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
          <h3 class="text-xl font-semibold text-gray-900 mb-3">Dijital Ders Karnesi</h3>
          <p class="text-gray-600">
            Her ders sonrası mentor tarafından doldurulur; katılım, öğrenilen konu ve mentor notlarıyla gelişimi takip eder.
          </p>
        </div>
        <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
          <h3 class="text-xl font-semibold text-gray-900 mb-3">Eğitim Pusulası</h3>
          <p class="text-gray-600">
            Premium+ üyeler için yapay zekâ destekli rehber; okul türleri, geçiş kuralları ve dil sertifikalarına göre kişisel yol haritası sunar.
          </p>
        </div>
        <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
          <h3 class="text-xl font-semibold text-gray-900 mb-3">Topluluk Soru-Cevap</h3>
          <p class="text-gray-600">
            Moderatorlü forumda sadece onaylı mentorlar yanıtlar; veliler ve öğrenciler için güvenilir bilgi kaynağıdır.
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
    <p class="text-center text-gray-600 mb-12 max-w-3xl mx-auto">
      Veliler, mentor adayları ve topluluklarla birlikte çocuğunuzun eğitim yolculuğunu
      şeffaf bir şekilde yürütüyoruz.
    </p>
    
    <div class="grid lg:grid-cols-3 gap-8">
      <div class="bg-gray-50 rounded-3xl p-8 shadow-lg border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Veliler İçin</h3>
        <ol class="list-decimal list-inside space-y-3 text-gray-700">
          <li>Mentorların fotoğrafını, üniversite bilgisini, deneyimini ve yorumlarını inceleyin.</li>
          <li>Tarih, ders ve ihtiyaçları içeren kısa bir ders talebi oluşturun.</li>
          <li>WhatsApp veya telefonla tanışma görüşmesi yapıp hedefleri birlikte belirleyin.</li>
          <li>Dijital Ders Karnesi ve Eğitim Pusulası ile ilerlemeyi takip edin.</li>
        </ol>
      </div>
      <div class="bg-gray-50 rounded-3xl p-8 shadow-lg border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Mentorlar</h3>
        <ol class="list-decimal list-inside space-y-3 text-gray-700">
          <li>“Mentor Ol” sayfasından kayıt olup kimlik ve üniversite belgelerini yükleyin.</li>
          <li>Profilinizi ders alanları, deneyim ve ücret bilgileriyle güncelleyin.</li>
          <li>Ders taleplerini inceleyip uygun velilerle iletişim kurun.</li>
          <li>Gönüllü rozetler ve Mentor Teşvik Rehberi’ndeki ödüllerle topluluğa katkı sunun.</li>
          <li>Ücretinizi veliyle doğrudan kararlaştırın; platform müdahil olmaz.</li>
        </ol>
      </div>
      <div class="bg-gray-50 rounded-3xl p-8 shadow-lg border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Dernekler & Kurumlar</h3>
        <p class="text-gray-700 space-y-3">
          Eğitim faaliyetleri, güvenli mentor-öğrenci buluşmaları ve yerel rehberlik için kurumlarla iş birliğine açığız.
          Topluluklar için özel mentor buluşmaları organize eder, Eğitim Pusulası’nı üyelerinize sunarız.
          İletişim sayfamızdan teklifinizi iletin, birlikte göçmen çocuklarının yanında olalım.
        </p>
      </div>
    </div>
    
    <div class="text-center mt-12">
      <Button variant="primary" size="lg" href="/nasil-calisir">
        Süreci Detaylı İncele
      </Button>
    </div>
  </div>
</section>
