<script>
  import { onMount, onDestroy } from 'svelte';
  import { browser } from '$app/environment';
  import 'leaflet/dist/leaflet.css';

  import markerIcon from 'leaflet/dist/images/marker-icon.png';
  import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
  import markerShadow from 'leaflet/dist/images/marker-shadow.png';

  export let teachers = [];

  let mapElement;
  let map;
  let markers = [];

  onMount(async () => {
    if (browser) {
      const L = (await import('leaflet')).default;

      // Fix marker icons
      delete L.Icon.Default.prototype._getIconUrl;
      L.Icon.Default.mergeOptions({
          iconRetinaUrl: markerIcon2x,
          iconUrl: markerIcon,
          shadowUrl: markerShadow,
      });

      // Default center (Germany)
      map = L.map(mapElement).setView([51.1657, 10.4515], 6);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);

      updateMarkers(L);
    }
  });

  function updateMarkers(L) {
    // Clear existing markers
    markers.forEach(m => map.removeLayer(m));
    markers = [];

    if (!teachers || teachers.length === 0) return;

    const bounds = L.latLngBounds();

    teachers.forEach(teacher => {
      if (teacher.lat && teacher.lng) {
        const marker = L.marker([teacher.lat, teacher.lng])
          .addTo(map)
          .bindPopup(`
            <div class="text-center">
              <h3 class="font-bold">${teacher.full_name}</h3>
              <p class="text-sm text-gray-600">${teacher.subjects.map(s => typeof s === 'object' ? s.name : s).join(', ')}</p>
              <p class="font-bold text-blue-600">€${teacher.hourly_rate}/saat</p>
              <a href="/profil/${teacher.id}" class="text-xs text-blue-500 hover:underline">Profili Gör</a>
            </div>
          `);
        
        markers.push(marker);
        bounds.extend([teacher.lat, teacher.lng]);
      }
    });

    if (markers.length > 0) {
      map.fitBounds(bounds, { padding: [50, 50] });
    }
  }

  $: if (map && teachers) {
    // Dynamically import Leaflet again to access L for updates
    import('leaflet').then(module => {
      updateMarkers(module.default);
    });
  }

  onDestroy(() => {
    if (map) {
      map.remove();
    }
  });
</script>



<div bind:this={mapElement} class="w-full h-[600px] rounded-xl z-0"></div>
