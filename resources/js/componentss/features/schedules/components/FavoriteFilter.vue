<template>
  <button
      @click="toggleShowFavorites"
      type="button"
      :disabled="isLoading" :class="
     favorite_filter.value !== null ? 'bg-primary text-white hover:bg-primary-dark': 'text-gray-700 hover:bg-gray-100',
     isLoading ? 'animate-pulse' : '' "
      class="flex w-full py-2 px-4 items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
  >
    <BasicIcon name="heart" class="shrink-0 size-4"/>
    <span>Избранное</span>
  </button>

</template>
<script>
import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {debounce} from "lodash";

export default {
  name: "FavoriteFilter",
  components: {
    BasicIcon,
    Link,
  },
  props: {
    favorite_filter: {
      type: Object,
    },
    favoriteGroups: {
      type: Object
    }
  },
  data() {
    return {
      // Инициализируйте локальное свойство данных для управления состоянием загрузки
      isLoading: false, // Вы также можете инициализировать его на основе пропса, если это необходимо: this.initialLoadingProp || false
    };
  },
  methods: {
    toggleShowFavorites() {
      this.isLoading = true; // Теперь вы изменяете свое локальное свойство данных

      if (this.favorite_filter.value === null) {
        this.filterFavorite(() => {
          this.isLoading = false; // Отключаем состояние загрузки после завершения
        });
      } else {
        this.clearFilterFavorite(() => {
          this.isLoading = false; // Отключаем состояние загрузки после завершения
        });
      }
    },

    filterFavorite: debounce(function (callback) {
      let url = new URL(window.location.href);
      const keysToDelete = [];

      for (const [key] of url.searchParams) {
        if (key.startsWith('favorite')) {
          keysToDelete.push(key);
        }
      }
      keysToDelete.forEach(key => url.searchParams.delete(key));
      let newUrl = url.toString();
      this.$inertia.visit(newUrl, {
        method: 'get',
        preserveState: true,
        data: {
          favorite: (this.favoriteGroups?.length !== 0) ? this.favoriteGroups : "",
        },
        onFinish: callback,
      });
    }, 500),
    clearFilterFavorite(callback) {
      let url = new URL(window.location.href);
      const keysToDelete = [];

      for (const [key] of url.searchParams) {
        if (key.startsWith('favorite')) {
          keysToDelete.push(key);
        }
      }
      keysToDelete.forEach(key => url.searchParams.delete(key));
      let newUrl = url.toString();
      this.$inertia.visit(newUrl, {
        method: 'get',
        preserveState: true,
        onFinish: callback,
      });
    },
  },
}

</script>

<style scoped>

</style>