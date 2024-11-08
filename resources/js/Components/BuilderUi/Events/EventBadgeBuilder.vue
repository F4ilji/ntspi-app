<template>

	<component
			v-for="(filter, index) in filters"
			:key="index"
			:is="getComponent(filter.type)"
			:filter="filter"
	/>
</template>
<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";
import SearchBadge from "@/Components/BuilderUi/Events/Badges/SearchBadge.vue";
import CategoryBadge from "@/Components/BuilderUi/Events/Badges/CategoryBadge.vue";
import TagBadge from "@/Components/BuilderUi/Events/Badges/TagBadge.vue";
export default {
	name: "EventBadgeBuilder",
	components: {
		Link,
		SearchBadge,
		CategoryBadge,
		TagBadge,
	},
	data() {
		return {
		}
	},
	methods: {
		getComponent(type) {
			const componentMap = {
				search: 'SearchBadge',
				category: 'CategoryBadge',
				tag: 'TagBadge',
			};
			return componentMap[type] || null;
		},
	},
	props: {
		filters: {
			type: Object,
		},
	},
}

</script>


<style scoped>

</style>