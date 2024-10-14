<template>
		<component
				v-for="(block, index) in blocks"
				:key="index"
				:is="getComponent(block.type)"
				:block="block"
		/>

</template>

<script>

import {defineAsyncComponent} from "vue";

export default {
	name: "PostBuilder",
	methods: {
		getComponent(type) {
			const componentMap = {
				heading: () => import('@/Components/BuilderUi/Posts/Blocks/HeadingBlock.vue'),
				paragraph: () => import('@/Components/BuilderUi/Posts/Blocks/ParagraphBlock.vue'),
				images: () => import('@/Components/ClientImageSlider.vue'),
				image: () => import('@/Components/BuilderUi/Posts/Blocks/ImageBlock.vue'),
				files: () => import('@/Components/BuilderUi/Posts/Blocks/FileBlock.vue'),
				person: () => import('@/Components/BuilderUi/Posts/Blocks/PersonBlock.vue'),
				stepper: () => import('@/Components/BuilderUi/Posts/Blocks/StepperBlock.vue'),
				video: () => import('@/Components/BuilderUi/Posts/Blocks/VideoBlock.vue'),
				tabs: () => import('@/Components/BuilderUi/Posts/Blocks/TabBlock.vue'),
				postsList: () => import('@/Components/BuilderUi/Posts/Blocks/PostListBlock.vue'),
				postItem: () => import('@/Components/BuilderUi/Posts/Blocks/PageItemBlock.vue'),
				pageItem: () => import('@/Components/BuilderUi/Posts/Blocks/PostItemBlock.vue'),
				customForm: () => import('@/Components/BuilderUi/Pages/Blocks/FormBlock.vue')
			};
			return defineAsyncComponent(componentMap[type] || null);
		},
	},

	props: {
		blocks: {
			type: Object,
		},
	},
}

</script>


<style scoped>

</style>