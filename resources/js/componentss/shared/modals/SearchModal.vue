<template>
	<div :id="open_id" class="hs-overlay hidden hs-overlay-backdrop-open:bg-black/70 [--body-scroll:false] [--overlay-backdrop:true] size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-hidden pointer-events-none" role="dialog" tabindex="-1" :aria-labelledby="open_id + '-label'">
    <div class="hs-overlay-open:mt-0 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-10 opacity-0 transition-all max-w-full max-h-full h-full md:mx-auto">
      <div :data-hs-overlay="'#' + open_id" class="w-full pointer-events-auto cursor-pointer">
        <button class="md:px-4 px-2 py-2 ml-auto items-center flex justify-end" type="button">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 hover:text-white text-gray-200 duration-150 font-bold">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>

        </button>
      </div>
      <div class="flex flex-col bg-white pointer-events-auto max-w-full md:mx-4 md:px-2 max-h-full h-full md:max-h-full rounded-xl overflow-y-auto">
        <slot />
			</div>
		</div>
	</div>

</template>

<script>

import {Link} from "@inertiajs/vue3";
import axios from "axios";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {defineAsyncComponent} from "vue";


export default {
  inheritAttrs: false,
  name: 'SearchModal',
	props: {
		open_id: {
			type: String,
		}

	},
	data() {
		return {}
	},
	components: {
		BasicIcon,
		Link,
	},
	methods: {

	},


}

</script>

<style scoped>


.header-filter {
	transition: all 0.3s;
	backdrop-filter: saturate(180%) blur(7px);
	background: hsla(0, 0%, 100%, .6);
}




</style>
