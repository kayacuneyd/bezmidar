<script>
  // Accept SvelteKit props to silence unknown prop warnings in dev
  export let data;
  export let params;

  import { onMount } from 'svelte';
  import { api } from '$lib/utils/api.js';
  import { fade } from 'svelte/transition';

  let requests = [];
  let loading = true;
  let error = '';

  onMount(async () => {
    try {
      const response = await api.get('/requests/list.php');
      requests = response.data.requests || response.data; // Handle both structures
    } catch (e) {
      error = e.message;
    } finally {
      loading = false;
    }
  });
</script>

<svelte:head>
  <title>Ders Talepleri - Bezmidar</title>
</svelte:head>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-end">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ders Talepleri</h1>
        <p class="text-gray-600 mt-2">Velilerin oluşturduğu özel ders ilanları</p>
      </div>
      <a href="/panel/taleplerim/yeni" class="hidden md:inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
        <span>+</span> Yeni Talep Oluştur
      </a>
    </div>

  {#if loading}
    <div class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>
  {:else if error}
    <div class="bg-red-50 text-red-700 p-4 rounded-lg">
      {error}
    </div>
  {:else if requests.length === 0}
    <div class="text-center py-12 bg-gray-50 rounded-lg">
      <p class="text-gray-600 text-lg">Henüz aktif bir talep bulunmuyor.</p>
    </div>
  {:else}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      {#each requests as request}
        <a href="/ders-talepleri/{request.id}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 overflow-hidden group">
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                {request.subject_name}
              </span>
              <span class="text-gray-500 text-sm flex items-center gap-1">
                📍 {request.city}
              </span>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition">
              {request.title}
            </h3>
            
            <p class="text-gray-600 text-sm line-clamp-2 mb-4">
              {request.description}
            </p>
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs">
                  {request.parent_name ? request.parent_name.charAt(0) : 'V'}
                </div>
                <span class="text-sm text-gray-700">{request.parent_name}</span>
              </div>
              <div class="text-green-600 font-semibold text-sm">
                {request.budget_range}
              </div>
            </div>
          </div>
        </a>
      {/each}
    </div>
  {/if}
</div>
