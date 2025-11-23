<script>
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { authStore } from '$lib/stores/auth.js';
  import { api } from '$lib/utils/api.js';
  import { toast } from '$lib/stores/toast.js';
  import MarkdownEditor from '$lib/components/MarkdownEditor.svelte';
  import { marked } from 'marked';

  let activeTab = 'teachers';

  let teacherStatus = 'pending';
  let teachers = [];
  let teacherLoading = false;

  let recentMessages = [];
  let messagesLoading = false;
  let selectedConversation = null;
  let conversationLoading = false;

  let supportMessages = [];
  let supportLoading = false;

  let blogPosts = [];
  let blogLoading = false;
  let blogForm = {
    id: null,
    slug: '',
    title: '',
    excerpt: '',
    content: '',
    content_markdown: '',
    author: '',
    image: '',
    is_published: 1
  };

  let rewardOverview = {
    parent_hours: 0,
    teacher_hours: 0,
    parent_count: 0,
    teacher_count: 0
  };
  let rewardHistory = [];
  let rewardLoading = false;
  let rewardForm = {
    user_id: '',
    hours: '',
    notes: ''
  };

  let initialized = false;

  $: authState = $authStore;

  $: if (!authState.loading) {
    if (!authState.isAuthenticated) {
      goto('/giris');
    } else if (authState.user?.role !== 'admin') {
      goto('/panel');
    }
  }

  onMount(async () => {
    if (!authState.isAuthenticated || authState.user?.role !== 'admin') {
      return;
    }
    await Promise.all([
      loadTeachers(),
      loadRecentMessages(),
      loadSupportMessages(),
      loadBlogPosts(),
      loadRewards()
    ]);
    initialized = true;
  });

  async function loadTeachers() {
    teacherLoading = true;
    try {
      const res = await api.get('/admin/teachers/list.php', { status: teacherStatus });
      teachers = res.data || [];
    } catch (err) {
      toast.error(err.message || 'Öğretmenler yüklenemedi');
    } finally {
      teacherLoading = false;
    }
  }

  async function updateTeacherStatus(userId, status) {
    try {
      await api.post('/admin/teachers/update.php', {
        user_id: userId,
        approval_status: status
      });
      toast.success('Durum güncellendi');
      loadTeachers();
    } catch (err) {
      toast.error(err.message || 'Durum güncellenemedi');
    }
  }

  async function loadRecentMessages() {
    messagesLoading = true;
    try {
      const res = await api.get('/admin/messages/recent.php', { limit: 100 });
      recentMessages = res.data || [];
    } catch (err) {
      toast.error(err.message || 'Mesajlar getirilemedi');
    } finally {
      messagesLoading = false;
    }
  }

  async function viewConversation(conversationId) {
    conversationLoading = true;
    try {
      const res = await api.get('/admin/messages/conversation.php', { conversation_id: conversationId });
      selectedConversation = res.data;
    } catch (err) {
      toast.error(err.message || 'Konuşma getirilemedi');
    } finally {
      conversationLoading = false;
    }
  }

  async function toggleUserActive(userId, isActive) {
    try {
      await api.post('/admin/users/toggle_active.php', {
        user_id: userId,
        is_active: isActive ? 1 : 0
      });
      toast.success('Kullanıcı durumu güncellendi');
      loadRecentMessages();
      loadTeachers();
    } catch (err) {
      toast.error(err.message || 'Kullanıcı güncellenemedi');
    }
  }

  async function loadSupportMessages(status = null) {
    supportLoading = true;
    try {
      const params = status ? { status } : {};
      const res = await api.get('/admin/support/messages.php', params);
      supportMessages = res.data || [];
    } catch (err) {
      toast.error(err.message || 'Destek mesajları getirilemedi');
    } finally {
      supportLoading = false;
    }
  }

  async function updateSupport(item, newStatus) {
    try {
      await api.post('/admin/support/update.php', {
        id: item.id,
        status: newStatus,
        admin_notes: item.admin_notes || ''
      });
      toast.success('Mesaj güncellendi');
      loadSupportMessages(activeSupportFilter || null);
    } catch (err) {
      toast.error(err.message || 'Mesaj güncellenemedi');
    }
  }

