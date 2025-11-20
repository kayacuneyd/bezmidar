<script>
  export let teacher;
  
  let subjects = [];
  
  $: {
    if (Array.isArray(teacher.subjects)) {
      subjects = teacher.subjects.map(s => typeof s === 'object' ? s.name : s);
    } else if (typeof teacher.subjects === 'string') {
      subjects = teacher.subjects.split(',');
    } else {
      subjects = [];
    }
  }
</script>

<a 
  href="/profil/{teacher.id}" 
  class="block bg-white rounded-card shadow-card hover:shadow-card-hover transition-all border border-gray-100 p-6 group"
>
  <div class="flex gap-4">
    <!-- Avatar -->
    <div class="flex-shrink-0">
      {#if teacher.avatar_url}
        <img 
          src={teacher.avatar_url} 
          alt={teacher.full_name}
          class="w-20 h-20 rounded-full object-cover ring-2 ring-gray-100"
        />
      {:else}
        <div class="w-20 h-20 rounded-full bg-primary-100 flex items-center justify-center text-2xl font-bold text-primary-600">
          {teacher.full_name.charAt(0)}
        </div>
      {/if}
    </div>
    
    <!-- Info -->
    <div class="flex-1 min-w-0">
      <div class="flex items-start justify-between">
        <div>
          <h3 class="text-card flex items-center gap-2 text-gray-900 group-hover:text-primary-600 transition">
            {teacher.full_name}
            {#if teacher.is_verified}
              <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            {/if}
          </h3>
          <p class="text-sm text-gray-600 font-medium mt-1">{teacher.university}</p>
          <p class="text-sm text-gray-500">{teacher.department}</p>
        </div>
        <div class="text-right">
          <p class="text-2xl font-bold text-primary-600">€{teacher.hourly_rate}</p>
          <p class="text-xs text-gray-500">/saat</p>
        </div>
      </div>
      
      <!-- Rating -->
      {#if teacher.review_count > 0}
        <div class="flex items-center gap-1 mt-3">
          <span class="text-secondary-500">⭐</span>
          <span class="text-sm font-semibold text-gray-900">{teacher.rating_avg}</span>
          <span class="text-sm text-gray-500">({teacher.review_count} yorum)</span>
        </div>
      {/if}
      
      <!-- Subjects -->
      {#if subjects.length > 0}
        <div class="flex flex-wrap gap-2 mt-3">
          {#each subjects.slice(0, 3) as subject}
            <span class="px-3 py-1 bg-primary-50 text-primary-700 text-xs font-medium rounded-full">
              {subject}
            </span>
          {/each}
          {#if subjects.length > 3}
            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
              +{subjects.length - 3}
            </span>
          {/if}
        </div>
      {/if}
      
      <!-- Location -->
      {#if teacher.city}
        <div class="flex items-center gap-1 mt-3 text-sm text-gray-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <span>{teacher.city}{teacher.zip_code ? `, ${teacher.zip_code}` : ''}</span>
        </div>
      {/if}
    </div>
  </div>
</a>
