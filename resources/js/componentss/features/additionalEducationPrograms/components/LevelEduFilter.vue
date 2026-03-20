<template>
	<select @change="handleChange" v-model="directionEdu" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
		<option selected value="">Все</option>
		<option v-for="direction in directions.data" :value="direction.slug">{{ direction.title }}</option>
	</select>
</template>
<script>
import {Link} from "@inertiajs/vue3";

import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {debounce} from "lodash";

export default {
	name: "LevelEduFilter",
	components: {
		BasicIcon,
		Link,
	},
	data() {
		return {
			directionEdu: this.direction_filter?.value || '',
			}
	},
	methods: {
		handleChange() {
			if (this.directionEdu === "") {
				this.clearFilter();
			} else {
				this.filter();
			}
		},
		filter: debounce(function () {
			let url = new URL(window.location.href);
			url.searchParams.delete('page');
			url.searchParams.delete('direction');
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
				data: {
					direction: this.directionEdu,
				},
			});
		}, 200),
		clearFilter() {
			let url = new URL(window.location.href);
			url.searchParams.delete('direction');
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
			});
		},
	},
	props: {
		directions: {
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