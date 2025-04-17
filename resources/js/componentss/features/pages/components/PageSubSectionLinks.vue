<template>

	<div class="sticky top-[100px] hidden h-[calc(100vh-121px)] max-w-[20%] min-w-[20%] md:flex md:shrink-0 md:flex-col md:justify-between">
		<nav v-if="subSectionPages"
				 class="flex h-[calc(100vh-200px)] flex-col overflow-hidden pr-2 pb-4">
			<div class="text-gray-1000 mb-2 text-md font-medium">{{ currentSection }}</div>
			<div>
				<ul class="px-0.5 last-of-type:mb-0 mb-8">
					<li v-for="page in subSectionPages.data" :key="page.id" class="my-1.5 flex">
						<a :class="{'text-white font-normal bg-primary border': isSameRoute(page.path), 'text-gray-600 hover:text-[#2C6288]': !isSameRoute(page.path) }"
							 :href="(page.is_url) ? page.path : route('page.view', page.path) + '/'"
							 class="duration-300 flex gap-x-2 w-full rounded-md cursor-pointer items-center px-2 py-1.5 text-left text-sm">
							<BasicIcon v-if="page.icon" class="w-4" :name="page.icon" />
									{{ page.title }}
						</a>
					</li>
				</ul>
			</div>

		</nav>
	</div>



</template>

<script>
import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
export default {
	name: "PageSubSectionLinks",
	components: {Link, BasicIcon},
	data() {
		return {
			currentNavSection: null,
			scrollTop: false,
		}
	},
	methods: {
		isSameRoute(route) {
			if (this.$page.props.ziggy.location === this.$page.props.ziggy.url + '/' + route) {
				return true
			}
		},
	},
	mounted() {
	},



	props: {
		subSectionPages: {
			type: Object,
		},
		currentSection: {
			type: String,
		},
	},
}

</script>


<style scoped>



</style>