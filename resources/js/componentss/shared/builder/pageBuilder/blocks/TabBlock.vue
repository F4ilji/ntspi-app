<template>
	<div v-if="block.data.settings?.is_accordion">
    <div class="hs-accordion-group">
      <div v-for="(tab, index) in block.data.tab"  class="hs-accordion hs-accordion-active:border-gray-200 bg-white border border-transparent rounded-xl dark:hs-accordion-active:border-neutral-700 dark:bg-neutral-800 dark:border-transparent" id="hs-active-bordered-heading-one">
        <button class="hs-accordion-toggle hs-accordion-active:text-blue-600 inline-flex justify-between items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:hs-accordion-active:text-blue-500 dark:text-neutral-200 dark:hover:text-neutral-400 dark:focus:outline-hidden dark:focus:text-neutral-400" aria-expanded="false" aria-controls="hs-basic-active-bordered-collapse-one">
          {{ tab.title }}
          <svg class="hs-accordion-active:hidden block size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"></path>
            <path d="M12 5v14"></path>
          </svg>
          <svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"></path>
          </svg>
        </button>
        <div id="hs-basic-active-bordered-collapse-one" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="hs-active-bordered-heading-one">
          <div class="pb-4 px-5 text-gray-800">
            <PageTabBuilder :blocks="tab.content" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else>
		<div class="-mb-0.5 flex justify-center gap-2 flex-wrap" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
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
		</div>
	  <div class="mt-3">
		<template v-for="(tab, index) in block.data.tab">

			<div :id="generateSlug(tab.title)" :class="(activeTab === index) ? '' : 'hidden'"
					 role="tabpanel" :aria-labelledby="generateSlug(tab.title) + '-item'">
				<PageTabBuilder :blocks="tab.content" />
			</div>
		</template>
	</div>
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