let activeSupportFilter = '';

  async function loadBlogPosts() {
    blogLoading = true;
    try {
      const res = await api.get('/admin/blog/list.php');
      blogPosts = res.data || [];
    } catch (err) {
      toast.error(err.message || 'Blog yazıları getirilemedi');
    } finally {
      blogLoading = false;
    }
  }

  function htmlToMarkdown(html = '') {
    if (!html) return '';
    return html
      .replace(/<\/p>\s*<p>/gi, '\n\n')
      .replace(/<br\s*\/?>/gi, '\n')
      .replace(/<\/?(strong|b)>/gi, '**')
      .replace(/<\/?(em|i)>/gi, '*')
      .replace(/<\/?h1>/gi, '\n# ')
      .replace(/<\/?h2>/gi, '\n## ')
      .replace(/<\/?h3>/gi, '\n### ')
      .replace(/<\/?ul>/gi, '\n')
      .replace(/<li>(.*?)<\/li>/gi, (_, item) => `\n- ${item.trim()}`)
      .replace(/<\/?ol>/gi, '\n')
      .replace(/<!--.*?-->/g, '')
      .replace(/<\/?[^>]+>/g, '')
      .replace(/&nbsp;/gi, ' ')
      .replace(/&amp;/gi, '&')
      .trim();
  }

  function editPost(post) {
    blogForm = {
      id: post.id,
      slug: post.slug,
      title: post.title,
      excerpt: post.excerpt,
      content: post.content || '',
      content_markdown: post.content_markdown || htmlToMarkdown(post.content || ''),
      author: post.author || '',
      image: post.image || '',
      is_published: post.is_published ? 1 : 0
    };
    activeTab = 'blog';
  }

  function resetBlogForm() {
    blogForm = {
      id: null,
      slug: '',
      title: '',
      excerpt: '',
      content: '',
      content_markdown: '',
      author: '',
      image: '',
      is_published: 1
    };
  }

  async function saveBlogPost() {
    try {
      const payload = {
        ...blogForm,
        content: blogForm.content_markdown
          ? marked.parse(blogForm.content_markdown)
          : blogForm.content
      };

      await api.post('/admin/blog/save.php', payload);
      toast.success('Yazı kaydedildi');
      resetBlogForm();
      loadBlogPosts();
    } catch (err) {
      toast.error(err.message || 'Yazı kaydedilemedi');
    }
  }

  async function deleteBlogPost(id) {
    if (!confirm('Bu yazıyı silmek istediğinize emin misiniz?')) return;
    try {
      await api.post('/admin/blog/delete.php', { id });
      toast.success('Yazı silindi');
      loadBlogPosts();
    } catch (err) {
      toast.error(err.message || 'Yazı silinemedi');
    }
  }

  async function loadRewards() {
    rewardLoading = true;
    try {
      const res = await api.get('/admin/rewards/overview.php');
      rewardOverview = res.data?.overview || rewardOverview;
      rewardHistory = res.data?.history || [];
    } catch (err) {
      toast.error(err.message || 'Ödül verileri getirilemedi');
    } finally {
      rewardLoading = false;
    }
  }

  async function addManualHours() {
    if (!rewardForm.user_id || !rewardForm.hours) {
      toast.error('Kullanıcı ve saat zorunludur');
      return;
    }
    try {
      await api.post('/admin/rewards/adjust_hours.php', {
        user_id: Number(rewardForm.user_id),
        hours: Number(rewardForm.hours),
        notes: rewardForm.notes
      });
      toast.success('Saat kaydedildi');
      rewardForm = { user_id: '', hours: '', notes: '' };
      loadRewards();
    } catch (err) {
      toast.error(err.message || 'Saat kaydedilemedi');
    }
  }
