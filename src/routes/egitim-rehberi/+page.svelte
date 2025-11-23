<script>
  import Button from '$lib/components/Button.svelte';
</script>

<svelte:head>
  <title>Almanya Eğitim Sistemi Rehberi - DijitalMentor</title>
  <meta
    name="description"
    content="Almanya'daki okul sistemi (Gymnasium, Realschule, Hauptschule) hakkında Türk veliler için kapsamlı rehber. Çocuğunuz için en doğru okul türünü öğrenin."
  />
</svelte:head>

<div class="bg-gradient-to-b from-primary-50 to-white min-h-screen">
  <!-- Hero -->
  <section class="bg-primary-700 text-white py-16">
    <div class="container mx-auto px-4 text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-6">Almanya Eğitim Sistemi Rehberi</h1>
      <p class="text-xl text-primary-100 max-w-2xl mx-auto">
        Çocuğunuzun geleceği için okul türlerini ve geçiş süreçlerini anlamanıza yardımcı oluyoruz.
      </p>
    </div>
  </section>

  <div class="container mx-auto px-4 py-12 max-w-4xl">
    
    <!-- Introduction -->
    <div class="prose prose-lg mx-auto text-gray-600 mb-16">
      <p>
        Almanya'da eğitim sistemi eyaletlere göre küçük farklılıklar gösterse de temel yapı genellikle aynıdır. 
        İlkokuldan (Grundschule) sonra verilen yönlendirme (Empfehlung), çocuğunuzun akademik geleceğinde kritik bir rol oynar.
        İşte temel okul türleri ve bilmeniz gerekenler:
      </p>
    </div>

    <!-- School Types Grid -->
    <div class="grid md:grid-cols-2 gap-8 mb-16">
      
      <!-- Gymnasium -->
      <div class="bg-white p-8 rounded-2xl shadow-lg border-t-4 border-primary-600">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Gymnasium</h3>
        <p class="text-gray-600 mb-4">
          En yüksek akademik seviyedeki okul türüdür. Öğrencileri üniversiteye (Abitur) hazırlar.
        </p>
        <ul class="space-y-2 text-gray-600">
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> Hedef: Abitur ve Üniversite
          </li>
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> Yoğun teorik eğitim
          </li>
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> 12 veya 13 yıl sürer
          </li>
        </ul>
      </div>

      <!-- Realschule -->
      <div class="bg-white p-8 rounded-2xl shadow-lg border-t-4 border-blue-500">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Realschule</h3>
        <p class="text-gray-600 mb-4">
          Orta düzeyde eğitim verir. Mesleki eğitime (Ausbildung) veya teknik liselere (Fachoberschule) geçiş imkanı sunar.
        </p>
        <ul class="space-y-2 text-gray-600">
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> Hedef: Mittlere Reife
          </li>
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> Pratik ve teorik denge
          </li>
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> Başarılı öğrenciler Gymnasium'a geçebilir
          </li>
        </ul>
      </div>

      <!-- Hauptschule -->
      <div class="bg-white p-8 rounded-2xl shadow-lg border-t-4 border-yellow-500">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Hauptschule</h3>
        <p class="text-gray-600 mb-4">
          Temel eğitim verir ve öğrencileri doğrudan meslek hayatına veya çıraklık eğitimine hazırlar.
        </p>
        <ul class="space-y-2 text-gray-600">
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> Hedef: Hauptschulabschluss
          </li>
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> Pratik odaklı eğitim
          </li>
        </ul>
      </div>

      <!-- Gesamtschule -->
      <div class="bg-white p-8 rounded-2xl shadow-lg border-t-4 border-purple-500">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Gesamtschule</h3>
        <p class="text-gray-600 mb-4">
          Tüm okul türlerini tek çatı altında toplar. Öğrencinin başarısına göre farklı diplomalar (Abitur dahil) alınabilir.
        </p>
        <ul class="space-y-2 text-gray-600">
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> Esnek geçiş imkanları
          </li>
          <li class="flex items-start">
            <span class="text-green-500 mr-2">✓</span> Geç gelişen öğrenciler için ideal
          </li>
        </ul>
      </div>

    </div>

    <!-- CTA Section -->
    <div class="bg-primary-50 rounded-3xl p-8 md:p-12 text-center">
      <h2 class="text-3xl font-bold text-gray-900 mb-4">Kararsız mısınız?</h2>
      <p class="text-xl text-gray-600 mb-8">
        Çocuğunuzun durumunu değerlendirmek ve en doğru yolu çizmek için deneyimli mentorlarımızdan destek alın.
        Onlar da bu yollardan geçti.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <Button variant="primary" size="lg" href="/ara">
          Mentor Bul
        </Button>
        <Button variant="secondary" size="lg" href="/iletisim">
          Bize Danışın
        </Button>
      </div>
    </div>

  </div>
</div>
