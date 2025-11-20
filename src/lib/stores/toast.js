import { writable } from 'svelte/store';

function createToastStore() {
  const { subscribe, update } = writable([]);

  return {
    subscribe,
    success: (message, timeout = 3000) => addToast(message, 'success', timeout),
    error: (message, timeout = 3000) => addToast(message, 'error', timeout),
    info: (message, timeout = 3000) => addToast(message, 'info', timeout),
    remove: (id) => update(toasts => toasts.filter(t => t.id !== id))
  };

  function addToast(message, type, timeout) {
    const id = Math.floor(Math.random() * 10000);
    update(toasts => [...toasts, { id, message, type }]);
    
    if (timeout) {
      setTimeout(() => {
        update(toasts => toasts.filter(t => t.id !== id));
      }, timeout);
    }
  }
}

export const toast = createToastStore();
