<template>
  <form>
    <div class="relative z-10 space-x-3 px-3 py-2 bg-white border rounded-lg shadow-lg shadow-gray-100">
      <div class="flex justify-between">
        <div class="flex w-full">
          <label for="hs-search-article-1"
                 class="block text-sm text-gray-700 font-medium dark:text-white">
            <span class="sr-only">Поиск</span>
          </label>
          <input
              @keydown.enter.prevent
              autocomplete="off"
              v-model="searchInput"
              @input="search"
              type="search"
              id="hs-search-article-1"
              class="py-2.5 px-4 block w-full border-transparent rounded-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-100"
              placeholder="Поиск"
          >
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {debounce} from "lodash";

export default {
  name: "SearchFilter",
  components: {
    BasicIcon,
    Link,
  },
  props: {
    search_filter: {
      type: Object,
      default: () => ({ value: null }),
    },
  },
  data() {
    return {
      searchInput: this.search_filter.value,
    }
  },
  methods: {
    search: debounce(function () {
      if(this.searchInput === "") {
        let url = new URL(window.location.href);
        url.searchParams.delete('search');
        let newUrl = url.toString();
        this.$inertia.visit(newUrl,{
          method: 'get',
          preserveState: true,
          replace: true,
        });
      } else {
        let url = new URL(window.location.href);
        url.searchParams.delete('page');
        let newUrl = url.toString();
        this.$inertia.visit(newUrl,{
          method: 'get',
          data: {
            search: this.searchInput,
          },
          preserveState: true,
          replace: true,
        })
      }
    }, 500),
  },
};
</script>

<style scoped>

</style>