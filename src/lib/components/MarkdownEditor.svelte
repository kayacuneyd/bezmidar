<script>
  import { createEventDispatcher } from 'svelte';
  import { marked } from 'marked';

  export let value = '';
  export let placeholder = 'Markdown formatında içeriğinizi yazın...';
  export let label = 'İçerik';

  const dispatch = createEventDispatcher();

  $: previewHtml = value ? marked.parse(value) : '';

  function handleInput(event) {
    value = event.currentTarget.value;
    dispatch('input', value);
  }

  async function handleFileUpload(event) {
    const file = event.currentTarget.files?.[0];
    if (!file) return;

    try {
      const text = await file.text();
      value = text;
      dispatch('input', value);
    } finally {
      event.currentTarget.value = '';
    }
  }
</script>

<div class="space-y-3">
  <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <label class="font-medium text-sm text-gray-700">{label} (Markdown)</label>
    <label class="text-xs text-blue-600 font-semibold cursor-pointer flex items-center gap-2 hover:underline">
      <input
        type="file"
        accept=".md,.markdown,text/markdown"
        class="hidden"
        on:change={handleFileUpload}
      />
      .md dosyası yükle
    </label>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div class="flex flex-col gap-2">
      <textarea
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 min-h-[240px]"
        bind:value={value}
        placeholder={placeholder}
        on:input={handleInput}
      ></textarea>
      <p class="text-xs text-gray-500">
        * Başlıklar için <code class="bg-gray-100 px-1 rounded">#</code>, kalın için
        <code class="bg-gray-100 px-1 rounded">**metin**</code>, liste için
        <code class="bg-gray-100 px-1 rounded">- madde</code> kullanabilirsiniz.
      </p>
    </div>

    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 min-h-[240px] overflow-auto">
      <div class="text-xs uppercase tracking-wide text-gray-500 mb-2 font-semibold">Önizleme</div>
      {#if previewHtml}
        <div class="prose prose-sm md:prose">
          {@html previewHtml}
        </div>
      {:else}
        <p class="text-gray-400 text-sm">İçeriğiniz burada önizlenecek.</p>
      {/if}
    </div>
  </div>
</div>

<style>
  .prose :global(p) {
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
  }

  .prose :global(h1),
  .prose :global(h2),
  .prose :global(h3) {
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
  }
  .prose :global(ul) {
    margin-left: 1.25rem;
    list-style: disc;
  }
  .prose :global(ol) {
    margin-left: 1.25rem;
    list-style: decimal;
  }
</style>
