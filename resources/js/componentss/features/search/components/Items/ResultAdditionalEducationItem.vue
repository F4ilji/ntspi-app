<template>
	<Link v-if="item.tag === 'AdditionalEducation'" data-hs-overlay="#hs-basic-modal" :href="route('client.additionalEducation.show', item.data.id)" class="my-1 flex gap-3 px-2 py-2 items-center hover:bg-[#F2F2F2] rounded-lg">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
			<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z" />
		</svg>


		<span class="text-sm">{{ item.data.title }}</span>
	</Link>
	<div v-if="item.matches.length !== 0" class="">
		<template v-for="match in item.matches.slice(0,3)">
			<div class="flex gap-3 px-2 items-center hover:bg-[#F2F2F2] rounded-lg">
				<div class="ml-[8px] flex h-12 w-full items-center gap-2 overflow-hidden border-l border-gray-200 px-4 pl-[22px] text-sm text-gray-900 md:h-10">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
						<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12H12m-8.25 5.25h16.5" />
					</svg>
					<p class="truncate">{{ match }}</p>
				</div>
			</div>
		</template>
	</div>

</template>

<script>
import {Link} from "@inertiajs/vue3";

export default {
	name: "ResultAdditionalEducationItem",
	components: {Link},
	data() {
		return {
			selectedCategory: 'All',
		}
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