<template>

	<div>
		<h1 class="text-2xl mb-5 font-bold md:text-3xl">{{ header }}</h1>
	</div>

</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "BasicTitle",
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
      required: true
		},
	},
}

</script>


<style scoped>

</style>