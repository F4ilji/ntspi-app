<template>
	<div class="min-w-[12rem] py-1 space-y-3">
		<input type="text" v-model="searchTerm" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Поиск по тэгам">
		<button @click.prevent="clearFilter" class="text-gray-500 text-sm flex gap-x-1 items-center hover:text-gray-700">
			<BaseIcon class="w-4 h-4" name="delete" />
			<span>Очистить</span>
		</button>

		<div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[30vh] overflow-y-auto">
			<div>
				<div class="flex flex-col ml-3">
					<div v-for="item in filteredItems" :key="item.id">
						<input v-model="tag_slug" :value="item.slug['ru']" @change="filter"
									 type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
									 :id="'inp-tag' + item.id">
						<label :for="'inp-tag' + item.id" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">#{{ item.name.ru }}</label>
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
import {debounce} from "lodash/function.js";
export default {
	name: "TagFilter",
	components: {
		BaseIcon,
		Link,
		SearchBadge,
		CategoryBadge
	},
	data() {
		return {
			tag_slug: this.tag_filter.value || [],
			searchTerm: '',
			filteredItems: this.tags, // Изначально все элементы
			}
	},
	methods: {
		filter: debounce(function () {
			let url = new URL(window.location.href);
			url.searchParams.delete('page');
			url.searchParams.delete('tag[]');
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
				data: {
					tag: this.tag_slug,
				},
			});
		}, 500),
		clearFilter() {
			let url = new URL(window.location.href);
			url.searchParams.delete('tag[]');
			this.tag_slug = [];
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
				return this.tags; // Возвращаем все элементы, если ничего не введено
			}
			const term = this.searchTerm.toLowerCase(); // Приводим к нижнему регистру для нечувствительности к регистру
			return this.tags.filter(item =>
					item.name.ru.toLowerCase().includes(term) // Фильтруем по заголовку
			);
		}
	},
	props: {
		tags: {
			type: Object,
		},
		tag_filter: {
			type: Object,
		},
	},
}

</script>


<style scoped>

</style>