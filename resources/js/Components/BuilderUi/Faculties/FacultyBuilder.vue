<template>
		<component
				v-for="(block, index) in blocks"
				:key="index"
				:is="getComponent(block.type)"
				:block="block"
		/>

</template>

<script>
import HeadingBlock from "@/Components/BuilderUi/Faculties/Blocks/HeadingBlock.vue";
import ParagraphBlock from "@/Components/BuilderUi/Faculties/Blocks/ParagraphBlock.vue";
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import ImageBlock from "@/Components/BuilderUi/Faculties/Blocks/ImageBlock.vue";
import FileBlock from "@/Components/BuilderUi/Faculties/Blocks/FileBlock.vue";
import PersonBlock from "@/Components/BuilderUi/Faculties/Blocks/PersonBlock.vue";
import StepperBlock from "@/Components/BuilderUi/Faculties/Blocks/StepperBlock.vue";
import VideoBlock from "@/Components/BuilderUi/Faculties/Blocks/VideoBlock.vue";
import TabBlock from "@/Components/BuilderUi/Faculties/Blocks/TabBlock.vue";
import PostListBlock from "@/Components/BuilderUi/Faculties/Blocks/PostListBlock.vue";
import PageItemBlock from "@/Components/BuilderUi/Faculties/Blocks/PageItemBlock.vue";
import PostItemBlock from "@/Components/BuilderUi/Faculties/Blocks/PostItemBlock.vue";

export default {
	name: "FacultyBuilder",
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