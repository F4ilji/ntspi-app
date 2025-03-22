<template>
	<div>
		<PageSkeleton v-if="loading" />
		<div class="space-y-6" v-else>
			<component
					v-for="(block, index) in blocks"
					:key="index"
					:is="getComponent(block.type)"
					:block="block"
			/>
		</div>
	</div>
</template>

<script>
import { defineAsyncComponent } from 'vue';
import PageSkeleton from "@/componentss/shared/builder/pageBuilder/skeletons/PageSkeleton.vue";


export default {
	name: "PageTabBuilder",
	components: {PageSkeleton},
	data() {
		return {
			loading: true, // Флаг загрузки
		};
	},
	methods: {
		async loadAllComponents() {
			const componentMap = {
        heading: () => import('@/componentss/shared/builder/pageBuilder/blocks/HeadingBlock.vue'),
        paragraph: () => import('@/componentss/shared/builder/pageBuilder/blocks/ParagraphBlock.vue'),
        images: () => import('@/componentss/shared/builder/pageBuilder/blocks/BasicImageSlider.vue'),
        image: () => import('@/componentss/shared/builder/pageBuilder/blocks/ImageBlock.vue'),
        files: () => import('@/componentss/shared/builder/pageBuilder/blocks/FileBlock.vue'),
        person: () => import('@/componentss/shared/builder/pageBuilder/blocks/PersonBlock.vue'),
        stepper: () => import('@/componentss/shared/builder/pageBuilder/blocks/StepperBlock.vue'),
        video: () => import('@/componentss/shared/builder/pageBuilder/blocks/VideoBlock.vue'),
        postsList: () => import('@/componentss/shared/builder/pageBuilder/blocks/PostListBlock.vue'),
        postItem: () => import('@/componentss/shared/builder/pageBuilder/blocks/PostItemBlock.vue'),
        pageItem: () => import('@/componentss/shared/builder/pageBuilder/blocks/PageItemBlock.vue'),
        customForm: () => import('@/componentss/shared/builder/pageBuilder/blocks/FormBlock.vue'),
        pageResourceList: () => import('@/componentss/shared/builder/pageBuilder/blocks/PageResourceList.vue'),
        contact: () => import('@/componentss/shared/builder/pageBuilder/blocks/contacts/ContactSectionBlock.vue'),
        slider: () => import('@/componentss/features/sliders/shared/SliderBlock.vue')
			};

			// Создайте массив промисов для загрузки всех компонентов
			const promises = Object.values(componentMap).map(load => load());

			// Дождитесь завершения всех загрузок
			await Promise.all(promises);
			this.loading = false; // Установите флаг загрузки в false
		},
		getComponent(type) {
			const componentMap = {
        heading: () => import('@/componentss/shared/builder/pageBuilder/blocks/HeadingBlock.vue'),
        paragraph: () => import('@/componentss/shared/builder/pageBuilder/blocks/ParagraphBlock.vue'),
        images: () => import('@/componentss/shared/builder/pageBuilder/blocks/BasicImageSlider.vue'),
        image: () => import('@/componentss/shared/builder/pageBuilder/blocks/ImageBlock.vue'),
        files: () => import('@/componentss/shared/builder/pageBuilder/blocks/FileBlock.vue'),
        person: () => import('@/componentss/shared/builder/pageBuilder/blocks/PersonBlock.vue'),
        stepper: () => import('@/componentss/shared/builder/pageBuilder/blocks/StepperBlock.vue'),
        video: () => import('@/componentss/shared/builder/pageBuilder/blocks/VideoBlock.vue'),
        postsList: () => import('@/componentss/shared/builder/pageBuilder/blocks/PostListBlock.vue'),
        postItem: () => import('@/componentss/shared/builder/pageBuilder/blocks/PostItemBlock.vue'),
        pageItem: () => import('@/componentss/shared/builder/pageBuilder/blocks/PageItemBlock.vue'),
        customForm: () => import('@/componentss/shared/builder/pageBuilder/blocks/FormBlock.vue'),
        pageResourceList: () => import('@/componentss/shared/builder/pageBuilder/blocks/PageResourceList.vue'),
        contact: () => import('@/componentss/shared/builder/pageBuilder/blocks/contacts/ContactSectionBlock.vue'),
        slider: () => import('@/componentss/features/sliders/shared/SliderBlock.vue')
			};
			return defineAsyncComponent(componentMap[type] || null);
		},
	},
	async created() {
		await this.loadAllComponents(); // Загрузить все компоненты при создании
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