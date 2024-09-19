<template>
	<div class="min-w-[12rem] py-1 space-y-3">
		<button @click.prevent="clearFilter" class="text-gray-500 text-sm flex gap-x-1 items-center hover:text-gray-700">
			<BaseIcon class="w-4 h-4" name="delete" />
			<span>Очистить</span>
		</button>

		<div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[30vh] overflow-y-auto">
			<div>
				<div class="flex flex-col ml-3">
					<div v-for="(form, index) in forms">
						<input v-model="formEdu" :value="index" @change="filter"
									 type="radio" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
									 :id="'inp-tag' + index">
						<label :for="'inp-tag' + index" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">{{ form }}</label>
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
	name: "FormEducationalFilter",
	components: {
		BaseIcon,
		Link,
		SearchBadge,
		CategoryBadge
	},
	data() {
		return {
			formEdu: this.formEdu_filter.value || [],
			}
	},
	methods: {
		filter: debounce(function () {
			let url = new URL(window.location.href);
			url.searchParams.delete('page');
			url.searchParams.delete('form');
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
				data: {
					form: this.formEdu,
				},
			});
		}, 500),
		clearFilter() {
			let url = new URL(window.location.href);
			url.searchParams.delete('form');
			this.formEdu = [];
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
		forms: {
			type: Object,
		},
		formEdu_filter: {
			type: Object,
		},
	},
}

</script>


<style scoped>

</style>