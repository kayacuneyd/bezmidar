<script>
  import { api } from '$lib/utils/api.js';
  import { authStore } from '$lib/stores/auth.js';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';

  let role = $page.url.searchParams.get('role') || 'parent';
  
  let formData = {
    full_name: '',
    phone: '',
    password: '',
    role: role,
    city: '',
    zip_code: ''
  };
  
  let error = '';
  let loading = false;

  import { toast } from '$lib/stores/toast.js';

  async function handleRegister() {
    loading = true;
    error = '';
    
    try {
      const response = await api.post('/auth/register.php', formData);
      authStore.login(response.data.user, response.data.token);
      
      toast.success('Kayıt başarıyla oluşturuldu! Hoşgeldiniz.');
      
      if (formData.role === 'student') {
        goto('/panel/ayarlar'); // Profilini doldurması için
      } else {
        goto('/panel');
      }
    } catch (e) {
      const msg = e.message || 'Kayıt başarısız';
      error = msg;
      toast.error(msg);
    } finally {
      loading = false;
    }
  }
</script>

<svelte:head>
  <title>Kayıt Ol - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-12 max-w-md">
  <h1 class="text-3xl font-bold mb-8 text-center">Kayıt Ol</h1>
  
  <div class="flex justify-center gap-4 mb-8">
    <button 
      class="px-4 py-2 rounded-full {formData.role === 'parent' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-500'}"
      on:click={() => formData.role = 'parent'}
    >
      Veli / Öğrenci
    </button>
    <button 
      class="px-4 py-2 rounded-full {formData.role === 'student' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-500'}"
      on:click={() => formData.role = 'student'}
    >
      Öğretmen
    </button>
  </div>
  
  {#if error}
    <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
      {error}
    </div>
  {/if}
  
  <form on:submit|preventDefault={handleRegister} class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-1">Ad Soyad</label>
      <input 
        type="text" 
        bind:value={formData.full_name}
        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        required
      />
    </div>
    
    <div>
      <label class="block text-sm font-medium mb-1">Telefon Numarası</label>
      <input 
        type="tel" 
        bind:value={formData.phone}
        placeholder="+49..."
        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        required
      />
    </div>
    
    <div>
      <label class="block text-sm font-medium mb-1">Şehir</label>
      <input 
        type="text" 
        bind:value={formData.city}
        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        required
        placeholder="Örn: Berlin"
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Posta Kodu (PLZ)</label>
      <input 
        type="text" 
        bind:value={formData.zip_code}
        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        required
        placeholder="Örn: 10115"
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Şifre</label>
      <input 
        type="password" 
        bind:value={formData.password}
        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        required
        minlength="6"
      />
    </div>
    
    <button 
      type="submit" 
      class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 transition"
      disabled={loading}
    >
      {loading ? 'Kayıt yapılıyor...' : 'Kayıt Ol'}
    </button>
  </form>
  
  <p class="text-center mt-6 text-gray-600">
    Zaten hesabınız var mı? <a href="/giris" class="text-blue-600 hover:underline">Giriş Yap</a>
  </p>
</div>
