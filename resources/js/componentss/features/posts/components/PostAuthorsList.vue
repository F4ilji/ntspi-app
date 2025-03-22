<template>

	<div class="col-start-2">
		<div class="hs-tooltip inline-block">
			<button type="button" class="hs-tooltip-toggle underline hover:text-blue-400 duration-300">
				Над статьей работали
				<span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute duration-300 invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-neutral-700" role="tooltip">
          								<template v-for="author in authors">
														{{ author }} <br>
													</template>
        								</span>
			</button>
		</div>
	</div>


</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "PostAuthorsList",
	components: {Link},
	data() {
		return {
		}
	},
	methods: {
		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText
				LimitedText = text.substring(0, symbols)
				return LimitedText + "..."
			}
			return text
		},
	},

	props: {
		authors: {
			type: Array,
		},
	},
}

</script>


<style scoped>

</style>