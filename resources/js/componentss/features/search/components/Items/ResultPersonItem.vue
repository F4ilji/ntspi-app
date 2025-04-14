<template>
  <div class="border-b w-full">
    <a v-if="item.tag === 'User'" :href="route('client.person.show', item.data.slug)" target="_blank" class="rounded-lg hover:bg-[#F2F2F2] my-1 px-4 w-full flex items-center">
      <BasicIcon name="post_search" class="w-5 h-5 flex-shrink-0 text-gray-500" />
      <div class="px-4 space-y-1 py-2">
        <p class="text-sm">{{ item.data.title }}</p>
        <div v-if="item.matches.length !== 0" class="">
          <p class="truncate text-[13px] text-gray-500">{{ item.matches[0] }}</p>
        </div>
      </div>
    </a>
  </div>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";

export default {
	name: "ResultPersonItem",
	components: {BasicIcon, Link},
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