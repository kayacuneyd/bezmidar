<script>
  import { onMount } from 'svelte';
  import { api } from '$lib/utils/api.js';
  import TeacherCard from '$lib/components/TeacherCard.svelte';
  import FilterSidebar from '$lib/components/FilterSidebar.svelte';
  import MapView from '$lib/components/MapView.svelte';
  
  let teachers = [];
  let subjects = [];
  let loading = true;
  let error = null;
  let viewMode = 'list'; // 'list' or 'map'
  
  // Filter state
  let filters = {
    city: '',
    subject: '',
    max_rate: null,
    page: 1
  };
  
  async function loadSubjects() {
    try {
      const response = await api.get('/subjects/list.php');
      subjects = response.data;
    } catch (e) {
      console.error('Failed to load subjects:', e);
    }
  }
  
  async function loadTeachers() {
    loading = true;
    error = null;
    
    try {
      const response = await api.get('/teachers/list.php', filters);
      teachers = response.data.teachers;
    } catch (e) {
      error = 'Öğretmenler yüklenemedi. Lütfen tekrar deneyin.';
    } finally {
      loading = false;
    }
  }
  
  function applyFilters(newFilters) {
    filters = { ...filters, ...newFilters, page: 1 };
    loadTeachers();
  }
  
  onMount(() => {
    loadSubjects();
    loadTeachers();
  });
</script>

<svelte:head>
  <title>Öğretmen Ara - DijitalMentor</title>
</svelte:head>

<div class="container mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold">Öğretmen Ara</h1>
    
    <!-- View Toggle -->
    <div class="bg-gray-100 p-1 rounded-lg flex">
      <button 
        class="px-4 py-2 rounded-md text-sm font-medium transition {viewMode === 'list' ? 'bg-white shadow text-blue-600' : 'text-gray-600 hover:text-gray-900'}"
        on:click={() => viewMode = 'list'}
      >
        📋 Liste
      </button>
      <button 
        class="px-4 py-2 rounded-md text-sm font-medium transition {viewMode === 'map' ? 'bg-white shadow text-blue-600' : 'text-gray-600 hover:text-gray-900'}"
        on:click={() => viewMode = 'map'}
      >
        🗺️ Harita
      </button>
    </div>
  </div>
  
  <div class="grid lg:grid-cols-4 gap-8">
    <!-- Sidebar -->
    <aside class="lg:col-span-1">
      <FilterSidebar {subjects} {filters} on:filter={e => applyFilters(e.detail)} />
    </aside>
    
    <!-- Results -->
    <main class="lg:col-span-3">
      {#if loading}
        <div class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-4 text-gray-600">Yükleniyor...</p>
        </div>
      {:else if error}
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
          {error}
        </div>
      {:else if teachers.length === 0}
        <div class="text-center py-12">
          <p class="text-xl text-gray-600">Henüz öğretmen bulunamadı.</p>
          <p class="text-gray-500 mt-2">Filtreleri değiştirip tekrar deneyin.</p>
        </div>
      {:else}
        {#if viewMode === 'list'}
          <div class="grid md:grid-cols-2 gap-6">
            {#each teachers as teacher}
              <TeacherCard {teacher} />
            {/each}
          </div>
        {:else}
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <MapView {teachers} />
          </div>
        {/if}
      {/if}
    </main>
  </div>
</div>
