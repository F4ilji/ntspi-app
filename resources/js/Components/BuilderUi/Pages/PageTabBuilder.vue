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

import PageSkeleton from "@/Components/BuilderUi/Pages/PageSkeleton.vue";

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
				heading: () => import('@/Components/BuilderUi/Pages/Blocks/HeadingBlock.vue'),
				paragraph: () => import('@/Components/BuilderUi/Pages/Blocks/ParagraphBlock.vue'),
				images: () => import('@/Components/ClientImageSlider.vue'),
				image: () => import('@/Components/BuilderUi/Pages/Blocks/ImageBlock.vue'),
				files: () => import('@/Components/BuilderUi/Pages/Blocks/FileBlock.vue'),
				person: () => import('@/Components/BuilderUi/Pages/Blocks/PersonBlock.vue'),
				stepper: () => import('@/Components/BuilderUi/Pages/Blocks/StepperBlock.vue'),
				video: () => import('@/Components/BuilderUi/Pages/Blocks/VideoBlock.vue'),
				postsList: () => import('@/Components/BuilderUi/Pages/Blocks/PostListBlock.vue'),
				postItem: () => import('@/Components/BuilderUi/Pages/Blocks/PostItemBlock.vue'),
				pageItem: () => import('@/Components/BuilderUi/Pages/Blocks/PageItemBlock.vue'),
				customForm: () => import('@/Components/BuilderUi/Pages/Blocks/FormBlock.vue'),
				pageResourceList: () => import('@/Components/BuilderUi/Pages/Blocks/PageResourceList.vue'),
				contact: () => import('@/Components/BaseComponents/BaseBuilderUi/Blocks/Contacts/ContactSectionBlock.vue'),
			};

			// Создайте массив промисов для загрузки всех компонентов
			const promises = Object.values(componentMap).map(load => load());

			// Дождитесь завершения всех загрузок
			await Promise.all(promises);
			this.loading = false; // Установите флаг загрузки в false
		},
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
				postsList: () => import('@/Components/BuilderUi/Pages/Blocks/PostListBlock.vue'),
				postItem: () => import('@/Components/BuilderUi/Pages/Blocks/PostItemBlock.vue'),
				pageItem: () => import('@/Components/BuilderUi/Pages/Blocks/PageItemBlock.vue'),
				customForm: () => import('@/Components/BuilderUi/Pages/Blocks/FormBlock.vue'),
				pageResourceList: () => import('@/Components/BuilderUi/Pages/Blocks/PageResourceList.vue'),
				contact: () => import('@/Components/BaseComponents/BaseBuilderUi/Blocks/Contacts/ContactSectionBlock.vue'),
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