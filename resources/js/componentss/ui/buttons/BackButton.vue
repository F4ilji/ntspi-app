<template>
  <Link :href="resolvedLink" class="inline-flex items-center gap-x-1.5 text-sm text-gray-600 decoration-2 hover:underline dark:text-blue-500">
    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
    {{ title }}
  </Link>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
  name: "BackButton",
  components: { Link },
  props: {
    title: {
      type: String,
      required: true,
    },
    link: {
      type: String,
      required: true,
    }
  },
  computed: {
    resolvedLink() {
      // Проверяем, является ли "link" полным URL
      try {
        new URL(this.link);
        return this.link; // Если это валидный URL, возвращаем его
      } catch (e) {
        // Если это не валидный URL, предполагаем, что это название роута
        // Используем this.route() который доступен через ZiggyVue mixin
        if (typeof this.route === 'function') {
          try {
            return this.route(this.link);
          } catch (err) {
            console.error('Route function failed:', err);
            return '#';
          }
        }
        
        // Fallback: возвращаем ссылку как есть или хэш
        console.warn('route() function is not available');
        return '#';
      }
    }
  }
};
</script>

<style scoped>
/* Ваши стили */
</style>