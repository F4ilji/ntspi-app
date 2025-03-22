<template>
  <div class="hs-accordion" :id="'id' + direction_filter.type">
    <button class="hs-accordion-toggle hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none" aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-two">
      <svg class="hs-accordion-active:hidden block size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m6 9 6 6 6-6"></path>
      </svg>
      <svg class="hs-accordion-active:block hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m18 15-6-6-6 6"></path>
      </svg>
      Направление подготовки
    </button>
    <div id="hs-basic-with-arrow-collapse-two" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" :aria-labelledby="'id' + direction_filter.type">
      <div class="min-w-[12rem] py-1 space-y-3">
        <button @click.prevent="clearFilter" class="text-gray-500 text-sm flex gap-x-1 items-center hover:text-gray-700">
          <BasicIcon class="w-4 h-4" name="delete" />
          <span>Очистить</span>
        </button>

        <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[30vh] overflow-y-auto">
          <div>
            <div class="flex flex-col ml-3">
              <div v-for="item in direction_studies" :key="item.id">
                <input v-model="direction" :value="item.slug" @change="filter"
                       type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                       :id="'inp' + item.id">
                <label :for="'inp' + item.id" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">{{ item.name }}</label>
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
	name: "DirectionFilter",
	components: {
		BasicIcon,
		Link
	},
	data() {
		return {
			direction: this.direction_filter.value || [],
			tag_slug: [],
			searchTerm: '',
			}
	},
	methods: {
		filter: debounce(function () {
			let url = new URL(window.location.href);
			// Создаем массив для хранения всех ключей, которые нужно удалить
			const keysToDelete = [];

			// Перебираем все параметры и добавляем ключи, начинающиеся с 'category', в массив
			for (const [key] of url.searchParams) {
				if (key.startsWith('direction')) {
					keysToDelete.push(key);
				}
			}
			// Удаляем все ключи из массива
			keysToDelete.forEach(key => url.searchParams.delete(key));
			url.searchParams.delete('page');
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
				data: {
					direction: this.direction,
				},
			});
		}, 500),
		clearFilter() {
			let url = new URL(window.location.href);
			// Создаем массив для хранения всех ключей, которые нужно удалить
			const keysToDelete = [];

			// Перебираем все параметры и добавляем ключи, начинающиеся с 'category', в массив
			for (const [key] of url.searchParams) {
				if (key.startsWith('direction')) {
					keysToDelete.push(key);
				}
			}
			// Удаляем все ключи из массива
			keysToDelete.forEach(key => url.searchParams.delete(key));
			this.direction = [];
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
			});
		},
	},
	computed: {
	},
	props: {
		direction_studies: {
			type: Object,
		},
		direction_filter: {
			type: Object,
		},
	},
}

</script>


<style scoped>

</style>