<template>
		<component
				v-for="(block, index) in blocks"
				:key="index"
				:is="getComponent(block.type)"
				:block="block"
		/>

</template>

<script>
import HeadingBlock from "@/Components/BuilderUi/Departments/Blocks/HeadingBlock.vue";
import ParagraphBlock from "@/Components/BuilderUi/Departments/Blocks/ParagraphBlock.vue";
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import ImageBlock from "@/Components/BuilderUi/Departments/Blocks/ImageBlock.vue";
import FileBlock from "@/Components/BuilderUi/Departments/Blocks/FileBlock.vue";
import PersonBlock from "@/Components/BuilderUi/Departments/Blocks/PersonBlock.vue";
import StepperBlock from "@/Components/BuilderUi/Departments/Blocks/StepperBlock.vue";
import VideoBlock from "@/Components/BuilderUi/Departments/Blocks/VideoBlock.vue";
import TabBlock from "@/Components/BuilderUi/Departments/Blocks/TabBlock.vue";
import PostListBlock from "@/Components/BuilderUi/Departments/Blocks/PostListBlock.vue";
import PageItemBlock from "@/Components/BuilderUi/Departments/Blocks/PageItemBlock.vue";
import PostItemBlock from "@/Components/BuilderUi/Departments/Blocks/PostItemBlock.vue";

export default {
	name: "DepartmentBuilder",
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