</script>

<svelte:head>
  <title>Admin Panel - DijitalMentor</title>
</svelte:head>

{#if !initialized}
  <div class="container mx-auto px-4 py-12">
    <div class="flex justify-center py-12">
      <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
  </div>
{:else}
  <div class="container mx-auto px-4 py-8 space-y-8">
    <div>
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Paneli</h1>
      <p class="text-gray-600">Mentor onayı, mesaj denetimi, destek ve içerik yönetimi</p>
    </div>

    <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-2">
      {#each [
        { id: 'teachers', label: 'Öğretmen Onayları' },
        { id: 'messages', label: 'Mesaj Denetimi' },
        { id: 'support', label: 'Destek Kutusu' },
        { id: 'blog', label: 'Blog Yönetimi' },
        { id: 'rewards', label: 'Ödül & Saat Takibi' }
      ] as tab}
        <button
          class={`px-4 py-2 rounded-full text-sm font-semibold transition ${activeTab === tab.id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
          on:click={() => activeTab = tab.id}
        >
          {tab.label}
        </button>
      {/each}
    </div>

    {#if activeTab === 'teachers'}
      <section class="space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <h2 class="text-2xl font-semibold text-gray-900">Öğretmen Onayları</h2>
          <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600" for="teacher-status">Durum:</label>
            <select
              id="teacher-status"
              bind:value={teacherStatus}
              on:change={loadTeachers}
              class="border rounded-lg px-3 py-1 text-sm"
            >
              <option value="pending">Beklemede</option>
              <option value="approved">Onaylı</option>
              <option value="rejected">Reddedildi</option>
            </select>
          </div>
        </div>

        {#if teacherLoading}
          <div class="flex justify-center py-12">
            <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
          </div>
        {:else}
          <div class="overflow-x-auto bg-white rounded-2xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
              <thead class="bg-gray-50 text-gray-600">
                <tr>
                  <th class="px-4 py-3 text-left font-semibold">Ad Soyad</th>
                  <th class="px-4 py-3 text-left font-semibold">İletişim</th>
                  <th class="px-4 py-3 text-left font-semibold">Üniversite</th>
                  <th class="px-4 py-3 text-center font-semibold">Saatlik Ücret</th>
                  <th class="px-4 py-3 text-center font-semibold">Durum</th>
                  <th class="px-4 py-3 text-center font-semibold">İşlemler</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                {#each teachers as teacher}
                  <tr>
                    <td class="px-4 py-3">
                      <div class="font-semibold text-gray-900">{teacher.full_name}</div>
                      <div class="text-xs text-gray-500">{new Date(teacher.created_at).toLocaleDateString('tr-TR')}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                      <div>{teacher.email || '—'}</div>
                      <div>{teacher.phone || '—'}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                      <div>{teacher.university || '—'}</div>
                      <div class="text-xs text-gray-500">{teacher.department || ''}</div>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-900 font-semibold">
                      €{teacher.hourly_rate || '-'}
                    </td>
                    <td class="px-4 py-3 text-center">
                      <span class={`px-3 py-1 rounded-full text-xs font-semibold ${teacher.approval_status === 'approved' ? 'bg-green-100 text-green-700' : teacher.approval_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'}`}>
                        {teacher.approval_status}
                      </span>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex flex-wrap gap-2 justify-center">
                        <button
                          class="px-3 py-1 text-xs rounded-full bg-green-600 text-white hover:bg-green-700 disabled:opacity-50"
                          on:click={() => updateTeacherStatus(teacher.id, 'approved')}
                          disabled={teacher.approval_status === 'approved'}
                        >
                          Onayla
                        </button>
                        <button
                          class="px-3 py-1 text-xs rounded-full bg-red-600 text-white hover:bg-red-700 disabled:opacity-50"
                          on:click={() => updateTeacherStatus(teacher.id, 'rejected')}
                          disabled={teacher.approval_status === 'rejected'}
                        >
                          Reddet
                        </button>
                        <button
                          class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200"
                          on:click={() => toggleUserActive(teacher.id, teacher.is_active ? 0 : 1)}
                        >
                          {teacher.is_active ? 'Hesabı Kapat' : 'Hesabı Aç'}
                        </button>
                      </div>
                    </td>
                  </tr>
                {:else}
                  <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Listelenecek öğretmen bulunamadı.</td>
                  </tr>
                {/each}
              </tbody>
            </table>
          </div>
        {/if}
      </section>
    {:else if activeTab === 'messages'}
      <section class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-semibold text-gray-900">Mesaj Denetimi</h2>
          <button class="text-sm text-blue-600 hover:underline" on:click={loadRecentMessages}>Yenile</button>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 text-sm font-semibold text-gray-600">
              Son Mesajlar
            </div>
            {#if messagesLoading}
              <div class="flex justify-center py-8">
                <div class="w-6 h-6 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
              </div>
            {:else}
              <ul class="divide-y divide-gray-100 max-h-[520px] overflow-y-auto">
                {#each recentMessages as msg}
                  <li>
                    <button
                      type="button"
                      class="w-full text-left p-4 flex flex-col gap-1 hover:bg-gray-50"
                      on:click={() => viewConversation(msg.conversation_id)}
                    >
                      <div class="flex items-center justify-between">
                        <div class="font-semibold text-gray-900">{msg.sender_name}</div>
                        <div class="text-xs text-gray-500">{new Date(msg.created_at).toLocaleString('tr-TR')}</div>
                      </div>
                      <div class="text-xs text-gray-500">{msg.sender_role === 'student' ? 'Öğretmen' : 'Veli'} · Konuşma #{msg.conversation_id}</div>
                      <div class="text-sm text-gray-700 line-clamp-2">{msg.message_text}</div>
                    </button>
                  </li>
                {:else}
                  <li class="p-4 text-center text-gray-500">Mesaj bulunamadı.</li>
                {/each}
              </ul>
            {/if}
          </div>

          <div class="bg-white rounded-2xl border border-gray-100 p-4 min-h-[560px] space-y-3">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Konuşma Detayı</h3>
                {#if selectedConversation}
                  <p class="text-sm text-gray-500">
                    Öğretmen: {selectedConversation.conversation.teacher_name || selectedConversation.conversation.teacher_id} ·
                    Veli: {selectedConversation.conversation.parent_name || selectedConversation.conversation.parent_id}
                  </p>
                {/if}
              </div>
              {#if conversationLoading}
                <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
              {/if}
            </div>
            {#if selectedConversation}
              <div class="border border-gray-100 rounded-xl h-[460px] overflow-y-auto p-3 space-y-3 bg-gray-50">
                {#each selectedConversation.messages as m}
                  <div class={`p-3 rounded-xl ${m.sender_role === 'student' ? 'bg-blue-100 text-blue-900' : 'bg-white'}`}>
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                      <span class="font-semibold text-gray-800">{m.sender_name}</span>
                      <span>{new Date(m.created_at).toLocaleString('tr-TR')}</span>
                    </div>
                    <p class="text-sm text-gray-800 whitespace-pre-line">{m.message_text}</p>
                    <div class="text-xs text-gray-500 mt-2">
                      <button
                        class="text-red-600 hover:underline"
                        on:click={() => toggleUserActive(m.sender_id, 0)}
                      >
                        Kullanıcıyı Engelle
                      </button>
                    </div>
                  </div>
                {/each}
              </div>
            {:else}
              <div class="h-[460px] flex items-center justify-center text-gray-400">
                İncelenecek bir konuşma seçin.
              </div>
            {/if}
          </div>
        </div>
      </section>
    {:else if activeTab === 'support'}
      <section class="space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <h2 class="text-2xl font-semibold text-gray-900">Destek Kutusu</h2>
          <div class="flex items-center gap-2 text-sm">
            <label for="support-filter">Durum:</label>
            <select
              id="support-filter"
              class="border rounded-lg px-3 py-1"
              bind:value={activeSupportFilter}
              on:change={() => loadSupportMessages(activeSupportFilter || null)}
            >
              <option value="">Tümü</option>
              <option value="new">Yeni</option>
              <option value="in_progress">İnceleniyor</option>
              <option value="resolved">Çözüldü</option>
            </select>
          </div>
        </div>
        {#if supportLoading}
          <div class="flex justify-center py-12">
            <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
          </div>
        {:else}
          <div class="space-y-4">
            {#each supportMessages as msg}
              <div class="bg-white border border-gray-100 rounded-2xl p-5 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-3">
                  <div>
                    <div class="font-semibold text-gray-900">{msg.name}</div>
                    <div class="text-sm text-gray-500">{msg.email}</div>
                  </div>
                  <span class={`px-3 py-1 rounded-full text-xs font-semibold ${msg.status === 'resolved' ? 'bg-green-100 text-green-700' : msg.status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'}`}>
                    {msg.status}
                  </span>
                </div>
                <div class="text-sm text-gray-600">
                  <strong>Konu:</strong> {msg.subject}
                </div>
                <div class="text-gray-800 whitespace-pre-line">
                  {msg.message}
                </div>
                <div class="text-xs text-gray-500">
                  {new Date(msg.created_at).toLocaleString('tr-TR')}
                </div>
                <textarea
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                  placeholder="Not ekleyin"
                  bind:value={msg.admin_notes}
                ></textarea>
                <div class="flex flex-wrap gap-2">
                  <button class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800" on:click={() => updateSupport(msg, 'in_progress')}>İnceleniyor</button>
                  <button class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800" on:click={() => updateSupport(msg, 'resolved')}>Çözüldü</button>
                </div>
              </div>
            {:else}
              <div class="bg-white border border-dashed border-gray-200 rounded-2xl p-8 text-center text-gray-500">
                Gösterilecek mesaj yok.
              </div>
            {/each}
          </div>
        {/if}
      </section>
    {:else if activeTab === 'blog'}
      <section class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-100 rounded-2xl p-5 space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Blog Yazıları</h2>
            <button class="text-sm text-blue-600 hover:underline" on:click={loadBlogPosts}>Yenile</button>
          </div>
          {#if blogLoading}
            <div class="flex justify-center py-10">
              <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
          {:else}
            <ul class="divide-y divide-gray-100 max-h-[520px] overflow-y-auto">
              {#each blogPosts as post}
                <li class="py-3 flex flex-col gap-1">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="font-semibold text-gray-900">{post.title}</div>
                      <div class="text-xs text-gray-500">{post.slug}</div>
                    </div>
                    <div class="flex gap-2">
                      <button class="text-sm text-blue-600 hover:underline" on:click={() => editPost(post)}>Düzenle</button>
                      <button class="text-sm text-red-600 hover:underline" on:click={() => deleteBlogPost(post.id)}>Sil</button>
                    </div>
                  </div>
                  <div class="text-xs text-gray-500 flex items-center gap-2">
                    <span>{post.is_published ? 'Yayında' : 'Taslak'}</span>
                    <span>•</span>
                    <span>{new Date(post.created_at).toLocaleDateString('tr-TR')}</span>
                  </div>
                </li>
              {:else}
                <li class="py-6 text-center text-gray-500">Henüz yazı yok.</li>
              {/each}
            </ul>
          {/if}
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-5 space-y-3">
          <h2 class="text-xl font-semibold text-gray-900">{blogForm.id ? 'Yazıyı Düzenle' : 'Yeni Yazı'}</h2>
          <div class="grid gap-3">
            <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500" placeholder="Slug (ör: almanyada-turkce-egitimi)" bind:value={blogForm.slug} />
            <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500" placeholder="Başlık" bind:value={blogForm.title} />
            <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500" placeholder="Yazar" bind:value={blogForm.author} />
            <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500" placeholder="Kapak görseli URL" bind:value={blogForm.image} />
            <textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 min-h-[80px]" placeholder="Kısa özet" bind:value={blogForm.excerpt}></textarea>
            <MarkdownEditor bind:value={blogForm.content_markdown} />
            <label class="flex items-center gap-2 text-sm text-gray-700">
              <input type="checkbox" bind:checked={blogForm.is_published} />
              Yayında
            </label>
          </div>
          <div class="flex gap-3">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition" on:click={saveBlogPost}>Kaydet</button>
            {#if blogForm.id}
              <button class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition" on:click={resetBlogForm}>Yeni Yazı</button>
            {/if}
          </div>
        </div>
      </section>
    {:else if activeTab === 'rewards'}
      <section class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-semibold text-gray-900">Ödül & Saat Takibi</h2>
          <button class="text-sm text-blue-600 hover:underline" on:click={loadRewards}>Yenile</button>
        </div>
        {#if rewardLoading}
          <div class="flex justify-center py-10">
            <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
          </div>
        {:else}
          <div class="grid md:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-100 rounded-2xl p-4">
              <div class="text-xs uppercase text-gray-500 tracking-wide">Veli Toplam Saat</div>
              <div class="text-2xl font-bold text-gray-900 mt-2">{rewardOverview.parent_hours?.toFixed(1)}</div>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-4">
              <div class="text-xs uppercase text-gray-500 tracking-wide">Öğretmen Toplam Saat</div>
              <div class="text-2xl font-bold text-gray-900 mt-2">{rewardOverview.teacher_hours?.toFixed(1)}</div>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-4">
              <div class="text-xs uppercase text-gray-500 tracking-wide">Aktif Veliler</div>
              <div class="text-2xl font-bold text-gray-900 mt-2">{rewardOverview.parent_count}</div>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-4">
              <div class="text-xs uppercase text-gray-500 tracking-wide">Aktif Öğretmenler</div>
              <div class="text-2xl font-bold text-gray-900 mt-2">{rewardOverview.teacher_count}</div>
            </div>
          </div>

          <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-100 rounded-2xl p-5 space-y-3">
              <h3 class="font-semibold text-gray-900">Manuel Saat Ekle</h3>
              <div class="grid gap-3">
                <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500" placeholder="Kullanıcı ID" bind:value={rewardForm.user_id} />
                <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500" placeholder="Saat" type="number" step="0.5" bind:value={rewardForm.hours} />
                <textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 min-h-[80px]" placeholder="Not (opsiyonel)" bind:value={rewardForm.notes}></textarea>
              </div>
              <button class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition" on:click={addManualHours}>Kaydet</button>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-5">
              <h3 class="font-semibold text-gray-900 mb-3">Son Saat Kayıtları</h3>
              <div class="max-h-[360px] overflow-y-auto divide-y divide-gray-100">
                {#each rewardHistory as item}
                  <div class="py-3 text-sm">
                    <div class="flex items-center justify-between">
                      <div class="font-semibold text-gray-900">{item.full_name}</div>
                      <div class="text-xs text-gray-500">{new Date(item.completed_at).toLocaleDateString('tr-TR')}</div>
                    </div>
                    <div class="text-gray-600">Rol: {item.role === 'student' ? 'Öğretmen' : 'Veli'} · {item.hours_completed} saat</div>
                    {#if item.notes}
                      <div class="text-xs text-gray-500 mt-1">{item.notes}</div>
                    {/if}
                  </div>
                {:else}
                  <div class="py-3 text-center text-gray-500">Kayıt yok</div>
                {/each}
              </div>
            </div>
          </div>
        {/if}
      </section>
    {/if}
  </div>
{/if}
