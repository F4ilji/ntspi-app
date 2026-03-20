<template>
  <div>
    <component
        v-for="(block, index) in blocks"
        :key="index"
        :is="componentMapSSR[block.type]"
        :block="block"
    />
  </div>
</template>

<script>
import { defineAsyncComponent } from 'vue';

import ParagraphBlock from "@/componentss/shared/builder/pageBuilder/blocks/ParagraphBlock.vue";
import BasicImageSlider from "@/componentss/shared/builder/pageBuilder/blocks/BasicImageSlider.vue";
import ImageBlock from "@/componentss/shared/builder/pageBuilder/blocks/ImageBlock.vue";
import FileBlock from "@/componentss/shared/builder/pageBuilder/blocks/FileBlock.vue";
import PersonBlock from "@/componentss/shared/builder/pageBuilder/blocks/PersonBlock.vue";
import StepperBlock from "@/componentss/shared/builder/pageBuilder/blocks/StepperBlock.vue";
import VideoBlock from "@/componentss/shared/builder/pageBuilder/blocks/VideoBlock.vue";
import TabBlock from "@/componentss/shared/builder/pageBuilder/blocks/TabBlock.vue";
import PostListBlock from "@/componentss/shared/builder/pageBuilder/blocks/PostListBlock.vue";
import PostItemBlock from "@/componentss/shared/builder/pageBuilder/blocks/PostItemBlock.vue";
import PageItemBlock from "@/componentss/shared/builder/pageBuilder/blocks/PageItemBlock.vue";
import FormBlock from "@/componentss/shared/builder/pageBuilder/blocks/FormBlock.vue";
import PageResourceList from "@/componentss/shared/builder/pageBuilder/blocks/PageResourceList.vue";
import ContactSectionBlock from "@/componentss/shared/builder/pageBuilder/blocks/contacts/ContactSectionBlock.vue";
import SliderBlock from "@/componentss/features/sliders/shared/SliderBlock.vue";
import HeadingBlock from "@/componentss/shared/builder/pageBuilder/blocks/HeadingBlock.vue";


export default {
	name: "ServerSideBuilder",
	components: {
    HeadingBlock,
    ParagraphBlock,
    BasicImageSlider,
    ImageBlock,
    FileBlock,
    PersonBlock,
    StepperBlock,
    VideoBlock,
    TabBlock,
    PostListBlock,
    PostItemBlock,
    PageItemBlock,
    FormBlock,
    PageResourceList,
    ContactSectionBlock,
    SliderBlock
  },
	data() {
		return {
			loading: true,
      componentMapSSR: {
        heading: HeadingBlock,
        paragraph: ParagraphBlock,
        images: BasicImageSlider,
        image: ImageBlock,
        files: FileBlock,
        person: PersonBlock,
        stepper: StepperBlock,
        video: VideoBlock,
        tabs: TabBlock,
        postsList: PostListBlock,
        postItem: PostItemBlock,
        pageItem: PageItemBlock,
        customForm: FormBlock,
        pageResourceList: PageResourceList,
        contact: ContactSectionBlock,
        slider: SliderBlock
      },

    };
	},
	methods: {
	},
	props: {
		blocks: {
			type: Object,
			required: true,
		},
	},
}
</script>

<style scoped>
/* Анимация появления */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.15s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>