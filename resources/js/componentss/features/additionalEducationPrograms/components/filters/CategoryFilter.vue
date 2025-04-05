<template>
	<div class="min-w-[12rem] py-1 space-y-3">
		<button @click.prevent="clearFilter" class="text-gray-500 text-sm flex gap-x-1 items-center hover:text-gray-700">
			<BasicIcon class="w-4 h-4" name="delete" />
			<span>Очистить</span>
		</button>

		<div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[30vh] overflow-y-auto">
			<div>
				<div class="flex flex-col ml-3">
					<div v-for="item in categories" :key="item.id">
						<input v-model="category" :value="item.slug" @change="filter"
									 type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-primary focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
									 :id="'inp' + item.id">
						<label :for="'inp' + item.id" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">{{ item.name }}</label>
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
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {debounce} from "lodash";
export default {
	name: "CategoryFilter",
	components: {
		BasicIcon,
		Link,
		SearchBadge,
		CategoryBadge
	},
	data() {
		return {
			category: this.category_filter.value || [],
			searchTerm: '',
			}
	},
	methods: {
		filter: debounce(function () {
			let url = new URL(window.location.href);
			url.searchParams.delete('page');
			url.searchParams.delete('category[]');
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
				data: {
					category: this.category,
				},
			});
		}, 500),
		clearFilter() {
			let url = new URL(window.location.href);
			url.searchParams.delete('category[]');
			this.category = [];
			this.searchTerm = ''; // Сбросить поле поиска
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