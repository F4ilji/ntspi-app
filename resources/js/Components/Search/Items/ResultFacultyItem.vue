<template>
	<a v-if="item.tag === 'Faculty'" data-hs-overlay="#hs-basic-modal" :href="route('client.post.show', item.data.slug)" class="my-1 flex gap-3 px-2 py-2 items-center hover:bg-[#F2F2F2] rounded-lg">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
			<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
		</svg>
		<span class="text-sm">{{ item.data.title }}</span>
	</a>
<!--	<div v-if="item.matches.length !== 0" class="">-->
<!--		<template v-for="match in item.matches.slice(0,3)">-->
<!--			<div class="flex gap-3 px-2 items-center hover:bg-[#F2F2F2] rounded-lg">-->
<!--				<div class="ml-[8px] flex h-12 w-full items-center gap-2 overflow-hidden border-l border-gray-200 px-4 pl-[22px] text-sm text-gray-900 md:h-10">-->
<!--					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">-->
<!--						<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12H12m-8.25 5.25h16.5" />-->
<!--					</svg>-->
<!--					<p class="truncate">{{ match }}</p>-->
<!--				</div>-->
<!--			</div>-->
<!--		</template>-->
<!--	</div>-->
</template>

<script>
import {Link} from "@inertiajs/vue3";
import PreSearchLinkList from "@/Components/Search/PreSearchLinkList.vue";

export default {
	name: "ResultFacultyItem",
	components: {PreSearchLinkList, Link},
	data() {
		return {
			selectedCategory: 'All',
		}
	},
	methods: {

	},
	props: {
		item: {
			type: Object,
		}
	},
}
</script>

<style scoped>

</style>