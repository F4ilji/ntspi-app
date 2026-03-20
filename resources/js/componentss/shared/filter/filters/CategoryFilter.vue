<template>

  <div class="hs-accordion" :id="'id' + category_filter.type">
    <button class="hs-accordion-toggle hs-accordion-active:text-primary py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none" aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-two">
      <svg class="hs-accordion-active:hidden block size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m6 9 6 6 6-6"></path>
      </svg>
      <svg class="hs-accordion-active:block hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m18 15-6-6-6 6"></path>
      </svg>
      Категории
    </button>
    <div id="hs-basic-with-arrow-collapse-two" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" :aria-labelledby="'id' + category_filter.type">
      <div class="min-w-[12rem] py-1 space-y-3">
        <input type="text" v-model="searchTerm" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Поиск по категориям">
        <button @click.prevent="clearFilter" class="text-gray-500 text-sm flex gap-x-1 items-center hover:text-gray-700">
          <BasicIcon class="w-4 h-4" name="delete" />
          <span>Очистить</span>
        </button>

        <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[30vh] overflow-y-auto">
          <div>
            <div class="flex flex-col ml-3">
              <div v-for="item in filteredItems" :key="item.id">
                <input v-model="category_slug" :value="item.slug" @change="filter"
                       type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-primary focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                       :id="'inp' + item.id">
                <label :for="'inp' + item.id" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">{{ item.title }}</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


</template>
<script>
import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {debounce} from "lodash";

export default {
  name: "CategoryFilter",
  components: {
    BasicIcon,
    Link
  },
  data() {
    return {
      category_slug: this.category_filter.value || [],
      searchTerm: '',
    }
  },
  methods: {
    filter: debounce(function () {
      let url = new URL(window.location.href);
      const keysToDelete = [];

      for (const [key] of url.searchParams) {
        if (key.startsWith('category')) {
          keysToDelete.push(key);
        }
      }

      keysToDelete.forEach(key => url.searchParams.delete(key));
      url.searchParams.delete('page');
      let newUrl = url.toString();

      this.$inertia.visit(newUrl, {
        method: 'get',
        preserveState: true,
        data: {
          category: this.category_slug,
        },
      });
    }, 500),

    clearFilter() {
      let url = new URL(window.location.href);
      const keysToDelete = [];

      for (const [key] of url.searchParams) {
        if (key.startsWith('category')) {
          keysToDelete.push(key);
        }
      }

      keysToDelete.forEach(key => url.searchParams.delete(key));
      this.category_slug = [];
      this.searchTerm = '';
      let newUrl = url.toString();

      this.$inertia.visit(newUrl, {
        method: 'get',
        preserveState: true,
      });
    },
  },
  computed: {
    filteredItems() {
      if (!this.searchTerm) {
        return this.categories.data;
      }
      const term = this.searchTerm.toLowerCase();
      return this.categories.data.filter(item =>
          item.title.toLowerCase().includes(term)
      );
    }
  },
  props: {
    categories: {
      type: Object,
      required: true // Добавляем required для ясности
    },
    category_filter: {
      type: Object,
      default: () => ({ value: [] }) // Добавляем значение по умолчанию
    },
  }
}
</script>

<style scoped>

</style>