<script>
  import Button from '$lib/components/Button.svelte';
</script>

<svelte:head>
  <title>Nasıl Çalışır? - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-12 space-y-12">
  <div class="text-center">
    <p class="text-sm uppercase tracking-[0.4em] text-primary-600 mb-2">Nasıl Çalışır</p>
    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
      Eğitim yolculuğunuza net adımlar
    </h1>
    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
      Veliler, mentorlar ve partner dernekler için süreci sadeleştirdik. Herkes
      DijitalMentor’da ne yapması gerektiğini adım adım bilir.
    </p>
  </div>

  <section class="grid lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
      <h2 class="text-2xl font-semibold text-gray-900 mb-4">Veliler & Öğrenciler</h2>
      <ol class="list-decimal list-inside space-y-4 text-gray-700 leading-relaxed">
        <li>Mentorları fotoğraf, üniversite, bölüm, deneyim ve yorumlarıyla inceleyin.</li>
        <li>Dil, ders ve PLZ filtreleriyle uygun adayları filtreleyin.</li>
        <li>Kısa bir talep yazarak ders, sınıf seviyesi ve saat beklentinizi belirtin.</li>
        <li>WhatsApp/telefonla tanışıp hedefleri belirleyip derslere başlayın.</li>
        <li>Dijital Ders Karnesi ve Eğitim Pusulası ile ilerlemeyi takip edin.</li>
      </ol>
    </div>
    <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
      <h2 class="text-2xl font-semibold text-gray-900 mb-4">Mentor Adayları</h2>
      <ol class="list-decimal list-inside space-y-4 text-gray-700 leading-relaxed">
        <li>"Mentor Ol" sayfasından kayıt olun ve kimlik, üniversite belgenizi yükleyin.</li>
        <li>Profilinizi ders alanları, deneyim, saatlik ücret ve CV ile tamamlayın.</li>
        <li>Talepleri inceleyip uygun olan velilere ulaşın ve tanışma dersini planlayın.</li>
        <li>Topluluk etkinliklerinde gönüllü rozetler, Mentor Teşvik Rehberi ödülleri kazanın.</li>
        <li>Ücretinizi veliyle doğrudan kararlaştırın; platform parasal sürece müdahil olmaz.</li>
      </ol>
    </div>
  </section>

  <section class="bg-gradient-to-br from-indigo-50 to-white rounded-3xl p-8 shadow-lg border border-indigo-200">
    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Dernekler & Kurumlar</h2>
    <p class="text-gray-700 leading-relaxed space-y-4">
      Dernekler, eğitim etkinliklerinde aracı rolü üstleniyor ve topluluklara güvenli mentor-öğrenci buluşmaları sunuyor.
      Eğitim Pusulası, yerel rehberlik ve topluluk soru-cevap köşesi gibi araçları birlikte çalıştırarak diasporadaki eğitim ağını güçlendiriyoruz.
      İletişim formundan öneri ya da iş birliği talebinizi gönderin.
    </p>
  </section>

  <div class="text-center space-y-4">
    <p class="text-lg text-gray-600">
      Hemen mentor aramaya başlamak için <a href="/ara" class="text-blue-600 font-semibold underline">Öğretmen Ara</a> sayfasına göz atın,
      mentor olmak istiyorsanız <a href="/kayit?role=student" class="text-blue-600 font-semibold underline">Mentor Ol</a> sayfasını kullanın.
    </p>
    <Button variant="primary" size="lg" href="/iletisim">
      Destek ya da iş birliği talebi gönderin
    </Button>
  </div>
</div>
