<template>
	<div class="min-w-[12rem] py-1 space-y-3">
		<button @click.prevent="clearFilter" class="text-gray-500 text-sm flex gap-x-1 items-center hover:text-gray-700">
			<BaseIcon class="w-4 h-4" name="delete" />
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

</template>
<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";
import SearchBadge from "@/Components/BuilderUi/Events/Badges/SearchBadge.vue";
import CategoryBadge from "@/Components/BuilderUi/Events/Badges/CategoryBadge.vue";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import {debounce} from "lodash/function.js";
export default {
	name: "DirectionFilter",
	components: {
		BaseIcon,
		Link,
		SearchBadge,
		CategoryBadge
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
			url.searchParams.delete('page');
			url.searchParams.delete('direction[]');
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
			url.searchParams.delete('direction[]');
			this.direction = [];
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