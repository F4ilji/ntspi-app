<template>
	<PreSearchLinkList v-if="result === null" class="px-2 font-light" />
	<div v-else-if="result.length !== 0"
			 :class="{'filter pointer-events-none': loading === true}"
			 class="px-2 font-light duration-300">
		<template v-for="(elements, tag) in result">
			<component
          class="rounded-none"
					v-for="(item, index) in elements"
					:key="index"
					:is="getComponent(tag)"
					:item="item"
					v-if="selectedCategory === null || selectedCategory === tag"
			/>
		</template>
	</div>
  <div class="" v-else-if="result.length === 0">
    <div class="h-full flex flex-col items-center justify-center gap-1">
      <p class="font-light">Извините, ничего не найдено</p>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-700">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
      </svg>
    </div>
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
import BasicResultItem from "@/componentss/features/search/components/Items/BasicResultItem.vue";


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
		Link,
    BasicResultItem
  },
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