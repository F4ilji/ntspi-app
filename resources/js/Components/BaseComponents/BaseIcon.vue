<template>
	<svg xmlns="http://www.w3.org/2000/svg"
			 :fill="icon.fill || 'none'"
			 :viewBox="icon.viewBox || this.viewBox || '0 0 24 24'"
			 :stroke-width="icon.stroke_width || this.stroke_width || 1.5"
			 :stroke="icon.stroke || 'currentColor'"
			 :class="$attrs.class || 'w-6 h-6'"
			 v-html="icon.path"
	>
	</svg>


</template>

<script>
import slugify from "slugify";
import icons from "@/Components/other/icons.js";

export default {
	name: "BaseIcon",
	data() {
		return {
			icon: icons[this.name] || icons['file']
		}
	},
	methods: {
	},
	props: {
		name: {
			type: String,
		},
		viewBox: {
			type: String,
		},
		stroke_width: {
			type: Number,
		},
		fill: {
			type: String
		}
	},
}
</script>

<style scoped>

</style>