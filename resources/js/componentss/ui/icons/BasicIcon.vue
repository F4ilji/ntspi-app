<template>
  <svg aria-hidden="true">
    <use :href="symbolId" :fill="color" />
  </svg>
</template>

<script>
import { defineComponent, computed } from 'vue'

export default defineComponent({
  name: "BasicIcon",
  props: {
    name: {
      type: String,
      required: true,
    },
    prefix: {
      type: String,
      default: 'icon',
    },
    color: {
      type: String,
      default: '#333',
    },
  },
  setup(props) {
    const rawName = props.name.replace(/^heroicon-(o|c|s|m)-/, '');
    const symbolId = computed(() => `#${props.prefix}-${rawName}`)
    return { symbolId }
  },
  // computed: {
  // 	icon() {
  // 		return icons[this.transformIconName(this.name)] || icons['file'];
  // 	},
  // 	svgAttributes() {
  // 		return {
  // 			xmlns: "http://www.w3.org/2000/svg",
  // 			fill: this.icon.fill || 'none',
  // 			viewBox: this.icon.viewBox || this.viewBox || '0 0 24 24',
  // 			'stroke-width': this.icon.stroke_width || this.stroke_width || 1.5,
  // 			stroke: this.icon.stroke || 'currentColor',
  // 			class: this.$attrs.class || 'w-6 h-6'
  // 		};
  // 	}
  // },
})
</script>