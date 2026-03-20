<template>

	<div class="">
		<!--Фильтр-->

		<button type="button" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary text-white hover:bg-primary-light focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-offcanvas-example" data-hs-overlay="#hs-offcanvas-example">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
				<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
			</svg>

		</button>

		<!--Конец Фильтра-->
	</div>
	<div id="hs-offcanvas-example" class="hs-overlay hs-overlay-open:translate-x-0 hidden -translate-x-full fixed top-0 start-0 transition-all duration-300 transform h-full max-w-xs w-full z-[80] bg-white border-e overflow-auto" role="dialog" tabindex="-1" aria-labelledby="hs-offcanvas-example-label">
		<div class="flex justify-between items-center py-3 px-4 border-b">
			<h3 id="hs-offcanvas-example-label" class="font-bold text-gray-800">
				Фильтры
			</h3>
			<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none" aria-label="Close" data-hs-overlay="#hs-offcanvas-example">
				<span class="sr-only">Close</span>
				<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M18 6 6 18"></path>
					<path d="m6 6 12 12"></path>
				</svg>
			</button>
		</div>
		<div class="p-4">
			<IsOnlineFilter :is_online_filter="is_online_filter" />
			<div class="hs-accordion-group">
				<div class="hs-accordion" :id="'id' + category_filter.type">
					<button class="hs-accordion-toggle hs-accordion-active:text-primary py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none" aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-two">
						<svg class="hs-accordion-active:hidden block size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="m6 9 6 6 6-6"></path>
						</svg>
						<svg class="hs-accordion-active:block hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="m18 15-6-6-6 6"></path>
						</svg>
						Категории
						<span v-if="category_filter.value" class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-md text-xs font-medium border border-gray-200 bg-white text-gray-800 shadow-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white">{{ category_filter.value.length }}</span>

					</button>
					<div id="hs-basic-with-arrow-collapse-two" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" :aria-labelledby="'id' + category_filter.type">
						<CategoryFilter :categories="categories" :category_filter="category_filter" />
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import CategoryFilter from "@/componentss/shared/filter/filters/CategoryFilter.vue";
import IsOnlineFilter from "@/componentss/shared/filter/filters/IsOnlineFilter.vue";
import SortingByFilter from "@/componentss/shared/filter/filters/SortingByFilter.vue";
import TagFilter from "@/componentss/shared/filter/filters/TagFilter.vue";

export default {
	name: "EventListFilter",
	components: {IsOnlineFilter, SortingByFilter, TagFilter, CategoryFilter, BasicIcon},
	data() {
		return {
		}
	},

	props: {
		categories: {
			type: Object,
		},
		category_filter: {
			type: Object,
		},
		sortingBy_filter: {
			type: Object,
		},
		is_online_filter: {
			type: Object,
		}
	},
}
</script>

<style scoped>
</style>