<script>
  import Button from '$lib/components/Button.svelte';
</script>

<svelte:head>
  <title>Almanya'da Türk Öğrencilerin Başarı Hikayeleri - DijitalMentor Blog</title>
  <meta
    name="description"
    content="Almanya'da eğitim gören Türk öğrencilerin ilham veren başarı hikayeleri. Zorlukları nasıl aştılar, Gymnasium ve üniversite yolunda neler yaptılar?"
  />
</svelte:head>

<article class="max-w-3xl mx-auto px-4 py-16">
  <div class="mb-8">
    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">Başarı Hikayeleri</span>
    <span class="text-gray-500 text-sm ml-4">23 Kasım 2025</span>
  </div>

  <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
    "Yapamazsın" Dediler, O Şimdi Mühendis: Almanya'da Bir Başarı Öyküsü
  </h1>

  <div class="prose prose-lg prose-blue text-gray-600">
    <p class="lead text-xl text-gray-800 font-medium mb-8">
      Almanya'da yaşayan pek çok gurbetçi ailenin ortak endişesi: "Çocuğum Gymnasium'a gidebilecek mi?" 
      Bugün, Realschule tavsiyesine rağmen azmedip mühendis olan Ahmet'in hikayesini paylaşıyoruz.
    </p>

    <img 
      src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80" 
      alt="Üniversite öğrencileri kütüphanede çalışıyor" 
      class="w-full h-64 md:h-96 object-cover rounded-2xl shadow-lg mb-8"
    />

    <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Dil Bariyeri ve İlk Yıllar</h2>
    <p>
      Ahmet, ilkokula başladığında Almancası yaşıtlarına göre biraz gerideydi. Öğretmenleri zeki olduğunu görüyor 
      ancak dil sorunu nedeniyle Gymnasium (Lise) yerine Realschule'ye gitmesini tavsiye ediyordu. Ailesi endişeliydi 
      ama pes etmedi.
    </p>

    <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Bir Mentorun Dokunuşu</h2>
    <p>
      Ahmet'in hayatı, üniversitede okuyan Türk bir "abi" ile tanışmasıyla değişti. Bu mentor, Ahmet'e sadece matematik 
      ve Almanca çalıştırmakla kalmadı; ona kendi hikayesini anlattı. "Ben de senin gibiydim, ama bak şimdi üniversitedeyim" 
      demesi, Ahmet'in kendine olan inancını tazeledi.
    </p>

    <blockquote class="border-l-4 border-blue-500 pl-4 italic my-8 bg-gray-50 p-4 rounded-r-lg">
      "Bana ders anlatan bir öğretmenden fazlasıydı. Benimle aynı yollardan geçmiş bir yol arkadaşıydı. 
      Onu görünce 'Ben de yapabilirim' dedim." – Ahmet
    </blockquote>

    <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Sonuç: Teknik Üniversite</h2>
    <p>
      Düzenli çalışma ve doğru yönlendirme ile Ahmet notlarını yükseltti. Önce Realschule'yi dereceyle bitirdi, 
      ardından Gymnasium'a geçiş yaptı ve Abitur'u başarıyla tamamladı. Şu an Münih Teknik Üniversitesi'nde 
      Makine Mühendisliği okuyor.
    </p>

    <h3 class="text-xl font-bold text-gray-900 mt-8 mb-4">Velilere Tavsiyeler</h3>
    <ul class="list-disc pl-6 space-y-2 mb-8">
      <li><strong>Erken Destek:</strong> Sorunlar büyümeden destek alın.</li>
      <li><strong>Rol Model Önemi:</strong> Çocuğunuzu başarılı üniversiteli gençlerle tanıştırın.</li>
      <li><strong>İletişim:</strong> Okul öğretmenleriyle sürekli iletişimde kalın.</li>
    </ul>
  </div>

  <!-- CTA -->
  <div class="mt-12 bg-primary-50 p-8 rounded-2xl border border-primary-100 text-center">
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Sizin Çocuğunuzun Hikayesi de Böyle Olsun</h3>
    <p class="text-gray-600 mb-6">
      DijitalMentor'daki yüzlerce üniversiteli genç, çocuğunuza ilham olmak için bekliyor.
    </p>
    <Button variant="primary" size="lg" href="/ara">
      Hemen Bir Mentor Bulun
    </Button>
  </div>
</article>
