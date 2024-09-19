<template>

	<span class="block">Чтение займет {{ time }}<!-- --> Минуты</span>


</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "PostTimeRead",
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
		time: {
			type: String,
		},
	},
}

</script>


<style scoped>

</style>