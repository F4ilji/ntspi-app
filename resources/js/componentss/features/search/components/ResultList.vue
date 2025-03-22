<template>
	<PreSearchLinkList v-if="result === null" class="px-2 font-light" />
	<div v-else-if="result.length !== 0"
			 :class="{'filter': loading === true}"
			 class="px-2 font-light duration-300">
		<template v-for="(elements, tag) in result">
			<component
					v-for="(item, index) in elements"
					:key="index"
					:is="getComponent(tag)"
					:item="item"
					v-if="selectedCategory === null || selectedCategory === tag"
			/>
		</template>
	</div>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import PreSearchLinkList from "@/componentss/features/search/components/PreSearchLinkList.vue";
import ResultPostItem from "@/componentss/features/search/components/Items/ResultPostItem.vue";
import ResultPageItem from "@/componentss/features/search/components/Items/ResultPagetItem.vue";
import ResultEducationalProgramItem
  from "@/componentss/features/search/components/Items/ResultEducationalProgramItem.vue";
import ResultEventItem from "@/componentss/features/search/components/Items/ResultEventItem.vue";
import ResultPersonItem from "@/componentss/features/search/components/Items/ResultPersonItem.vue";
import ResultFacultyItem from "@/componentss/features/search/components/Items/ResultFacultyItem.vue";
import ResultEducationalGroupItem from "@/componentss/features/search/components/Items/ResultEducationalGroupItem.vue";
import ResultAdditionalEducationItem
  from "@/componentss/features/search/components/Items/ResultAdditionalEducationItem.vue";


export default {
	name: "ResultList",
	components: {
		ResultEducationalGroupItem,
		ResultAdditionalEducationItem,
		ResultEventItem,
		ResultEducationalProgramItem,
		ResultPageItem,
		ResultPostItem,
		PreSearchLinkList,
		ResultPersonItem,
		ResultFacultyItem,
		Link},
	data() {
		return {
		}
	},
	methods: {
		getComponent(type) {
			const componentMap = {
				Post: 'ResultPostItem',
				Page: 'ResultPageItem',
				Event: 'ResultEventItem',
				EducationalProgram: 'ResultEducationalProgramItem',
				AdditionalEducational: 'ResultAdditionalEducationItem',
				EducationalGroup: 'ResultEducationalGroupItem',
				User: 'ResultPersonItem',
				Faculty: 'ResultFacultyItem'
			};
			return componentMap[type] || null;
		},
	},


	props: {
		result: {
			type: Object,
		},
		selectedCategory: {
			type: String,
		},
		loading: {
			type: Boolean,
		}
	},
}
</script>

<style scoped>

.filter {
	filter: saturate(100%) blur(3px);
}

</style>