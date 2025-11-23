<script>
  let loading = false;
  let success = false;

  async function handleSubmit(e) {
    loading = true;
    // Simulate API call
    await new Promise(r => setTimeout(r, 1000));
    loading = false;
    success = true;
    e.target.reset();
    
    setTimeout(() => {
      success = false;
    }, 5000);
  }
</script>

<svelte:head>
  <title>İletişim - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-12 max-w-6xl">
  <div class="text-center mb-16">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">İletişime Geçin</h1>
    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
      Sorularınız, önerileriniz veya destek talepleriniz için bize her zaman ulaşabilirsiniz.
    </p>
  </div>

  <div class="grid md:grid-cols-2 gap-16 items-start">
    
    <!-- Contact Info -->
    <div class="bg-blue-50 rounded-2xl p-8 md:p-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-8">İletişim Bilgileri</h2>
      
      <div class="space-y-8">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl flex-shrink-0 text-blue-600">
            📍
          </div>
          <div>
            <h3 class="font-bold text-gray-900 mb-1">Adres</h3>
            <p class="text-gray-600">
              Musterstraße 123<br>
              10115 Berlin<br>
              Deutschland
            </p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl flex-shrink-0 text-blue-600">
            ✉️
          </div>
          <div>
            <h3 class="font-bold text-gray-900 mb-1">E-Posta</h3>
            <p class="text-gray-600">
              <a href="mailto:info@dijitalmentor.de" class="hover:text-blue-600 transition">info@dijitalmentor.de</a>
            </p>
            <p class="text-sm text-gray-500 mt-1">Genellikle 24 saat içinde yanıt veriyoruz.</p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl flex-shrink-0 text-blue-600">
            📞
          </div>
          <div>
            <h3 class="font-bold text-gray-900 mb-1">Telefon</h3>
            <p class="text-gray-600">
              <a href="tel:+49123456789" class="hover:text-blue-600 transition">+49 (0) 123 456789</a>
            </p>
            <p class="text-sm text-gray-500 mt-1">Hafta içi 09:00 - 18:00</p>
          </div>
        </div>
      </div>

      <div class="mt-12 pt-8 border-t border-blue-200">
        <h3 class="font-bold text-gray-900 mb-4">Sosyal Medya</h3>
        <div class="flex gap-4">
          <a
            href="https://instagram.com/dijitalmentor"
            target="_blank"
            rel="noreferrer"
            aria-label="Instagram"
            class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-pink-600 hover:shadow-md transition group"
          >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.468.99c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
            </svg>
          </a>
          <a
            href="https://facebook.com/dijitalmentor"
            target="_blank"
            rel="noreferrer"
            aria-label="Facebook"
            class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-blue-600 hover:shadow-md transition group"
          >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
            </svg>
          </a>
          <a
            href="https://wa.me/49123456789"
            target="_blank"
            rel="noreferrer"
            aria-label="WhatsApp"
            class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-green-600 hover:shadow-md transition group"
          >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.468.99c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
            </svg>
          </a>
        </div>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Mesaj Gönderin</h2>
      
      {#if success}
        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-2">
          <span>✅</span>
          Mesajınız başarıyla gönderildi. En kısa sürede size döneceğiz.
        </div>
      {/if}

      <form on:submit|preventDefault={handleSubmit} class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2" for="contact-name">Adınız Soyadınız</label>
            <input id="contact-name" type="text" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="Adınız Soyadınız" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2" for="contact-email">E-Posta Adresiniz</label>
            <input id="contact-email" type="email" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="ornek@email.com" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2" for="contact-subject">Konu</label>
          <select id="contact-subject" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            <option>Genel Bilgi</option>
            <option>Öğretmenlik Başvurusu</option>
            <option>Teknik Destek</option>
            <option>Öneri / Şikayet</option>
            <option>Diğer</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2" for="contact-message">Mesajınız</label>
          <textarea id="contact-message" required rows="5" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="Mesajınızı buraya yazın..."></textarea>
        </div>

        <button 
          type="submit" 
          disabled={loading}
          class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition shadow-lg hover:shadow-xl disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
          {#if loading}
            <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            Gönderiliyor...
          {:else}
            Mesajı Gönder
          {/if}
        </button>
      </form>
    </div>

  </div>
</div>
