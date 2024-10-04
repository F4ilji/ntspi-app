<template>
	<component
			v-for="(block, index) in blocks"
			:key="index"
			:is="getComponent(block.type)"
			:block="block"
	/>
</template>

<script>
import { defineAsyncComponent } from 'vue';
export default {
	name: "PageBuilder",
	methods: {
		getComponent(type) {
			const componentMap = {
				heading: () => import('@/Components/BuilderUi/Pages/Blocks/HeadingBlock.vue'),
				paragraph: () => import('@/Components/BuilderUi/Pages/Blocks/ParagraphBlock.vue'),
				images: () => import('@/Components/ClientImageSlider.vue'),
				image: () => import('@/Components/BuilderUi/Pages/Blocks/ImageBlock.vue'),
				files: () => import('@/Components/BuilderUi/Pages/Blocks/FileBlock.vue'),
				person: () => import('@/Components/BuilderUi/Pages/Blocks/PersonBlock.vue'),
				stepper: () => import('@/Components/BuilderUi/Pages/Blocks/StepperBlock.vue'),
				video: () => import('@/Components/BuilderUi/Pages/Blocks/VideoBlock.vue'),
				tabs: () => import('@/Components/BuilderUi/Pages/Blocks/TabBlock.vue'),
				postsList: () => import('@/Components/BuilderUi/Pages/Blocks/PostListBlock.vue'),
				postItem: () => import('@/Components/BuilderUi/Pages/Blocks/PostItemBlock.vue'),
				pageItem: () => import('@/Components/BuilderUi/Pages/Blocks/PageItemBlock.vue'),
				customForm: () => import('@/Components/BuilderUi/Pages/Blocks/FormBlock.vue')
			};
			return defineAsyncComponent(componentMap[type] || null);
		},
	},
	props: {
		blocks: {
			type: Array,
			required: true,
		},
	},
}
</script>

<style scoped>
</style>