<script>
  import { authStore } from '$lib/stores/auth.js';
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  
  $: user = $authStore.user;
  
  onMount(() => {
    if (!$authStore.isAuthenticated) {
      goto('/giris');
    }
  });
</script>

<svelte:head>
  <title>Panelim - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold mb-8">Hoşgeldin, {user?.full_name}</h1>
  
  <div class="grid md:grid-cols-3 gap-6">
    <!-- Profil Kartı -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
      <h2 class="text-xl font-semibold mb-4">Profil Bilgileri</h2>
      <div class="space-y-2 text-gray-600">
        <p><strong>Telefon:</strong> {user?.phone}</p>
        <p><strong>Rol:</strong> {user?.role === 'student' ? 'Öğretmen' : 'Veli'}</p>
      </div>
      <div class="flex flex-col gap-2 mt-4">
        <a href="/panel/ayarlar" class="text-left text-blue-600 hover:underline">Profil Düzenle</a>
        {#if user?.role === 'parent'}
          <a href="/panel/taleplerim" class="text-left text-blue-600 hover:underline">Ders Taleplerim</a>
        {/if}
      </div>
    </div>
    
    {#if user?.role === 'student'}
      <!-- Öğretmen İstatistikleri -->
      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-semibold mb-4">İstatistikler</h2>
        <div class="grid grid-cols-2 gap-4 text-center">
          <div class="bg-blue-50 p-3 rounded">
            <div class="text-2xl font-bold text-blue-600">0</div>
            <div class="text-sm text-gray-600">Görüntülenme</div>
          </div>
          <div class="bg-green-50 p-3 rounded">
            <div class="text-2xl font-bold text-green-600">0</div>
            <div class="text-sm text-gray-600">Mesaj</div>
          </div>
        </div>
      </div>
    {/if}
  </div>
</div>
