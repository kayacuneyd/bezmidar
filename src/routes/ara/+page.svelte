<script>
  import { onMount } from 'svelte';
  import { api } from '$lib/utils/api.js';
  import TeacherCard from '$lib/components/TeacherCard.svelte';
  import FilterSidebar from '$lib/components/FilterSidebar.svelte';
  import MapView from '$lib/components/MapView.svelte';
  
  let teachers = [];
  let subjects = [];
  let loading = true;
  let loadingMore = false;
  let error = null;
  let viewMode = 'list'; // 'list' or 'map'
  
  // Pagination state
  let pagination = {
    currentPage: 1,
    totalPages: 1,
    total: 0,
    hasMore: false
  };
  
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
  
  async function loadTeachers(reset = true) {
    if (reset) {
      loading = true;
      teachers = [];
      filters.page = 1;
    } else {
      loadingMore = true;
    }
    error = null;
    
    try {
      const response = await api.get('/teachers/list.php', filters);
      const newTeachers = response.data.teachers || [];
      
      if (reset) {
        teachers = newTeachers;
      } else {
        teachers = [...teachers, ...newTeachers];
      }
      
      // Update pagination info
      if (response.data.pagination) {
        pagination = {
          currentPage: response.data.pagination.page || filters.page,
          totalPages: response.data.pagination.pages || 1,
          total: response.data.pagination.total || 0,
          hasMore: filters.page < (response.data.pagination.pages || 1)
        };
      }
    } catch (e) {
      error = 'Öğretmenler yüklenemedi. Lütfen tekrar deneyin.';
    } finally {
      loading = false;
      loadingMore = false;
    }
  }
  
  async function loadMoreTeachers() {
    if (loadingMore || !pagination.hasMore) return;
    
    filters.page += 1;
    await loadTeachers(false);
  }
  
  function applyFilters(newFilters) {
    filters = { ...filters, ...newFilters, page: 1 };
    loadTeachers(true);
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
          <div class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
              {#each teachers as teacher}
                <TeacherCard {teacher} />
              {/each}
            </div>
            
            <!-- Pagination Info -->
            {#if pagination.total > 0}
              <div class="text-center text-sm text-gray-600 py-2">
                {teachers.length} / {pagination.total} öğretmen gösteriliyor
              </div>
            {/if}
            
            <!-- Load More Button -->
            {#if pagination.hasMore && !loadingMore}
              <div class="text-center pt-4">
                <button
                  on:click={loadMoreTeachers}
                  class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-100"
                >
                  Daha Fazla Yükle
                </button>
              </div>
            {/if}
            
            {#if loadingMore}
              <div class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-2 text-sm text-gray-600">Yükleniyor...</p>
              </div>
            {/if}
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
