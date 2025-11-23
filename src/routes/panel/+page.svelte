<script>
  import { authStore } from '$lib/stores/auth.js';
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import RewardsPanel from '$lib/components/RewardsPanel.svelte';
  
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

{#if $authStore.isAuthenticated}
<div class="min-h-screen bg-gray-50/50 pb-12">
  <!-- Welcome Header -->
  <div class="bg-white border-b border-gray-100">
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Hoşgeldin, {user?.full_name} 👋</h1>
          <p class="text-gray-500 mt-1">Bugün ne yapmak istersin?</p>
        </div>
        <div class="flex items-center gap-3">
          <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-semibold border border-blue-100">
            {user?.role === 'student' ? 'Öğretmen Hesabı' : 'Veli / Öğrenci Hesabı'}
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="container mx-auto px-4 py-8">
    
    <!-- Approval Status Alert for Teachers -->
    {#if user?.role === 'student' && user?.approval_status === 'pending'}
      <div class="mb-6 bg-orange-50 border-l-4 border-orange-500 p-6 rounded-lg">
        <div class="flex items-start gap-4">
          <div class="text-3xl">⏳</div>
          <div class="flex-1">
            <h3 class="font-bold text-orange-900 text-lg mb-2">Profiliniz İnceleniyor</h3>
            <p class="text-orange-800 leading-relaxed">
              Öğretmen kaydınız yöneticilerimiz tarafından incelenmektedir. Onaylandıktan sonra profiliniz öğrenci ve veliler tarafından görülebilecek ve haritada listelenecektir.
            </p>
            <p class="text-orange-700 text-sm mt-3">
              💡 Bu süreçte <a href="/panel/ayarlar" class="underline font-semibold">profil bilgilerinizi</a> tamamlayabilirsiniz.
            </p>
          </div>
        </div>
      </div>
    {/if}
    
    <div class="grid md:grid-cols-3 gap-6">
      
      <!-- Quick Actions -->
      <div class="md:col-span-2 space-y-6">
        <h2 class="text-lg font-bold text-gray-900">Hızlı İşlemler</h2>
        
        <div class="grid sm:grid-cols-2 gap-4">
          <!-- Profile Card -->
          <a href="/panel/ayarlar" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 transition flex items-start gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition">
              👤
            </div>
            <div>
              <h3 class="font-bold text-gray-900 mb-1">Profilim</h3>
              <p class="text-sm text-gray-500">Kişisel bilgilerinizi ve tercihlerinizi düzenleyin.</p>
            </div>
          </a>

          <!-- Messages Card -->
          <a href="/panel/mesajlar" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-green-200 transition flex items-start gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition">
              💬
            </div>
            <div>
              <h3 class="font-bold text-gray-900 mb-1">Mesajlar</h3>
              <p class="text-sm text-gray-500">Öğretmenler ve velilerle olan görüşmeleriniz.</p>
            </div>
          </a>

          {#if user?.role === 'parent'}
            <!-- Requests Card -->
            <a href="/panel/taleplerim" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-200 transition flex items-start gap-4">
              <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition">
                📝
              </div>
              <div>
                <h3 class="font-bold text-gray-900 mb-1">Ders Taleplerim</h3>
                <p class="text-sm text-gray-500">Oluşturduğunuz özel ders ilanlarını yönetin.</p>
              </div>
            </a>
            
            <!-- New Request Card -->
            <a href="/panel/taleplerim/yeni" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-orange-200 transition flex items-start gap-4">
              <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition">
                ✨
              </div>
              <div>
                <h3 class="font-bold text-gray-900 mb-1">Yeni Talep</h3>
                <p class="text-sm text-gray-500">Hızlıca yeni bir özel ders ilanı oluşturun.</p>
              </div>
            </a>
          {/if}
        </div>
      </div>

      <!-- Sidebar / Stats -->
      <div class="space-y-6">
        <h2 class="text-lg font-bold text-gray-900">Özet</h2>
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-2xl font-bold text-gray-600">
              {user?.full_name?.charAt(0) || '?'}
            </div>
            <div>
              <div class="font-bold text-gray-900">{user?.full_name}</div>
              <div class="text-sm text-gray-500">{user?.city || 'Konum belirtilmedi'}</div>
            </div>
          </div>
          
          {#if user?.role === 'student'}
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-50">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">0</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Görüntülenme</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">0</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Yorum</div>
              </div>
            </div>
          {:else}
             <div class="pt-4 border-t border-gray-50">
               <div class="flex justify-between items-center mb-2">
                 <span class="text-sm text-gray-600">Üyelik Durumu</span>
                 <span class="text-sm font-bold text-green-600">Aktif</span>
               </div>
               <div class="flex justify-between items-center">
                 <span class="text-sm text-gray-600">Son Giriş</span>
                 <span class="text-sm text-gray-900">{new Date().toLocaleDateString('tr-TR')}</span>
               </div>
             </div>
          {/if}
        </div>
        
        <RewardsPanel />
        
        <!-- Support Card -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-6 rounded-2xl shadow-lg text-white">
          <h3 class="font-bold text-lg mb-2">Yardıma mı ihtiyacınız var?</h3>
          <p class="text-blue-100 text-sm mb-4">Destek ekibimiz haftanın 7 günü sorularınızı yanıtlamak için burada.</p>
          <button class="w-full bg-white text-blue-600 py-2 rounded-lg font-bold text-sm hover:bg-blue-50 transition">
            Destek ile İletişime Geç
          </button>
        </div>
      </div>
      
    </div>
  </div>
</div>
{:else}
  <div class="container mx-auto px-4 py-20 flex flex-col items-center gap-4 text-center">
    <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
    <p class="text-gray-600">Yönlendiriliyorsunuz...</p>
  </div>
{/if}
