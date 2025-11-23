<script>
  import { page } from '$app/stores';
  import { onMount } from 'svelte';
  import { api } from '$lib/utils/api';
  import ShareButtons from '$lib/components/ShareButtons.svelte';
  import CommentSection from '$lib/components/CommentSection.svelte';

  let post = null;
  let loading = true;
  let error = null;

  // Reactive statement to fetch data when slug changes
  $: slug = $page.params.slug;
  
  $: if (slug) {
    loadPost(slug);
  }

  async function loadPost(slug) {
    loading = true;
    const res = await api.get(`/blog/detail.php?slug=${slug}`);
    if (res.success) {
      post = res.data;
    } else {
      error = res.message;
    }
    loading = false;
  }
</script>

<svelte:head>
  {#if post}
    <title>{post.title} - DijitalMentor Blog</title>
    <meta name="description" content={post.excerpt} />
    <meta property="og:title" content={post.title} />
    <meta property="og:description" content={post.excerpt} />
    <meta property="og:image" content={post.image} />
    <meta property="og:type" content="article" />
  {:else}
    <title>Blog - DijitalMentor</title>
  {/if}
</svelte:head>

<div class="container mx-auto px-4 py-12 max-w-4xl">
  {#if loading}
    <div class="flex justify-center py-12">
      <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
  {:else if error}
    <div class="text-center py-12">
      <h1 class="text-2xl font-bold text-gray-900 mb-4">Hata</h1>
      <p class="text-gray-600">{error}</p>
      <a href="/blog" class="text-blue-600 hover:underline mt-4 inline-block">Blog Ana Sayfasına Dön</a>
    </div>
  {:else if post}
    <article>
      <!-- Header -->
      <header class="mb-8 text-center">
        <div class="flex items-center justify-center gap-2 text-sm text-gray-500 mb-4">
          <span>{post.date}</span>
          <span>•</span>
          <span>{post.author}</span>
        </div>
        <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
          {post.title}
        </h1>
        <img 
          src={post.image} 
          alt={post.title} 
          class="w-full h-64 md:h-96 object-cover rounded-2xl shadow-lg mb-8"
        />
      </header>

      <!-- Content -->
      <div class="prose prose-lg prose-blue max-w-none mb-12">
        {@html post.content}
      </div>

      <!-- Share -->
      <div class="flex justify-between items-center border-t border-b border-gray-200 py-6 mb-12">
        <div class="flex items-center gap-2">
          <button class="flex items-center gap-2 text-gray-600 hover:text-red-500 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            <span>{post.likes} Beğeni</span>
          </button>
        </div>
        <ShareButtons title={post.title} url={$page.url.href} />
      </div>

      <!-- Comments -->
      <CommentSection comments={post.comments} postId={post.id} />
      
    </article>
  {/if}
</div>
