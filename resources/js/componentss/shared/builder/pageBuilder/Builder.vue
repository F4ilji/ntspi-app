<template>
  <div>
    <transition name="fade" mode="out-in">
      <PageSkeleton v-if="loading" key="skeleton" />
      <div class="space-y-6" v-else key="content">
        <component
            v-for="(block, index) in blocks"
            :key="index"
            :is="getComponent(block.type)"
            :block="block"
        />
      </div>
    </transition>
  </div>
</template>

<script>
import { defineAsyncComponent } from 'vue';
import PageSkeleton from "@/componentss/shared/builder/pageBuilder/skeletons/PageSkeleton.vue";

export default {
	name: "Builder",
	components: {PageSkeleton},
	data() {
		return {
			loading: true,
      componentMap: {
        heading: () => import('@/componentss/shared/builder/pageBuilder/blocks/HeadingBlock.vue'),
        paragraph: () => import('@/componentss/shared/builder/pageBuilder/blocks/ParagraphBlock.vue'),
        images: () => import('@/componentss/shared/builder/pageBuilder/blocks/BasicImageSlider.vue'),
        image: () => import('@/componentss/shared/builder/pageBuilder/blocks/ImageBlock.vue'),
        files: () => import('@/componentss/shared/builder/pageBuilder/blocks/FileBlock.vue'),
        person: () => import('@/componentss/shared/builder/pageBuilder/blocks/PersonBlock.vue'),
        stepper: () => import('@/componentss/shared/builder/pageBuilder/blocks/StepperBlock.vue'),
        video: () => import('@/componentss/shared/builder/pageBuilder/blocks/VideoBlock.vue'),
        tabs: () => import('@/componentss/shared/builder/pageBuilder/blocks/TabBlock.vue'),
        postsList: () => import('@/componentss/shared/builder/pageBuilder/blocks/PostListBlock.vue'),
        postItem: () => import('@/componentss/shared/builder/pageBuilder/blocks/PostItemBlock.vue'),
        pageItem: () => import('@/componentss/shared/builder/pageBuilder/blocks/PageItemBlock.vue'),
        customForm: () => import('@/componentss/shared/builder/pageBuilder/blocks/FormBlock.vue'),
        pageResourceList: () => import('@/componentss/shared/builder/pageBuilder/blocks/PageResourceList.vue'),
        contact: () => import('@/componentss/shared/builder/pageBuilder/blocks/contacts/ContactSectionBlock.vue'),
        slider: () => import('@/componentss/features/sliders/shared/SliderBlock.vue')
      },
		};
	},
	methods: {
		async loadAllComponents() {
			// Создайте массив промисов для загрузки всех компонентов
			const promises = Object.values(this.componentMap).map(load => load());

			// Дождитесь завершения всех загрузок
			await Promise.all(promises);
			this.loading = false; // Установите флаг загрузки в false
		},
		getComponent(type) {
			return defineAsyncComponent(this.componentMap[type] || null);
		},
	},
	props: {
		blocks: {
			type: Object,
			required: true,
		},
	},
	async created() {
		await this.loadAllComponents(); // Загрузить все компоненты при создании
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