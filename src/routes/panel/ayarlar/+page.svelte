<script>
  import { authStore } from '$lib/stores/auth.js';
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { get } from 'svelte/store';

  let user = null;
  let loading = false;

  onMount(() => {
    const auth = get(authStore);
    if (!auth.isAuthenticated) {
      goto('/giris?redirect=/panel/ayarlar');
      return;
    }
    user = auth.user;
  });

  async function handleSave() {
    loading = true;
    // TODO: Implement API call to update profile
    await new Promise(r => setTimeout(r, 1000)); // Mock delay
    alert('Profil güncellendi (Demo)');
    loading = false;
  }
</script>

<svelte:head>
  <title>Ayarlar - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-8 max-w-2xl">
  <h1 class="text-2xl font-bold mb-8">Profil Ayarları</h1>

  {#if user}
    <form on:submit|preventDefault={handleSave} class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-6">
      <div>
        <label class="block text-sm font-medium mb-2">Ad Soyad</label>
        <input 
          type="text" 
          bind:value={user.full_name} 
          class="w-full border rounded-lg px-4 py-2 bg-gray-50" 
          disabled
        />
        <p class="text-xs text-gray-500 mt-1">Ad soyad değiştirmek için destek ile iletişime geçin.</p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Telefon</label>
        <input 
          type="text" 
          bind:value={user.phone} 
          class="w-full border rounded-lg px-4 py-2 bg-gray-50" 
          disabled
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Rol</label>
        <div class="capitalize px-4 py-2 bg-gray-50 border rounded-lg inline-block">
          {user.role === 'student' ? 'Öğretmen' : 'Veli / Öğrenci'}
        </div>
      </div>

      <div class="pt-4 border-t">
        <button 
          type="submit" 
          class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition disabled:opacity-50"
          disabled={loading}
        >
          {loading ? 'Kaydediliyor...' : 'Kaydet'}
        </button>
      </div>
    </form>
  {:else}
    <div class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
    </div>
  {/if}
</div>
