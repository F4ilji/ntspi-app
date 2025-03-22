<template>
  <div class="mt-10 flex items-center justify-center">
    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
      <Link as="button" :href="links.prev" :disabled="links.prev === null"
            class="relative inline-flex items-center gap-1 rounded-l-md border border-gray-300 bg-white px-3 py-2 pr-4 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 disabled:pointer-events-none disabled:opacity-40">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor" aria-hidden="true" data-slot="icon" class="h-3 w-3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
        </svg>
        <span>Предыдущая</span></Link>
      <Link as="button" :href="links.next" :disabled="links.next === null"
            class="relative inline-flex items-center gap-1 rounded-r-md border border-gray-300 bg-white px-3 py-2 pl-4 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 disabled:pointer-events-none disabled:opacity-40">
        <span>Следующая</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor" aria-hidden="true" data-slot="icon" class="h-3 w-3">
          <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
        </svg>
      </Link>
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
};
</script>

<style scoped>
/* Добавьте стили, если необходимо */
</style>