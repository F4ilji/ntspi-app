<template>
	<div v-if="item.tag === 'EducationalGroup'" data-hs-overlay="#hs-basic-modal"
			 class="my-1 flex gap-3 px-2 py-2 items-center rounded-lg">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
				 class="w-5 h-5 flex-shrink-0">
			<path stroke-linecap="round" stroke-linejoin="round"
						d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/>
		</svg>
		<span class="text-sm">{{ item.data.title }}</span>
	</div>
	<template v-for="schedule in item.data.schedules">
		<div class="mb-2">
			<div class="flex gap-3 px-2 items-center hover:bg-[#F2F2F2] rounded-lg">
				<Link href="/"
							class="ml-[8px] flex h-12 w-full items-center gap-2 overflow-hidden border-l border-gray-200 px-4 pl-[22px] text-sm text-gray-900 md:h-10">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
							 stroke="currentColor" class="w-5 h-5 flex-shrink-0">
						<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12H12m-8.25 5.25h16.5"/>
					</svg>
					<p class="truncate">{{ schedule.title }}</p>
				</Link>
			</div>
		</div>
	</template>

</template>

<script>
import {Link} from "@inertiajs/vue3";

export default {
	name: "ResultEducationalGroupItem",
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