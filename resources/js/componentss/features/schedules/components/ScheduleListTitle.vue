<template>
  <h1 class="text-2xl sm:text-4xl font-bold text-gray-800 text-center">
    {{ header }}
  </h1>
  <p class="mt-3 text-gray-600 text-center">
    {{ bottomText }}
  </p>

</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "ScheduleListTitle",
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