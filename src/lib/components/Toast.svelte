<script>
  import { toast } from '$lib/stores/toast.js';
  import { fly } from 'svelte/transition';
</script>

<div class="fixed top-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none">
  {#each $toast as t (t.id)}
    <div 
      in:fly={{ x: 20, duration: 300 }} 
      out:fly={{ x: 20, duration: 300 }}
      class="pointer-events-auto min-w-[300px] max-w-md p-4 rounded-xl shadow-lg border flex items-start gap-3 bg-white
        {t.type === 'success' ? 'border-green-100 shadow-green-50' : ''}
        {t.type === 'error' ? 'border-red-100 shadow-red-50' : ''}
        {t.type === 'info' ? 'border-blue-100 shadow-blue-50' : ''}
      "
    >
      <div class="text-xl">
        {#if t.type === 'success'}✅{/if}
        {#if t.type === 'error'}❌{/if}
        {#if t.type === 'info'}ℹ️{/if}
      </div>
      <div class="flex-1">
        <p class="font-medium text-gray-900">{t.message}</p>
      </div>
      <button 
        on:click={() => toast.remove(t.id)}
        class="text-gray-400 hover:text-gray-600 transition"
      >
        ✕
      </button>
    </div>
  {/each}
</div>
