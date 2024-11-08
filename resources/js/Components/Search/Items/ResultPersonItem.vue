<template>
	<a v-if="item.tag === 'User'" data-hs-overlay="#hs-basic-modal" :href="route('client.person.show', item.data.slug)" class="my-1 flex gap-3 px-2 py-2 items-center hover:bg-[#F2F2F2] rounded-lg">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
			<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
		</svg>
		<span class="text-sm">{{ item.data.name }}</span>
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
	name: "ResultPersonItem",
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