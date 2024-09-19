 <template>
		<component
				v-for="(block, index) in blocks"
				:key="index"
				:is="getComponent(block.type)"
				:block="block"
		/>
</template>

<script>
import HeadingBlock from "@/Components/BuilderUi/Pages/Blocks/HeadingBlock.vue";
import ParagraphBlock from "@/Components/BuilderUi/Pages/Blocks/ParagraphBlock.vue";
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import ImageBlock from "@/Components/BuilderUi/Pages/Blocks/ImageBlock.vue";
import FileBlock from "@/Components/BuilderUi/Pages/Blocks/FileBlock.vue";
import PersonBlock from "@/Components/BuilderUi/Pages/Blocks/PersonBlock.vue";
import StepperBlock from "@/Components/BuilderUi/Pages/Blocks/StepperBlock.vue";
import VideoBlock from "@/Components/BuilderUi/Pages/Blocks/VideoBlock.vue";
import TabBlock from "@/Components/BuilderUi/Pages/Blocks/TabBlock.vue";
import PostListBlock from "@/Components/BuilderUi/Pages/Blocks/PostListBlock.vue";
import PageItemBlock from "@/Components/BuilderUi/Pages/Blocks/PageItemBlock.vue";
import PostItemBlock from "@/Components/BuilderUi/Pages/Blocks/PostItemBlock.vue";
import FormBlock from "@/Components/BuilderUi/Pages/Blocks/FormBlock.vue";

export default {
	name: "PageBuilder",
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
		PostItemBlock,
		FormBlock
	},
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
				customForm: 'FormBlock'
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