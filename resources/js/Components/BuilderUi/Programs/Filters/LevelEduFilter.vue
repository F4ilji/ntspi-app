<template>
	<select @change="filter" v-model="levelEdu" class="py-3 px-4 pe-9 block w-full md:w-1/2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
		<option @click.prevent="clearFilter" selected value="">Все</option>
		<option v-for="(level, key) in levels" :value="key">{{ level }}</option>
	</select>
</template>
<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";
import SearchBadge from "@/Components/BuilderUi/Events/Badges/SearchBadge.vue";
import CategoryBadge from "@/Components/BuilderUi/Events/Badges/CategoryBadge.vue";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import {debounce} from "lodash/function.js";
export default {
	name: "LevelEduFilter",
	components: {
		BaseIcon,
		Link,
		SearchBadge,
		CategoryBadge
	},
	data() {
		return {
			levelEdu: this.level_filter.value || '',
			}
	},
	methods: {
		filter: debounce(function () {
			let url = new URL(window.location.href);
			url.searchParams.delete('page');
			url.searchParams.delete('level');
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
				data: {
					level: this.levelEdu,
				},
			});
		}, 500),
		clearFilter() {
			let url = new URL(window.location.href);
			url.searchParams.delete('level');
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
			});
		},
	},
	props: {
		levels: {
			type: Object,
		},
		level_filter: {
			type: Object,
		},
	},
}

</script>


<style scoped>

</style>