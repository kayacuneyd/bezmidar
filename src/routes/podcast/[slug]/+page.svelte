<script>
  import { page } from '$app/stores';
  import { onMount } from 'svelte';
  import { api } from '$lib/utils/api.js';
  import PodcastPlayer from '$lib/components/PodcastPlayer.svelte';
  import PodcastEpisodeCard from '$lib/components/PodcastEpisodeCard.svelte';

  let episode = null;
  let relatedEpisodes = [];
  let loading = true;
  let error = null;
  let showTranscript = false;

  $: slug = $page.params.slug;

  onMount(() => {
    if (slug && slug !== 'feed.xml' && slug !== 'rss') {
      loadEpisode();
    }
  });

  async function loadEpisode() {
    loading = true;
    error = null;

    try {
      const res = await api.get('/podcast/detail.php', { slug });

      if (res.success) {
        episode = res.data.episode;
        relatedEpisodes = res.data.related || [];
      } else {
        error = res.error || 'Episode bulunamadı';
      }
    } catch (err) {
      console.error('Load episode error:', err);
      error = 'Bağlantı hatası';
    } finally {
      loading = false;
    }
  }

  function shareOnWhatsApp() {
    const text = encodeURIComponent(`${episode.title} - Dijital Mentor Podcast`);
    const url = encodeURIComponent(window.location.href);
    window.open(`https://wa.me/?text=${text}%20${url}`, '_blank');
  }

  function shareOnTwitter() {
    const text = encodeURIComponent(`${episode.title} - @DijitalMentor`);
    const url = encodeURIComponent(window.location.href);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
  }

  function copyLink() {
    navigator.clipboard.writeText(window.location.href);
    alert('Link kopyalandı!');
  }
</script>

<svelte:head>
  {#if episode}
    <title>{episode.title} - Dijital Mentor Podcast</title>
    <meta name="description" content={episode.description || episode.title} />
    <link rel="canonical" href={$page.url.href} />

    <!-- Open Graph -->
    <meta property="og:title" content={episode.title} />
    <meta property="og:description" content={episode.description || ''} />
    <meta property="og:image" content={episode.cover_image_url || 'https://dijitalmentor.de/default-podcast-cover.jpg'} />
    <meta property="og:url" content={$page.url.href} />
    <meta property="og:type" content="music.song" />
    <meta property="music:duration" content={episode.duration_seconds || ''} />
    <meta property="music:release_date" content={episode.publish_date} />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content={episode.title} />
    <meta name="twitter:description" content={episode.description || ''} />
    <meta name="twitter:image" content={episode.cover_image_url || 'https://dijitalmentor.de/default-podcast-cover.jpg'} />

    <!-- JSON-LD Structured Data -->
    {@html `<script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "PodcastEpisode",
        "name": "${episode.title}",
        "description": "${episode.description ? episode.description.replace(/"/g, '\\"') : ''}",
        "image": "${episode.cover_image_url || 'https://dijitalmentor.de/default-podcast-cover.jpg'}",
        "datePublished": "${episode.publish_date}",
        "timeRequired": "PT${Math.floor((episode.duration_seconds || 0) / 60)}M",
        "associatedMedia": {
          "@type": "MediaObject",
          "contentUrl": "${episode.audio_url || ''}"
        },
        "partOfSeries": {
          "@type": "PodcastSeries",
          "name": "Dijital Mentor Podcast",
          "url": "https://dijitalmentor.de/podcast"
        }
      }
    </script>`}
  {/if}
</svelte:head>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
  {#if loading}
    <div class="flex justify-center items-center py-32">
      <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
  {:else if error}
    <div class="max-w-2xl mx-auto px-4 py-20 text-center">
      <div class="text-red-600 text-6xl mb-4">😕</div>
      <h1 class="text-3xl font-bold text-gray-900 mb-4">Episode Bulunamadı</h1>
      <p class="text-gray-600 mb-8">{error}</p>
      <a href="/podcast" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-block">
        ← Tüm Bölümlere Dön
      </a>
    </div>
  {:else if episode}
    <!-- Hero Section with Cover -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-8">
      <div class="container mx-auto px-4">
        <a href="/podcast" class="text-blue-100 hover:text-white mb-4 inline-flex items-center gap-2">
          ← Tüm Bölümler
        </a>
      </div>
    </div>

    <!-- Episode Content -->
    <div class="container mx-auto px-4 py-8">
      <div class="max-w-4xl mx-auto">
        <!-- Cover Image & Info -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
          <div class="grid md:grid-cols-3 gap-6 p-6">
            <div class="md:col-span-1">
              {#if episode.cover_image_url}
                <img
                  src={episode.cover_image_url}
                  alt={episode.title}
                  class="w-full aspect-square object-cover rounded-xl"
                />
              {:else}
                <div class="w-full aspect-square bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center text-white text-6xl">
                  🎙️
                </div>
              {/if}
            </div>

            <div class="md:col-span-2 flex flex-col justify-between">
              <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-3">{episode.title}</h1>
                {#if episode.description}
                  <p class="text-gray-600 mb-4">{episode.description}</p>
                {/if}
                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                  {#if episode.duration_formatted}
                    <span>⏱️ {episode.duration_formatted}</span>
                  {/if}
                  <span>📅 {new Date(episode.publish_date).toLocaleDateString('tr-TR', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                </div>
              </div>

              <!-- Share Buttons -->
              <div class="flex gap-3 mt-6">
                <button
                  on:click={shareOnWhatsApp}
                  class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition flex items-center gap-2"
                >
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                  </svg>
                  WhatsApp
                </button>

                <button
                  on:click={shareOnTwitter}
                  class="px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition flex items-center gap-2"
                >
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                  Twitter
                </button>

                <button
                  on:click={copyLink}
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                  📋 Link Kopyala
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Audio Player -->
        {#if episode.audio_url}
          <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <PodcastPlayer audioUrl={episode.audio_url} title={episode.title} />
          </div>
        {/if}

        <!-- YouTube Embed -->
        {#if episode.youtube_video_id}
          <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">📺 YouTube'da İzle</h2>
            <div class="aspect-video">
              <iframe
                src={`https://www.youtube.com/embed/${episode.youtube_video_id}`}
                title={episode.title}
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                class="w-full h-full rounded-lg"
              ></iframe>
            </div>
          </div>
        {/if}

        <!-- Transcript -->
        {#if episode.transcript}
          <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <button
              on:click={() => (showTranscript = !showTranscript)}
              class="flex items-center justify-between w-full text-left"
            >
              <h2 class="text-xl font-semibold text-gray-900">📄 Transkript</h2>
              <span class="text-2xl text-gray-400">{showTranscript ? '−' : '+'}</span>
            </button>

            {#if showTranscript}
              <div class="mt-4 prose prose-blue max-w-none">
                {@html episode.transcript.replace(/\n/g, '<br>')}
              </div>
            {/if}
          </div>
        {/if}

        <!-- Related Episodes -->
        {#if relatedEpisodes.length > 0}
          <div class="mt-12">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Diğer Bölümler</h2>
            <div class="grid md:grid-cols-3 gap-6">
              {#each relatedEpisodes as related}
                <PodcastEpisodeCard episode={related} />
              {/each}
            </div>
          </div>
        {/if}
      </div>
    </div>
  {/if}
</div>
