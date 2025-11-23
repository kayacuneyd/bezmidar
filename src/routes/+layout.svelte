<script>
  import '../app.css';
  import Navbar from '$lib/components/Navbar.svelte';
  import Footer from '$lib/components/Footer.svelte';
  import Toast from '$lib/components/Toast.svelte';
  import { onMount } from 'svelte';
  import { authStore } from '$lib/stores/auth.js';
  import { injectAnalytics } from '@vercel/analytics/sveltekit';

  injectAnalytics();
  
  onMount(() => authStore.checkAuth());
</script>

<div class="min-h-screen flex flex-col">
  <Navbar />
  <Toast />
  <main class="flex-1">
    <slot />
  </main>
  <Footer />
</div>
