<template>
  <div class="max-w-2xl text-center mx-auto mb-10 lg:mb-14">
    <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">{{ header }}</h2>
    <p class="mt-1 text-gray-600 dark:text-neutral-400">{{ bottomText }}</p>
  </div>

</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "DivisionListTitle",
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
    bottomText: {
      type: String,
      required: true,
    }
	},
}

</script>


<style scoped>

</style>