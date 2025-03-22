<template>
	<div >
		<h2 :id="generateSlug(block.data.content)" class="font-bold text-xl">{{ block.data.content }}</h2>
	</div>
</template>

<script>
import slugify from "slugify";

export default {
	name: "HeadingBlock",
	methods: {
		generateSlug: function (text) {
			return slugify(text, {
				lower: true,
				strict: true,
				locale: 'ru'
			});
		},
	},
	props: {
		block: {
			type: Object,
		},
	},
}
</script>

<style scoped>

</style>