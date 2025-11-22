<script>
  import { page } from '$app/stores';
  import { onMount } from 'svelte';
  import { api } from '$lib/utils/api.js';
  import { authStore } from '$lib/stores/auth.js';
  import { goto } from '$app/navigation';

  let request = null;
  let loading = true;
  let error = '';
  let startingConversation = false;

  onMount(async () => {
    try {
      const response = await api.get(`/requests/detail.php?id=${$page.params.id}`);
      request = response.data;
    } catch (e) {
      error = e.message;
    } finally {
      loading = false;
    }
  });

  async function handleStartConversation() {
    if (!$authStore.isAuthenticated) {
      goto('/giris');
      return;
    }

    if ($authStore.user.role !== 'student') {
      alert('Sadece öğretmenler ders taleplerine başvurabilir.');
      return;
    }

    startingConversation = true;
    try {
      console.log('Starting conversation with parent_id:', request.parent_id);
      console.log('Current user:', $authStore.user);

      const response = await api.post('/messages/start.php', {
        other_user_id: request.parent_id
      });

      console.log('Start conversation response:', response);

      if (response.success) {
        // Redirect to messages page with the conversation
        goto(`/panel/mesajlar?conversation_id=${response.data.conversation_id}`);
      } else {
        alert(`Mesajlaşma başlatılamadı: ${response.message || 'Bilinmeyen hata'}`);
      }
    } catch (e) {
      console.error('Conversation start error:', e);
      console.error('Error details:', e.message, e.stack);
      alert(`Mesajlaşma başlatılamadı: ${e.message || 'Lütfen tekrar deneyin.'}`);
    } finally {
      startingConversation = false;
    }
  }
</script>

<svelte:head>
  <title>{request ? request.title : 'Talep Detayı'} - Bezmidar</title>
</svelte:head>

<div class="container mx-auto px-4 py-8 max-w-3xl">
  {#if loading}
    <div class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>
  {:else if error}
    <div class="bg-red-50 text-red-700 p-4 rounded-lg">
      {error}
    </div>
  {:else if request}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-8">
        <div class="flex flex-wrap gap-4 justify-between items-start mb-6">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                {request.subject_name}
              </span>
              <span class="text-gray-500 flex items-center gap-1">
                📍 {request.city}
              </span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">{request.title}</h1>
          </div>
          <div class="text-right">
            <div class="text-2xl font-bold text-green-600">{request.budget_range}</div>
            <div class="text-sm text-gray-500">Saatlik Bütçe</div>
          </div>
        </div>

        <div class="prose max-w-none text-gray-700 mb-8">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Detaylar</h3>
          <p class="whitespace-pre-line">{request.description}</p>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-gray-100">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg">
              {request.parent_name ? request.parent_name.charAt(0) : 'V'}
            </div>
            <div>
              <div class="font-semibold text-gray-900">{request.parent_name}</div>
              <div class="text-sm text-gray-500">İlan Tarihi: {new Date(request.created_at).toLocaleDateString('tr-TR')}</div>
            </div>
          </div>

          {#if $authStore.isAuthenticated}
            {#if $authStore.user.role === 'student'}
              <button
                on:click={handleStartConversation}
                disabled={startingConversation}
                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                {#if startingConversation}
                  <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                  İletişim Başlatılıyor...
                {:else}
                  ✉️ İletişime Geç
                {/if}
              </button>
            {:else}
              <div class="bg-gray-100 text-gray-600 px-6 py-3 rounded-lg font-semibold text-center">
                Sadece öğretmenler başvurabilir
              </div>
            {/if}
          {:else}
            <a href="/giris" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
              Başvurmak için Giriş Yap
            </a>
          {/if}
        </div>
      </div>
    </div>
  {/if}
</div>
