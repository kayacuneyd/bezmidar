<script>
  import { onMount } from 'svelte';
</script>

<svelte:head>
  <title>Nasıl Çalışır? - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-12 max-w-4xl">
  <!-- Hero Section -->
  <div class="text-center mb-16">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
      Çocuğunuz İçin Doğru Mentoru Bulmak<br/>
      <span class="text-blue-600">Artık Çok Kolay</span>
    </h1>
    <p class="text-xl text-gray-600 leading-relaxed max-w-2xl mx-auto">
      Teknolojiyle aranız iyi olmasa bile endişelenmeyin. 
      Sizin için her şeyi basitleştirdik. 
      Sadece 3 adımda çocuğunuza hem ders hem mentorluk desteği verecek kişiyi bulabilirsiniz.
    </p>
  </div>

  <!-- Steps -->
  <!-- Steps -->
  <div class="flex flex-col gap-16">
    
    <!-- Step 1 -->
    <div class="bg-white rounded-2xl p-8 shadow-xl border-2 border-blue-100 flex flex-col md:flex-row items-center gap-8 transform hover:scale-105 transition duration-300">
      <div class="w-32 h-32 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 shadow-inner">
        <span class="text-6xl">🔍</span>
      </div>
      <div class="text-center md:text-left">
        <div class="inline-block bg-blue-600 text-white text-lg font-bold px-6 py-2 rounded-full mb-4 shadow-md">
          1. ADIM
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Mentorları Keşfedin</h2>
        <p class="text-lg text-gray-600 leading-relaxed">
          Şehrinizdeki ve çevrimiçi mentorların fotoğraflarını, üniversitelerini, bölümlerini
          ve deneyimlerini inceleyin. Hangi dersi verdiğini, saatlik ücretini ve önceki velilerin
          yorumlarını tek ekranda görebilirsiniz.
        </p>
      </div>
    </div>

    <!-- Step 2 -->
    <div class="bg-white rounded-2xl p-8 shadow-xl border-2 border-green-100 flex flex-col md:flex-row items-center gap-8 transform hover:scale-105 transition duration-300">
      <div class="w-32 h-32 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 shadow-inner">
        <span class="text-6xl">📝</span>
      </div>
      <div class="text-center md:text-left">
        <div class="inline-block bg-green-600 text-white text-lg font-bold px-6 py-2 rounded-full mb-4 shadow-md">
          2. ADIM
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Kısa Bir Talep Yazın</h2>
        <p class="text-lg text-gray-600 leading-relaxed">
          “Kızım Almanca yazmada zorlanıyor” veya “Oğlum matematikte geride kaldı” gibi birkaç
          cümleyle durumunuzu anlatın. Hangi dersten, kaçıncı sınıf için ve haftada yaklaşık kaç
          saat destek istediğinizi yazmanız yeterli.
        </p>
      </div>
    </div>

    <!-- Step 3 -->
    <div class="bg-white rounded-2xl p-8 shadow-xl border-2 border-red-100 flex flex-col md:flex-row items-center gap-8 transform hover:scale-105 transition duration-300">
      <div class="w-32 h-32 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 shadow-inner">
        <span class="text-6xl">📞</span>
      </div>
      <div class="text-center md:text-left">
        <div class="inline-block bg-red-600 text-white text-lg font-bold px-6 py-2 rounded-full mb-4 shadow-md">
          3. ADIM
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Tanışma Görüşmesi Yapın</h2>
        <p class="text-lg text-gray-600 leading-relaxed">
          Size uygun gelen mentorla WhatsApp veya telefon üzerinden kısaca konuşun.
          Hem ders planını hem de çocuğunuzun hedeflerini beraber belirleyin. İçiniz
          ısındığında derslere başlarsınız; tempo ve sıklık tamamen sizin kontrolünüzdedir.
        </p>
      </div>
    </div>

  </div>

  <!-- Video Placeholder -->
  <div class="mt-16 relative rounded-2xl overflow-hidden shadow-2xl group cursor-pointer">
    <img 
      src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80" 
      alt="Nasıl çalışır video önizleme" 
      class="w-full h-64 md:h-96 object-cover transform group-hover:scale-105 transition duration-500"
    />
    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center group-hover:bg-opacity-30 transition duration-300">
      <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg transform group-hover:scale-110 transition duration-300">
        <span class="text-4xl text-primary-600 ml-2">▶️</span>
      </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/80 to-transparent text-white">
      <h3 class="text-xl font-bold mb-1">Nasıl Yapıldığını İzleyin</h3>
      <p class="text-gray-200 text-sm">
        Sistemi anlatan kısa videomuzu izleyin
      </p>
    </div>
  </div>

  <!-- CTA -->
  <div class="mt-16 text-center">
    <h2 class="text-2xl font-bold mb-6">Hemen Başlamak İster misiniz?</h2>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
      <a href="/ara" class="bg-blue-600 text-white text-xl font-bold px-8 py-4 rounded-xl hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
        Öğretmenleri Gör
      </a>
      <a href="/kayit" class="bg-white text-blue-600 border-2 border-blue-600 text-xl font-bold px-8 py-4 rounded-xl hover:bg-blue-50 transition">
        Ücretsiz Kayıt Ol
      </a>
    </div>
  </div>
</div>
