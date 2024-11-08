<template>
	<div class="min-w-[12rem] py-1 space-y-3">
		<input type="text" v-model="searchTerm" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Поиск по категориям">
		<button @click.prevent="clearFilter" class="text-gray-500 text-sm flex gap-x-1 items-center hover:text-gray-700">
			<BaseIcon class="w-4 h-4" name="delete" />
			<span>Очистить</span>
		</button>

		<div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[30vh] overflow-y-auto">
			<div>
				<div class="flex flex-col ml-3">
					<div v-for="item in filteredItems" :key="item.id">
						<input v-model="category_slug" :value="item.slug" @change="filter"
									 type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
									 :id="'inp' + item.id">
						<label :for="'inp' + item.id" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">{{ item.title }}</label>
					</div>
				</div>
			</div>
		</div>
	</div>

</template>
<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";
import SearchBadge from "@/Components/BuilderUi/Events/Badges/SearchBadge.vue";
import CategoryBadge from "@/Components/BuilderUi/Events/Badges/CategoryBadge.vue";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import {debounce} from "lodash";
export default {
	name: "CategoryFilter",
	components: {
		BaseIcon,
		Link,
		SearchBadge,
		CategoryBadge
	},
	data() {
		return {
			category_slug: this.category_filter.value || [],
			tag_slug: [],
			searchTerm: '',
			filteredItems: this.categories.data, // Изначально все элементы
			}
	},
	methods: {
		filter: debounce(function () {
			let url = new URL(window.location.href);
			// Создаем массив для хранения всех ключей, которые нужно удалить
			const keysToDelete = [];

			// Перебираем все параметры и добавляем ключи, начинающиеся с 'category', в массив
			for (const [key] of url.searchParams) {
				if (key.startsWith('category')) {
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
					category: this.category_slug,
				},
			});
		}, 500),
		clearFilter() {
			let url = new URL(window.location.href);
			// Создаем массив для хранения всех ключей, которые нужно удалить
			const keysToDelete = [];

			// Перебираем все параметры и добавляем ключи, начинающиеся с 'category', в массив
			for (const [key] of url.searchParams) {
				if (key.startsWith('category')) {
					keysToDelete.push(key);
				}
			}
			// Удаляем все ключи из массива
			keysToDelete.forEach(key => url.searchParams.delete(key));
			this.category_slug = [];
			this.searchTerm = ''; // Сбросить поле поиска
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
				return this.categories.data; // Возвращаем все элементы, если ничего не введено
			}
			const term = this.searchTerm.toLowerCase(); // Приводим к нижнему регистру для нечувствительности к регистру
			return this.categories.data.filter(item =>
					item.title.toLowerCase().includes(term) // Фильтруем по заголовку
			);
		}
	},
	props: {
		categories: {
			type: Object,
		},
		category_filter: {
			type: Object,
		},
	},
}

</script>


<style scoped>

</style>