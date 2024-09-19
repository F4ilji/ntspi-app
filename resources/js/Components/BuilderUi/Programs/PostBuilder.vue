<template>
		<component
				v-for="(block, index) in blocks"
				:key="index"
				:is="getComponent(block.type)"
				:block="block"
		/>

</template>

<script>
import HeadingBlock from "@/Components/BuilderUi/Events/Blocks/HeadingBlock.vue";
import ParagraphBlock from "@/Components/BuilderUi/Events/Blocks/ParagraphBlock.vue";
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import ImageBlock from "@/Components/BuilderUi/Events/Blocks/ImageBlock.vue";
import FileBlock from "@/Components/BuilderUi/Events/Blocks/FileBlock.vue";
import PersonBlock from "@/Components/BuilderUi/Events/Blocks/PersonBlock.vue";
import StepperBlock from "@/Components/BuilderUi/Events/Blocks/StepperBlock.vue";
import VideoBlock from "@/Components/BuilderUi/Events/Blocks/VideoBlock.vue";
import TabBlock from "@/Components/BuilderUi/Events/Blocks/TabBlock.vue";
import PostListBlock from "@/Components/BuilderUi/Events/Blocks/PostListBlock.vue";
import PageItemBlock from "@/Components/BuilderUi/Events/Blocks/PageItemBlock.vue";
import PostItemBlock from "@/Components/BuilderUi/Events/Blocks/PostItemBlock.vue";

export default {
	name: "PostBuilder",
	components: {
		FileBlock,
		ImageBlock,
		ClientImageSlider,
		ParagraphBlock,
		HeadingBlock,
		PersonBlock,
		StepperBlock,
		VideoBlock,
		TabBlock,
		PostListBlock,
		PageItemBlock,
		PostItemBlock},
	methods: {
		getComponent(type) {
			const componentMap = {
				heading: 'HeadingBlock',
				paragraph: 'ParagraphBlock',
				images: 'ClientImageSlider',
				image: 'ImageBlock',
				files: 'FileBlock',
				person: 'PersonBlock',
				stepper: 'StepperBlock',
				video: 'VideoBlock',
				tabs: 'TabBlock',
				postsList: 'PostListBlock',
				postItem: 'PostItemBlock',
				pageItem: 'PageItemBlock',

			};
			return componentMap[type] || null;
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