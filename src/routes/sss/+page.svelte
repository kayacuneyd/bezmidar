<script>
  import { slide } from 'svelte/transition';

  const faqs = [
    {
      category: "Genel",
      items: [
        {
          q: "DijitalMentor nedir?",
          a: "DijitalMentor, Almanya başta olmak üzere Avrupa'da yaşayan Türk aileleri; çocuklarına hem ders hem abi/abla mentorluk verebilen üniversiteli gençlerle buluşturan bir platformdur. Çocuğunuz için kendi dilinden anlayan, benzer yollardan geçmiş rol modellerle tanışabilirsiniz."
        },
        {
          q: "Platformu kullanmak ücretli mi?",
          a: "Hayır, veliler için öğretmen aramak ve iletişime geçmek tamamen ücretsizdir. Sadece alacağınız dersler için öğretmeninize ödeme yaparsınız."
        },
        {
          q: "Mentorlarınız güvenilir mi?",
          a: "Her mentorun üniversite öğrencisi veya mezunu olduğuna dair belgeleri istiyoruz ve profilleri tek tek kontrol ediyoruz. Onaylı olmayan hesaplar listelenmez. Şüpheli bir durum fark ederseniz hemen inceleme başlatıyoruz."
        }
      ]
    },
    {
      category: "Veliler İçin",
      items: [
        {
          q: "Nasıl öğretmen bulabilirim?",
          a: "'Öğretmen Ara' sayfasına giderek şehrinizi ve aradığınız dersi seçmeniz yeterlidir. Size en yakın öğretmenleri listeleyip profillerini inceleyebilirsiniz."
        },
        {
          q: "Çocuğum Almanca okul sistemine alışmakta zorlanıyor, yardımcı olur musunuz?",
          a: "Evet. Mentorlarımız sadece ders anlatmakla kalmaz, Almanya'da okumuş abi/abla rolünde çocuklarınıza kendi deneyimlerini aktarırlar. Bu sayede hem notları hem de özgüvenleri güçlenir."
        },
        {
          q: "Ödemeyi kime yapacağım?",
          a: "Ödemeyi doğrudan anlaştığınız öğretmene yaparsınız. DijitalMentor ödeme sürecine karışmaz ve komisyon almaz. Ödeme yöntemini (nakit, havale vb.) öğretmeninizle konuşarak belirlersiniz."
        },
        {
          q: "Dersler nerede yapılıyor?",
          a: "Derslerin nerede yapılacağına öğretmeninizle birlikte karar verirsiniz. Kendi evinizde, öğretmenin evinde veya online (görüntülü görüşme) olarak ders alabilirsiniz."
        },
        {
          q: "Memnun kalmazsam ne yapabilirim?",
          a: "Öğretmeninizle yaşadığınız herhangi bir sorunda dersleri istediğiniz zaman sonlandırabilirsiniz. Ciddi durumlarda 'İletişim' sayfasından bize bildirirseniz gerekli incelemeleri yaparız."
        }
      ]
    },
    {
      category: "Öğretmenler İçin",
      items: [
        {
          q: "Nasıl öğretmen olabilirim?",
          a: "Sağ üstteki 'Kayıt Ol' butonuna tıklayıp 'Öğretmen' seçeneğini işaretleyerek profilinizi oluşturabilirsiniz. Profiliniz onaylandıktan sonra veliler size ulaşabilir."
        },
        {
          q: "Ders ücretimi kendim belirleyebilir miyim?",
          a: "Evet, saatlik ders ücretinizi profilinizde kendiniz belirlersiniz. İstediğiniz zaman bu ücreti güncelleyebilirsiniz."
        }
      ]
    }
  ];

  let openIndex = null; // Track which item is open globally or per category if needed. 
  // Let's use a simple string ID approach "categoryIndex-itemIndex" to allow only one open at a time or multiple.
  // For simplicity, let's allow one open item at a time for better focus.
  
  let activeItem = null;

  function toggle(categoryIndex, itemIndex) {
    const id = `${categoryIndex}-${itemIndex}`;
    if (activeItem === id) {
      activeItem = null;
    } else {
      activeItem = id;
    }
  }
</script>

<svelte:head>
  <title>Sıkça Sorulan Sorular - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-12 max-w-3xl">
  <div class="text-center mb-12">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Sıkça Sorulan Sorular</h1>
    <p class="text-xl text-gray-600">
      Aklınıza takılan soruların cevaplarını burada bulabilirsiniz.
    </p>
  </div>

  <div class="space-y-20">
    {#each faqs as category, cIndex}
      <div>
        <h2 class="text-2xl font-bold text-gray-900 py-4 mb-8 border-b border-gray-200">
          {category.category}
        </h2>
        
        <div class="space-y-8">
          {#each category.items as item, iIndex}
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white hover:border-blue-300 transition duration-300">
              <button 
                class="w-full flex items-center justify-between p-6 text-left focus:outline-none"
                on:click={() => toggle(cIndex, iIndex)}
              >
                <span class="font-bold text-lg text-gray-800 pr-8">{item.q}</span>
                <span class="text-2xl text-blue-600 transition-transform duration-300 {activeItem === `${cIndex}-${iIndex}` ? 'rotate-180' : ''}">
                  ⌄
                </span>
              </button>
              
              {#if activeItem === `${cIndex}-${iIndex}`}
                <div transition:slide={{ duration: 300 }} class="bg-blue-50 px-6 pb-6 text-gray-700 leading-relaxed">
                  <div class="pt-2 border-t border-blue-100/50">
                    {item.a}
                  </div>
                </div>
              {/if}
            </div>
          {/each}
        </div>
      </div>
    {/each}
  </div>

  <div class="mt-16 text-center bg-gray-50 rounded-2xl p-8 border border-gray-200">
    <h3 class="text-xl font-bold text-gray-900 mb-2">Aradığınız cevabı bulamadınız mı?</h3>
    <p class="text-gray-600 mb-6">
      Bizimle iletişime geçmekten çekinmeyin. Size yardımcı olmaktan mutluluk duyarız.
    </p>
    <a href="/iletisim" class="inline-block bg-blue-600 text-white font-bold px-8 py-3 rounded-xl hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
      Bize Ulaşın
    </a>
  </div>
</div>
