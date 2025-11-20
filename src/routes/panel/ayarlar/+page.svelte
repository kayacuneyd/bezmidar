<script>
  import { authStore } from '$lib/stores/auth.js';
  import { toast } from '$lib/stores/toast.js';
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { get } from 'svelte/store';

  let user = null;
  let loading = false;
  let saving = false;
  let uploadingAvatar = false;
  let uploadingCV = false;
  let showPremiumModal = false;
  
  let formData = {
    full_name: '',
    email: '',
    city: '',
    zip_code: '',
    bio: '',
    university: '',
    department: '',
    graduation_year: null,
    hourly_rate: 20,
    experience_years: 0
  };
  
  let newPassword = '';
  let confirmPassword = '';

  onMount(() => {
    const auth = get(authStore);
    if (!auth.isAuthenticated) {
      goto('/giris?redirect=/panel/ayarlar');
      return;
    }
    user = auth.user;
    
    // Populate form
    formData.full_name = user.full_name || '';
    formData.email = user.email || '';
    formData.city = user.city || '';
    formData.zip_code = user.zip_code || '';
    formData.bio = user.bio || '';
    formData.university = user.university || '';
    formData.department = user.department || '';
    formData.graduation_year = user.graduation_year || null;
    formData.hourly_rate = user.hourly_rate || 20;
    formData.experience_years = user.experience_years || 0;
  });

  async function handleSave() {
    saving = true;
    await new Promise(r => setTimeout(r, 1000));
    toast.success('Profiliniz başarıyla güncellendi.');
    saving = false;
  }
  
  async function handleAvatarUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
      toast.error('Dosya çok büyük. Maksimum 2MB olmalı.');
      return;
    }
    
    uploadingAvatar = true;
    await new Promise(r => setTimeout(r, 1000));
    
    // Mock: Update user avatar
    user.avatar_url = URL.createObjectURL(file);
    toast.success('Profil fotoğrafı güncellendi.');
    uploadingAvatar = false;
  }
  
  async function handleCVUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Check premium status
    if (!user?.is_premium) {
      showPremiumModal = true;
      event.target.value = '';
      return;
    }
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
      toast.error('Dosya çok büyük. Maksimum 2MB olmalı.');
      return;
    }
    
    uploadingCV = true;
    await new Promise(r => setTimeout(r, 1000));
    toast.success('CV başarıyla yüklendi.');
    uploadingCV = false;
  }
  
  async function handlePasswordChange() {
    if (!newPassword || !confirmPassword) {
      toast.error('Lütfen tüm alanları doldurun.');
      return;
    }
    
    if (newPassword !== confirmPassword) {
      toast.error('Şifreler eşleşmiyor.');
      return;
    }
    
    if (newPassword.length < 6) {
      toast.error('Şifre en az 6 karakter olmalı.');
      return;
    }
    
    saving = true;
    await new Promise(r => setTimeout(r, 1000));
    toast.success('Şifreniz başarıyla güncellendi.');
    newPassword = '';
    confirmPassword = '';
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
  <div class="container mx-auto px-4 max-w-4xl">
    
    <!-- Header Section -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Hesap Ayarları</h1>
      <p class="text-gray-500 mt-2">Profil bilgilerinizi ve tercihlerinizi yönetin.</p>
    </div>

    {#if user}
      <div class="space-y-6">
        
        <!-- Profile Photo Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Profil Fotoğrafı</h2>
          <div class="flex items-center gap-6">
            <div class="relative">
              {#if user.avatar_url}
                <img src={user.avatar_url} alt={user.full_name} class="w-24 h-24 rounded-full object-cover border-4 border-gray-100" />
              {:else}
                <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center text-2xl font-bold text-gray-600">
                  {getInitials(user.full_name)}
                </div>
              {/if}
              {#if uploadingAvatar}
                <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-2 border-white border-t-transparent"></div>
                </div>
              {/if}
            </div>
            <div class="flex-1">
              <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <span>📷</span>
                <span>Fotoğraf Yükle</span>
                <input type="file" accept="image/*" on:change={handleAvatarUpload} class="hidden" />
              </label>
              <p class="text-sm text-gray-500 mt-2">JPG, PNG veya WEBP. Maksimum 2MB.</p>
            </div>
          </div>
        </div>

        <!-- Personal Info Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Kişisel Bilgiler</h2>
          <form on:submit|preventDefault={handleSave} class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                <input 
                  type="text" 
                  bind:value={formData.full_name}
                  class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                <input 
                  type="email" 
                  bind:value={formData.email}
                  placeholder="ornek@email.com"
                  class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
                <input 
                  type="text" 
                  bind:value={formData.city}
                  class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Posta Kodu</label>
                <input 
                  type="text" 
                  bind:value={formData.zip_code}
                  class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                />
              </div>
            </div>
            
            {#if user.role === 'student'}
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hakkımda</label>
                <textarea 
                  bind:value={formData.bio}
                  rows="4"
                  placeholder="Kendinizi tanıtın, deneyimlerinizi ve öğretim yaklaşımınızı paylaşın..."
                  class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                ></textarea>
              </div>
              
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Üniversite</label>
                  <input 
                    type="text" 
                    bind:value={formData.university}
                    class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Bölüm</label>
                  <input 
                    type="text" 
                    bind:value={formData.department}
                    class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Mezuniyet Yılı</label>
                  <input 
                    type="number" 
                    bind:value={formData.graduation_year}
                    min="2000"
                    max="2030"
                    class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Deneyim (Yıl)</label>
                  <input 
                    type="number" 
                    bind:value={formData.experience_years}
                    min="0"
                    class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                  />
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Saatlik Ücret (€)</label>
                <input 
                  type="number" 
                  bind:value={formData.hourly_rate}
                  min="0"
                  step="0.5"
                  class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                />
              </div>
            {/if}
            
            <div class="flex justify-end pt-4 border-t">
              <button 
                type="submit" 
                class="px-8 py-2.5 bg-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/20 hover:bg-blue-700 hover:shadow-blue-600/30 active:scale-95 transition-all disabled:opacity-50"
                disabled={saving}
              >
                {saving ? 'Kaydediliyor...' : 'Değişiklikleri Kaydet'}
              </button>
            </div>
          </form>
        </div>

        <!-- Password Change Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Şifre Değiştir</h2>
          <form on:submit|preventDefault={handlePasswordChange} class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre</label>
                <input 
                  type="password" 
                  bind:value={newPassword}
                  minlength="6"
                  class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Şifre Tekrar</label>
                <input 
                  type="password" 
                  bind:value={confirmPassword}
                  minlength="6"
                  class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                />
              </div>
            </div>
            
            <div class="flex justify-end">
              <button 
                type="submit" 
                class="px-6 py-2 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition"
                disabled={saving}
              >
                Şifreyi Güncelle
              </button>
            </div>
          </form>
        </div>

        <!-- CV Upload Section (Teachers Only) -->
        {#if user.role === 'student'}
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
              <div>
                <h2 class="text-lg font-semibold text-gray-900">CV Yükle</h2>
                <p class="text-sm text-gray-500 mt-1">Premium özellik - Profesyonel CV'nizi paylaşın</p>
              </div>
              {#if !user.is_premium}
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Premium</span>
              {/if}
            </div>
            
            <div class="flex items-center gap-4">
              <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <span>📄</span>
                <span>{uploadingCV ? 'Yükleniyor...' : 'CV Seç'}</span>
                <input type="file" accept=".pdf,.doc,.docx" on:change={handleCVUpload} class="hidden" disabled={uploadingCV} />
              </label>
              <p class="text-sm text-gray-500">PDF, DOC veya DOCX. Maksimum 2MB.</p>
            </div>
            
            {#if !user.is_premium}
              <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                  <span class="font-semibold">💡 Not:</span> CV yükleme özelliği premium üyeler içindir. Premium üye olmak için yukarıdaki "Premium Üye Ol" butonuna tıklayın.
                </p>
              </div>
            {/if}
          </div>
        {/if}

        <!-- Premium Membership Section -->
        {#if !user.is_premium}
          <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-2xl p-6">
            <div class="flex items-start gap-4">
              <div class="text-4xl">⭐</div>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Premium Üyelik</h3>
                <p class="text-gray-700 mb-4">
                  {#if user.role === 'student'}
                    Premium üye olarak veli iletişim bilgilerine erişebilir ve CV yükleyebilirsiniz.
                  {:else}
                    Premium üye olarak öğretmen iletişim bilgilerine (WhatsApp) erişebilirsiniz.
                  {/if}
                </p>
                <button 
                  on:click={() => showPremiumModal = true}
                  class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-bold rounded-xl hover:from-yellow-600 hover:to-orange-600 transition shadow-lg"
                >
                  Premium Üye Ol - 10€/Yıl
                </button>
              </div>
            </div>
          </div>
        {/if}

      </div>
    {:else}
      <div class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-100 border-t-blue-600"></div>
      </div>
    {/if}
  </div>
</div>

<!-- Premium Modal -->
{#if showPremiumModal}
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
          ⭐
        </div>
        <h3 class="text-xl font-bold text-gray-900">Premium Üyelik</h3>
        <p class="text-gray-600 mt-4 leading-relaxed">
          Tüm premium özelliklere 1 yıl boyunca erişmek için <span class="font-bold text-gray-900">10€ değerinde Amazon Hediye Kartı</span>'nı aşağıdaki e-posta adresine gönderin.
        </p>
        
        <div class="bg-gray-50 p-4 rounded-xl mt-6 border border-gray-100">
          <p class="text-sm text-gray-500 mb-1">E-posta Adresi</p>
          <p class="text-lg font-mono font-bold text-blue-600 select-all">hediye@dijitalmentor.de</p>
        </div>
        
        <div class="mt-6 text-left bg-blue-50 p-4 rounded-lg">
          <p class="text-sm font-semibold text-blue-900 mb-2">Premium Özellikler:</p>
          <ul class="text-sm text-blue-800 space-y-1">
            {#if user?.role === 'student'}
              <li>✓ Veli iletişim bilgilerine erişim</li>
              <li>✓ CV yükleme</li>
              <li>✓ Öncelikli destek</li>
            {:else}
              <li>✓ Öğretmen WhatsApp bilgilerine erişim</li>
              <li>✓ Sınırsız mesajlaşma</li>
              <li>✓ Öncelikli destek</li>
            {/if}
          </ul>
        </div>
      </div>
      
      <button 
        on:click={() => showPremiumModal = false}
        class="w-full bg-gray-900 text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition shadow-lg shadow-gray-200"
      >
        Anlaşıldı, Kapat
      </button>
    </div>
  </div>
{/if}
