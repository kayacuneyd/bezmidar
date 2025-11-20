<script>
  import { authStore } from '$lib/stores/auth.js';
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { get } from 'svelte/store';

  let user = null;
  let loading = false;
  let saving = false;

  onMount(() => {
    const auth = get(authStore);
    if (!auth.isAuthenticated) {
      goto('/giris?redirect=/panel/ayarlar');
      return;
    }
    user = auth.user;
  });

  import { toast } from '$lib/stores/toast.js';

  async function handleSave() {
    saving = true;
    // TODO: Implement API call to update profile
    await new Promise(r => setTimeout(r, 1000)); // Mock delay
    toast.success('Profiliniz başarıyla güncellendi.');
    saving = false;
  }
  
  function getInitials(name) {
    return name ? name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : 'U';
  }
</script>

<svelte:head>
  <title>Ayarlar - DijitalMentor</title>
</svelte:head>

<div class="min-h-screen bg-gray-50/50 py-12">
  <div class="container mx-auto px-4 max-w-3xl">
    
    <!-- Header Section -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Hesap Ayarları</h1>
      <p class="text-gray-500 mt-2">Profil bilgilerinizi ve tercihlerinizi yönetin.</p>
    </div>

    {#if user}
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Profile Header / Cover -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 h-32 relative">
          <div class="absolute -bottom-12 left-8">
            <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-lg">
              <div class="w-full h-full rounded-xl bg-gray-100 flex items-center justify-center text-2xl font-bold text-gray-600">
                {getInitials(user.full_name)}
              </div>
            </div>
          </div>
        </div>

        <!-- Form Content -->
        <div class="pt-16 px-8 pb-8">
          <form on:submit|preventDefault={handleSave} class="space-y-8">
            
            <!-- Personal Info Section -->
            <div>
              <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Kişisel Bilgiler</h2>
              <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="text-sm font-medium text-gray-700">Ad Soyad</label>
                  <input 
                    type="text" 
                    bind:value={user.full_name} 
                    class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-gray-50 text-gray-500 cursor-not-allowed" 
                    disabled
                  />
                  <p class="text-xs text-gray-400">İsim değişikliği için lütfen destek ile iletişime geçin.</p>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-medium text-gray-700">Telefon Numarası</label>
                  <input 
                    type="tel" 
                    bind:value={user.phone} 
                    class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-gray-50 text-gray-500 cursor-not-allowed" 
                    disabled
                  />
                </div>
              </div>
            </div>

            <!-- Account Info Section -->
            <div>
              <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Hesap Bilgileri</h2>
              <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="text-sm font-medium text-gray-700">Üyelik Tipi</label>
                  <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg bg-gray-50">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                      {#if user.role === 'student'}
                        🎓
                      {:else}
                        👨‍👩‍👧
                      {/if}
                    </div>
                    <span class="font-medium text-gray-900 capitalize">
                      {user.role === 'student' ? 'Öğretmen' : 'Veli / Öğrenci'}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t">
              <button 
                type="button" 
                class="px-6 py-2.5 text-gray-700 font-medium hover:bg-gray-50 rounded-xl transition-colors"
                on:click={() => window.history.back()}
              >
                İptal
              </button>
              <button 
                type="submit" 
                class="px-8 py-2.5 bg-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/20 hover:bg-blue-700 hover:shadow-blue-600/30 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                disabled={saving}
              >
                {saving ? 'Kaydediliyor...' : 'Değişiklikleri Kaydet'}
              </button>
            </div>

          </form>
        </div>
      </div>
    {:else}
      <div class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-100 border-t-blue-600"></div>
      </div>
    {/if}
  </div>
</div>
