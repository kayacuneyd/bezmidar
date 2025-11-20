<script>
  export let variant = 'primary'; // primary, secondary, tertiary
  export let size = 'md'; // sm, md, lg
  export let href = null;
  export let type = 'button';
  export let disabled = false;
  export let fullWidth = false;

  const baseClasses = 'inline-flex items-center justify-center font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
  
  const variantClasses = {
    primary: 'bg-primary text-white hover:bg-primary-700 focus:ring-primary-300 shadow-sm hover:shadow-md',
    secondary: 'bg-white text-primary border-2 border-primary hover:bg-primary-50 focus:ring-primary-300',
    tertiary: 'text-primary hover:text-primary-700 hover:underline'
  };
  
  const sizeClasses = {
    sm: 'px-4 py-2 text-sm rounded-lg',
    md: 'px-6 py-3 text-base rounded-xl',
    lg: 'px-8 py-4 text-lg rounded-xl'
  };

  $: classes = `${baseClasses} ${variantClasses[variant]} ${sizeClasses[size]} ${fullWidth ? 'w-full' : ''}`;
</script>

{#if href}
  <a {href} class={classes} class:opacity-50={disabled}>
    <slot />
  </a>
{:else}
  <button {type} {disabled} class={classes} on:click>
    <slot />
  </button>
{/if}
