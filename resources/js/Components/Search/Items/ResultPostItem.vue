<template>
	<a v-if="item.tag === 'Post'" data-hs-overlay="#hs-basic-modal" :href="route('client.post.show', item.data.slug)" class="my-1 flex gap-3 px-2 py-2 items-center hover:bg-[#F2F2F2] rounded-lg">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
			<path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
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
	name: "ResultPostItem",
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