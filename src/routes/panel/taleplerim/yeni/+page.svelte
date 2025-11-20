<script>
  import { api } from '$lib/utils/api.js';
  import { goto } from '$app/navigation';
  import { onMount } from 'svelte';

  import { authStore } from '$lib/stores/auth.js';
  import { get } from 'svelte/store';

  let subjects = [];
  let loading = false;
  
  let formData = {
    subject_id: '',
    title: '',
    description: '',
    city: '',
    budget_range: ''
  };

  onMount(async () => {
    const { isAuthenticated } = get(authStore);
    if (!isAuthenticated) {
      goto('/giris?redirect=/panel/taleplerim/yeni');
      return;
    }

    const res = await api.get('/subjects/list.php');
    subjects = res.data;
  });

  import { toast } from '$lib/stores/toast.js';

  async function handleSubmit() {
    loading = true;
    try {
      await api.post('/requests/create.php', formData);
      toast.success('Ders talebiniz başarıyla oluşturuldu.');
      goto('/panel/taleplerim');
    } catch (e) {
      toast.error('Hata: ' + e.message);
    } finally {
      loading = false;
    }
  }
</script>

<svelte:head>
  <title>Yeni Talep Oluştur - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-8 max-w-2xl">
  <h1 class="text-2xl font-bold mb-8">Yeni Ders Talebi Oluştur</h1>
  
  <form on:submit|preventDefault={handleSubmit} class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-6">
    <div>
      <label class="block text-sm font-medium mb-2">Ders Konusu</label>
      <select bind:value={formData.subject_id} class="w-full border rounded-lg px-4 py-2" required>
        <option value="">Seçiniz</option>
        {#each subjects as subject}
          <option value={subject.id}>{subject.name}</option>
        {/each}
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium mb-2">Başlık</label>
      <input 
        type="text" 
        bind:value={formData.title} 
        placeholder="Örn: Lise 2 Matematik Desteği"
        class="w-full border rounded-lg px-4 py-2" 
        required
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-2">Şehir</label>
      <input 
        type="text" 
        bind:value={formData.city} 
        placeholder="Örn: Berlin"
        class="w-full border rounded-lg px-4 py-2" 
        required
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-2">Bütçe Aralığı (Saatlik)</label>
      <input 
        type="text" 
        bind:value={formData.budget_range} 
        placeholder="Örn: 20-30€"
        class="w-full border rounded-lg px-4 py-2" 
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-2">Detaylı Açıklama</label>
      <textarea 
        bind:value={formData.description} 
        rows="4"
        placeholder="Beklentilerinizi ve öğrencinin durumunu kısaca anlatın..."
        class="w-full border rounded-lg px-4 py-2" 
        required
      ></textarea>
    </div>

    <div class="flex justify-end gap-4 pt-4">
      <a href="/panel/taleplerim" class="px-6 py-2 text-gray-600 hover:bg-gray-50 rounded-lg transition">İptal</a>
      <button 
        type="submit" 
        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition disabled:opacity-50"
        disabled={loading}
      >
        {loading ? 'Oluşturuluyor...' : 'Talebi Yayınla'}
      </button>
    </div>
  </form>
</div>
