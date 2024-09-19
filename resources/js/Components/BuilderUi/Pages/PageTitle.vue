<template>

	<div class="space-y-3">
		<h1 class="text-2xl mb-10 font-bold md:text-3xl">{{ header }}</h1>
	</div>

</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "PageTitle",
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