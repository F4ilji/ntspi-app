<template>
	<div class="min-w-[12rem] py-1 space-y-3">
		<h3 class="font-medium text-gray-900 text-sm mb-2">Сортировать по</h3>
		<div>
			<select @change="filter" v-model="sortOrder" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
				<option value="desc">Дате (Сначала новые)</option>
				<option value="asc">Дате (Сначала старые)</option>
			</select>
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
	name: "SortingByFilter",
	components: {
		BasicIcon,
		Link,
		SearchBadge,
		CategoryBadge
	},
	data() {
		return {
			sortOrder: this.sortingBy_filter.value || 'desc',
			}
	},
	methods: {
		filter: debounce(function () {
			let url = new URL(window.location.href);
			url.searchParams.delete('page');
			url.searchParams.delete('sort');
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
				data: {
					sort: this.sortOrder,
				},
			});
		}, 500),
		clearFilter() {
			let url = new URL(window.location.href);
			url.searchParams.delete('sort');
			this.tag_slug = [];
			this.searchTerm = ''; // Сбросить поле поиска
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
			});
		},
	},
	props: {
		sortingBy_filter: {
			type: Object,
		},
	},
}

</script>


<style scoped>

</style>