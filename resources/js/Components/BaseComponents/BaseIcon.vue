<template>
	<svg v-bind="svgAttributes" v-html="icon.path"></svg>
</template>

<script>
import icons from "@/Components/other/icons.js";

export default {
	name: "BaseIcon",
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
	computed: {
		icon() {
			return icons[this.transformIconName(this.name)] || icons['file'];
		},
		svgAttributes() {
			return {
				xmlns: "http://www.w3.org/2000/svg",
				fill: this.icon.fill || 'none',
				viewBox: this.icon.viewBox || this.viewBox || '0 0 24 24',
				'stroke-width': this.icon.stroke_width || this.stroke_width || 1.5,
				stroke: this.icon.stroke || 'currentColor',
				class: this.$attrs.class || 'w-6 h-6'
			};
		}
	},
	methods: {
		transformIconName(iconName) {
			return iconName.replace(/^heroicon-(o|c|s|m)-/, '')
					.replace(/-(\w)/g, (_, letter) => letter.toUpperCase());
		}
	}
}
</script>