<template>
	<Link v-if="item.tag === 'Event'" data-hs-overlay="#hs-basic-modal" :href="route('client.event.show', item.data.slug)" class="my-1 flex gap-3 px-2 py-2 items-center hover:bg-[#F2F2F2] rounded-lg">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
			<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
		</svg>

    <div class="flex items-center gap-x-2">
      <span class="text-sm">{{ item.data.title }}</span>
      <span class="text-xs text-gray-500">Начало {{ item.data.event_date_start }} в {{ item.data.event_time_start }}</span>
    </div>	</Link>
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

export default {
	name: "ResultEventItem",
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