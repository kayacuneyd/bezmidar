<script>
  import { api } from '$lib/utils/api.js';
  import { authStore } from '$lib/stores/auth.js';
  import { goto } from '$app/navigation';

  let phone = '';
  let password = '';
  let error = '';
  let loading = false;

  async function handleLogin() {
    loading = true;
    error = '';
    
    try {
      const response = await api.post('/auth/login.php', { phone, password });
      authStore.login(response.data.user, response.data.token);
      goto('/panel');
    } catch (e) {
      error = e.message || 'Giriş başarısız';
    } finally {
      loading = false;
    }
  }
</script>

<svelte:head>
  <title>Giriş Yap - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-12 max-w-md">
  <h1 class="text-3xl font-bold mb-8 text-center">Giriş Yap</h1>
  
  {#if error}
    <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
      {error}
    </div>
  {/if}
  
  <form on:submit|preventDefault={handleLogin} class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-1" for="login-phone">Telefon Numarası</label>
      <input 
        id="login-phone"
        type="tel" 
        bind:value={phone}
        placeholder="+49..."
        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        required
      />
    </div>
    
    <div>
      <label class="block text-sm font-medium mb-1" for="login-password">Şifre</label>
      <input 
        id="login-password"
        type="password" 
        bind:value={password}
        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        required
      />
    </div>
    
    <button 
      type="submit" 
      class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 transition"
      disabled={loading}
    >
      {loading ? 'Giriş yapılıyor...' : 'Giriş Yap'}
    </button>
  </form>
  
  <p class="text-center mt-6 text-gray-600">
    Hesabınız yok mu? <a href="/kayit" class="text-blue-600 hover:underline">Kayıt Ol</a>
  </p>
</div>
