<script>
  import { authStore } from '$lib/stores/auth.js';

  $: user = $authStore.user;
  $: role = user?.role;
  $: showFindTeacher = role !== 'student';
  $: showLessonRequests = role !== 'parent';
</script>

<footer class="bg-gray-900 text-white mt-auto">
  <div class="container mx-auto px-4 py-8">
    <div class="grid md:grid-cols-4 gap-8">
      <!-- Logo & Tagline -->
      <div>
        <div class="flex items-center gap-2 mb-3">
          <span class="font-bold text-xl">DijitalMentor</span>
        </div>
        <p class="text-gray-400 text-sm">
          Gurbet çocuklarına kendi dilinden öğretmen
        </p>
      </div>
      
      <!-- Platform -->
      <div>
        <h4 class="font-semibold mb-3">Platform</h4>
        <ul class="space-y-2 text-sm text-gray-400">
          {#if showFindTeacher}
            <li><a href="/ara" class="hover:text-white">Öğretmen Ara</a></li>
          {/if}
          {#if showLessonRequests}
            <li><a href="/ders-talepleri" class="hover:text-white">Ders Talepleri</a></li>
          {/if}
          <li><a href="/kayit?role=student" class="hover:text-white">Öğretmen Ol</a></li>
          <li><a href="/hakkimizda" class="hover:text-white">Hakkımızda</a></li>
        </ul>
      </div>
      
      <!-- Destek -->
      <div>
        <h4 class="font-semibold mb-3">Destek</h4>
        <ul class="space-y-2 text-sm text-gray-400">
          <li><a href="/sss" class="hover:text-white">Sık Sorulan Sorular</a></li>
          <li><a href="/iletisim" class="hover:text-white">İletişim</a></li>
          <li><a href="/blog" class="hover:text-white">Blog</a></li>
        </ul>
      </div>
      
      <!-- Yasal -->
      <div>
        <h4 class="font-semibold mb-3">Yasal</h4>
        <ul class="space-y-2 text-sm text-gray-400">
          <li><a href="/gizlilik-politikasi" class="hover:text-white">Gizlilik Politikası</a></li>
          <li><a href="/kullanim-kosullari" class="hover:text-white">Kullanım Koşulları</a></li>
          <li><a href="/impressum" class="hover:text-white">Impressum</a></li>
        </ul>
      </div>
    </div>
    
    <!-- Bottom -->
    <div class="border-t border-gray-800 mt-8 pt-6 text-sm text-gray-500">
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <!-- Copyright & Location -->
        <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4">
          <p>© 2025 DijitalMentor. Tüm hakları saklıdır.</p>
          <span class="hidden md:inline text-gray-700">•</span>
          <p class="flex items-center gap-1">
            <span>🏗️</span>
            <span>Built in Kornwestheim</span>
          </p>
        </div>
        
        <!-- Developer Credit -->
        <div class="flex items-center gap-2">
          <span class="text-gray-600">Developed by</span>
          <a 
            href="https://kayacuneyt.com" 
            target="_blank" 
            rel="noopener noreferrer"
            class="font-medium text-blue-400 hover:text-blue-300 transition"
          >
            Cüneyt Kaya
          </a>
        </div>
      </div>
      
      <!-- Social Media -->
      <div class="flex justify-center gap-4 mt-6 pt-4 border-t border-gray-800">
        <a href="https://instagram.com/dijitalmentor" target="_blank" rel="noreferrer" class="hover:text-white transition">Instagram</a>
        <a href="https://facebook.com/dijitalmentor" target="_blank" rel="noreferrer" class="hover:text-white transition">Facebook</a>
        <a href="https://wa.me/49123456789" target="_blank" rel="noreferrer" class="hover:text-white transition">WhatsApp</a>
      </div>
    </div>
  </div>
</footer>
