<script>
  import { onMount } from 'svelte';
  import { api } from '$lib/utils/api';

  let posts = [];
  let loading = true;

  onMount(async () => {
    const res = await api.get('/blog/list.php');
    if (res.success) {
      posts = res.data;
    }
    loading = false;
  });
</script>

<svelte:head>
  <title>Blog - DijitalMentor</title>
  <meta name="description" content="Eğitim, çocuk gelişimi, mentorluk ve gurbette yaşam hakkında faydalı yazılar." />
</svelte:head>

<div class="container mx-auto px-4 py-12 max-w-6xl">
  <div class="text-center mb-16">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Blog</h1>
    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
      Eğitim, çocuk gelişimi ve gurbette yaşam hakkında faydalı bilgiler.
    </p>
  </div>

  {#if loading}
    <div class="flex justify-center py-12">
      <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
  {:else}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      {#each posts as post}
        <article class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition duration-300 border border-gray-100 flex flex-col">
          <a href="/blog/{post.slug}" class="block overflow-hidden h-48">
            <img 
              src={post.image} 
              alt={post.title} 
              class="w-full h-full object-cover transform hover:scale-105 transition duration-500"
            />
          </a>
          <div class="p-6 flex-1 flex flex-col">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
              <span>{post.date}</span>
              <span>•</span>
              <span>{post.author}</span>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
              <a href="/blog/{post.slug}" class="hover:text-blue-600 transition">
                {post.title}
              </a>
            </h2>
            <p class="text-gray-600 mb-4 line-clamp-3 flex-1">
              {post.excerpt}
            </p>
            <a href="/blog/{post.slug}" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1 mt-auto">
              Devamını Oku <span>→</span>
            </a>
          </div>
        </article>
      {/each}
    </div>
  {/if}
</div>
