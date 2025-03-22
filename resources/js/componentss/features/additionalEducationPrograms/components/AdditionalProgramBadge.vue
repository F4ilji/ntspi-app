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
import CategoryBadge from "@/componentss/features/additionalEducationPrograms/components/badges/CategoryBadge.vue";
import FormBadge from "@/componentss/features/additionalEducationPrograms/components/badges/FormBadge.vue";
export default {
	name: "AdditionalProgramBadge",
	components: {
		Link,
		CategoryBadge,
		FormBadge
	},
	data() {
		return {
		}
	},
	methods: {
		getComponent(type) {
			const componentMap = {
				category: 'CategoryBadge',
				form: 'FormBadge',
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