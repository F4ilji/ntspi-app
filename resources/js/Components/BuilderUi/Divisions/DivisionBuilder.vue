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
	name: "DivisionBuilder",
	components: {

	},
	methods: {
		getComponent(type) {
			const componentMap = {
				heading: () => import('@/Components/BuilderUi/Divisions/Blocks/HeadingBlock.vue'),
				paragraph: () => import('@/Components/BuilderUi/Divisions/Blocks/ParagraphBlock.vue'),
				images: () => import('@/Components/ClientImageSlider.vue'),
				image: () => import('@/Components/BuilderUi/Divisions/Blocks/ImageBlock.vue'),
				files: () => import('@/Components/BuilderUi/Divisions/Blocks/FileBlock.vue'),
				person: () => import('@/Components/BuilderUi/Divisions/Blocks/PersonBlock.vue'),
				stepper: () => import('@/Components/BuilderUi/Divisions/Blocks/StepperBlock.vue'),
				video: () => import('@/Components/BuilderUi/Divisions/Blocks/VideoBlock.vue'),
				tabs: () => import('@/Components/BuilderUi/Divisions/Blocks/TabBlock.vue'),
				postsList: () => import('@/Components/BuilderUi/Divisions/Blocks/PostListBlock.vue'),
				postItem: () => import('@/Components/BuilderUi/Divisions/Blocks/PageItemBlock.vue'),
				pageItem: () => import('@/Components/BuilderUi/Divisions/Blocks/PostItemBlock.vue'),
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