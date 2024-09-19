<template>

	<a href="#" @click.prevent="this.back" class="inline-flex items-center gap-x-1.5 text-sm text-gray-600 decoration-2 hover:underline dark:text-blue-500">
		<svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
		{{ title }}
	</a>

</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "FacultyBackButton",
	components: {Link},
	data() {
		return {}
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
		back() {
			if (this.$page.props.urlPrev !== 'empty') {
				this.$inertia.visit(this.$page.props.urlPrev);
			}
		},
	},

	props: {
		title: {
			type: String,
		},
	},
}

</script>


<style scoped>

</style>