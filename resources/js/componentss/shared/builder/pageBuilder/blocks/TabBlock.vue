<template>

	<div class="">
		<nav class="-mb-0.5 flex justify-center gap-2 flex-wrap" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
			<button
					v-for="(tab, index) in block.data.tab" type="button"
					class="hs-tab-active:bg-gray-100 rounded-md hs-tab-active:text-gray-700 py-1.5 px-3 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
					:class="(activeTab === index) ? 'active' : ''"
					@click="activeTab = index"
					:id="generateSlug(tab.title) + '-item'"
					:data-hs-tab="'#' + generateSlug(tab.title)"
					aria-selected="false"
					:aria-controls="generateSlug(tab.title)" role="tab">
				{{ tab.title }}
			</button>
		</nav>
	</div>

	<div class="mt-3">
		<template v-for="(tab, index) in block.data.tab">

			<div :id="generateSlug(tab.title)" :class="(activeTab === index) ? '' : 'hidden'"
					 role="tabpanel" :aria-labelledby="generateSlug(tab.title) + '-item'">
				<PageTabBuilder :blocks="tab.content" />
			</div>
		</template>
	</div>
</template>

<script>
import slugify from "slugify";
import PageTabBuilder from "@/componentss/shared/builder/tabsBuilder/PageTabBuilder.vue";


export default {
	name: "TabBlock",
	components: {
		PageTabBuilder
	},
	data() {
		return {
			activeTab: 0,
		}
	},
	methods: {
		generateSlug: function (text) {
			return slugify(text, {
				lower: true,
				strict: true,
				locale: 'ru'
			});
		},
	},
	mounted() {
		this.domainPath = window.location.origin;

	},
	props: {
		block: {
			type: Object,
		},
	},
}
</script>

<style>

</style>