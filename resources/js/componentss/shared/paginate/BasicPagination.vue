<template>
  <div class="mt-10 flex items-center justify-center">
    <nav class="flex items-center gap-x-1" aria-label="Pagination">
      <a :href="links.links[0].url" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-2 text-sm rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none" aria-label="Previous">
        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m15 18-6-6 6-6"></path>
        </svg>
        <span class="sr-only">Previous</span>
      </a>

      <div class="flex items-center gap-x-1">
        <!-- Десктопная версия - показываем все страницы -->
        <template v-for="link in getPageLinks(links)" :key="link.label">
          <a :href="link.url"
             :class="link.active ? 'border-gray-200 text-gray-800' : 'border-transparent hover:bg-gray-100'"
             class="hidden md:flex min-h-9.5 min-w-9.5 justify-center items-center border py-2 px-3 text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none">
            {{ link.label }}
          </a>
        </template>

        <!-- Мобильная версия - показываем только активную и соседние страницы -->
        <template v-for="link in getMobilePageLinks(links)" :key="'mobile-' + link.label">
          <a :href="link.url"
             :class="link.active ? 'border-gray-200 text-gray-800' : 'border-transparent hover:bg-gray-100'"
             class="md:hidden min-h-9.5 min-w-9.5 flex justify-center items-center border py-2 px-3 text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none">
            {{ link.label }}
          </a>
        </template>
      </div>

      <a :href="links.links.slice(-1).pop().url" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-2 text-sm rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none" aria-label="Next">
        <span class="sr-only">Next</span>
        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m9 18 6-6-6-6"></path>
        </svg>
      </a>
    </nav>
  </div>
</template>

<script>
import {Link} from "@inertiajs/vue3";

export default {
  components: {Link},
  props: {
    links: {
      type: Object,
      required: true,
    },
  },
  methods: {
    getPageLinks(pagination) {
      return pagination.links.filter(link =>
          link.label !== 'pagination.previous' &&
          link.label !== 'pagination.next'
      );
    },
    getMobilePageLinks(pagination) {
      const allLinks = this.getPageLinks(pagination);
      const activeIndex = allLinks.findIndex(link => link.active);



      // Возвращаем только активную страницу и соседние
      if (activeIndex === -1) return allLinks;

      const start = Math.max(0, activeIndex - 1);
      const end = Math.min(allLinks.length - 1, activeIndex + 1);

      return allLinks.slice(start, end + 1);
    }
  }
};
</script>

<style scoped>
/* Добавьте стили, если необходимо */
</style>