<template>
		<!--Фильтр-->


		<button type="button"
						aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-offcanvas-example" data-hs-overlay="#hs-offcanvas-example"
						class="flex w-full py-2 px-4 items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-primaryBlue focus:text-white disabled:opacity-50 disabled:pointer-events-none">
			<BaseIcon class="shrink-0 size-4" name="settings" />
			<span>Фильтры</span>
		</button>


		<!--Конец Фильтра-->
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
			<div class="hs-accordion-group">
				<div class="hs-accordion" :id="'id' + formEdu_filter.type">
					<button class="hs-accordion-toggle hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none" aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-two">
						<svg class="hs-accordion-active:hidden block size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="m6 9 6 6 6-6"></path>
						</svg>
						<svg class="hs-accordion-active:block hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="m18 15-6-6-6 6"></path>
						</svg>
						Формат обучения
					</button>
					<div id="hs-basic-with-arrow-collapse-two" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" :aria-labelledby="'id' + formEdu_filter.type">
						<FormEducationalFilter :forms="forms_educational" :formEdu_filter="formEdu_filter" />
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import CategoryFilter from "@/Components/BuilderUi/Events/Filters/CategoryFilter.vue";
import TagFilter from "@/Components/BuilderUi/Events/Filters/TagFilter.vue";
import SortingByFilter from "@/Components/BuilderUi/Events/Filters/SortingByFilter.vue";
import FormEducationalFilter from "@/Components/BuilderUi/Programs/Filters/FormEducationalFilter.vue";
import BudgetFilter from "@/Components/BuilderUi/Programs/Filters/BudgetFilter.vue";
import DirectionFilter from "@/Components/BuilderUi/Programs/Filters/DirectionFilter.vue";

export default {
	name: "ClientScheduleFilter",
	components: {
		DirectionFilter,
		BudgetFilter, FormEducationalFilter, SortingByFilter, TagFilter, CategoryFilter, BaseIcon},
	data() {
		return {
		}
	},

	props: {
		faculty_filter: {
			type: Object,
		},
		formEdu_filter: {
			type: Object
		},
		forms_educational: {
			type: Object
		}
	},
}
</script>

<style scoped>
</style>