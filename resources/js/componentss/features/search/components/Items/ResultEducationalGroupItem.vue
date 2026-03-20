<template>

  <div class="border-b w-full">
    <Link v-if="item.tag === 'EducationalGroup'" :href="route('client.schedule.index', { search: item.data.title })" class="rounded-lg hover:bg-[#F2F2F2] my-1 px-4 w-full flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
           class="w-5 h-5 flex-shrink-0 text-gray-500">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/>
      </svg>
      <div class="px-4 space-y-1 py-2">
        <p class="text-sm">{{ item.data.title }}</p>
      </div>
    </Link>
  </div>


</template>

<script>
import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {helpers} from "@/mixins/Helpers.js";

export default {
  mixins: [helpers],
  name: "ResultEducationalGroupItem",
	components: {BasicIcon, Link},
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