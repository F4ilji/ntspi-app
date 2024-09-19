<template>

	<h1 class="text-brand-primary text-2xl mb-3 mt-2 md:text-3xl font-semibold tracking-tight dark:text-white lg:text-[40px] lg:leading-tight">
		{{ header }}
	</h1>

</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "PostTitle",
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
		header: {
			type: String,
		},
	},
}

</script>


<style scoped>

</style>