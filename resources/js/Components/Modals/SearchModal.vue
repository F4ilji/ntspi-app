<template>

	<div :id="open_id" class="hs-overlay hidden [--overlay-backdrop:false] size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" :aria-labelledby="open_id + '-label'">
		<div class="hs-overlay-open:mt-0 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-10 opacity-0 transition-all max-w-full max-h-full h-full md:mt-0 md:mx-auto">
			<div class="flex flex-col bg-white pointer-events-auto max-w-full max-h-full h-full md:max-h-full">
				<ClientGlobalSearch :open_id="open_id" ></ClientGlobalSearch>
			</div>
		</div>
	</div>

</template>

<script>

import {Link} from "@inertiajs/vue3";
import axios from "axios";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import {defineAsyncComponent} from "vue";


export default {
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
		BaseIcon,
		ClientGlobalSearch: defineAsyncComponent(() =>
				import('@/Components/ClientGlobalSearch.vue')
		),
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
