<template>
		<component
				v-for="(block, index) in blocks"
				:key="index"
				:is="getComponent(block.type)"
				:block="block"
		/>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";
import HeadingBlock from "@/Components/BuilderUi/Departments/Blocks/HeadingBlock.vue";
import ParagraphBlock from "@/Components/BuilderUi/Departments/Blocks/ParagraphBlock.vue";
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import ImageBlock from "@/Components/BuilderUi/Departments/Blocks/ImageBlock.vue";
import FileBlock from "@/Components/BuilderUi/Departments/Blocks/FileBlock.vue";
import PersonBlock from "@/Components/BuilderUi/Departments/Blocks/PersonBlock.vue";
import StepperBlock from "@/Components/BuilderUi/Departments/Blocks/StepperBlock.vue";
import VideoBlock from "@/Components/BuilderUi/Departments/Blocks/VideoBlock.vue";
import PostListBlock from "@/Components/BuilderUi/Departments/Blocks/PostListBlock.vue";

export default {
	name: "DivisionTabBuilder",
	components: {FileBlock, ImageBlock, ClientImageSlider, ParagraphBlock, HeadingBlock, Link, PersonBlock, StepperBlock, VideoBlock, PostListBlock},
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
				postsList: 'PostListBlock',

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