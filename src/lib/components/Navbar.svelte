<script>
  import { authStore } from '$lib/stores/auth.js';
  import Button from '$lib/components/Button.svelte';
  
  let mobileMenuOpen = false;
  
  $: user = $authStore.user;
  $: isAuthenticated = $authStore.isAuthenticated;
</script>

<nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
  <div class="container mx-auto px-4">
    <div class="flex justify-between items-center h-16">
      <!-- Logo -->
      <a href="/" class="flex items-center gap-2">
        <span class="font-bold text-xl text-primary-600">DijitalMentor</span>
      </a>
      
      <!-- Desktop Menu -->
      <div class="hidden md:flex items-center gap-6">
        <a href="/ara" class="text-gray-700 hover:text-primary-600 font-medium transition">Öğretmen Bul</a>
        <a href="/ders-talepleri" class="text-gray-700 hover:text-primary-600 font-medium transition">Ders Talepleri</a>
        <a href="/nasil-calisir" class="text-gray-700 hover:text-primary-600 font-medium transition">Nasıl Çalışır?</a>
        
        {#if isAuthenticated}
          <a href="/panel" class="text-gray-700 hover:text-primary-600 transition">
            👤 {user?.full_name}
          </a>
          <button 
            on:click={() => authStore.logout()}
            class="text-error hover:text-error-600 font-medium transition"
          >
            Çıkış
          </button>
        {:else}
          <a href="/giris" class="text-gray-700 hover:text-primary-600 font-medium transition">Giriş</a>
          <Button variant="primary" size="sm" href="/kayit">
            Kayıt Ol
          </Button>
        {/if}
      </div>
      
      <!-- Mobile Menu Button -->
      <button 
        class="md:hidden"
        on:click={() => mobileMenuOpen = !mobileMenuOpen}
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>
  
  <!-- Mobile Menu -->
  {#if mobileMenuOpen}
    <div class="md:hidden border-t">
      <div class="container mx-auto px-4 py-4 flex flex-col gap-3">
        <a href="/ara" class="py-2">Öğretmen Ara</a>
        <a href="/hakkimizda" class="py-2">Hakkımızda</a>
        {#if isAuthenticated}
          <a href="/panel" class="py-2">Panelim</a>
          <button on:click={() => authStore.logout()} class="text-left py-2 text-red-600">
            Çıkış
          </button>
        {:else}
          <a href="/giris" class="py-2">Giriş</a>
          <a href="/kayit" class="py-2 text-blue-600">Kayıt Ol</a>
        {/if}
      </div>
    </div>
  {/if}
</nav>
