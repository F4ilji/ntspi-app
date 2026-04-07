<template>
  <div class="space-y-4">
    <!-- Publishing Settings -->
    <div class="border border-layer-line rounded-lg overflow-hidden">
      <div class="px-4 py-3 bg-surface/50 border-b border-line-2">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-muted-foreground-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="text-sm font-medium text-foreground">Публикация по времени</h3>
        </div>
      </div>
      <div class="p-4 space-y-4">
        <div class="flex items-center gap-3">
          <input
            id="publish_after"
            :checked="publishAfter"
            @change="$emit('update:publishAfter', $event.target.checked)"
            :disabled="!!publishAt"
            type="checkbox"
            class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20"
          />
          <label for="publish_after" class="text-sm text-foreground">
            Включить отложенную публикацию
          </label>
        </div>

        <div v-if="publishAfter">
          <label for="publish_at" class="block text-sm font-medium text-foreground mb-1.5">
            Дата и время публикации
          </label>
          <input
            id="publish_at"
            :value="publishAt"
            @input="$emit('update:publishAt', $event.target.value)"
            type="datetime-local"
            :class="inputClass"
          />
          <p v-if="errors.publish_at" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            {{ errors.publish_at }}
          </p>
        </div>
      </div>
    </div>

    <!-- Social Media -->
    <div class="border border-layer-line rounded-lg overflow-hidden">
      <div class="px-4 py-3 bg-surface/50 border-b border-line-2">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2.04c-5.5 0-10 4.49-10 10.02c0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89c1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 0 0 8.44-9.9c0-5.53-4.5-10.02-10-10.02Z"/>
          </svg>
          <h3 class="text-sm font-medium text-foreground">Публикация в соцсетях</h3>
        </div>
      </div>
      <div class="p-4">
        <div class="flex items-center gap-3">
          <input
            id="publication_vk"
            :checked="publishVk"
            @change="$emit('update:publishVk', $event.target.checked)"
            type="checkbox"
            class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20"
          />
          <label for="publication_vk" class="text-sm text-foreground">
            Опубликовать ВКонтакте
          </label>
        </div>
        <p class="mt-2 text-xs text-muted-foreground-1">Новость будет автоматически опубликована в VK</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PostPublishSettings',
  props: {
    publishAfter: {
      type: Boolean,
      default: false
    },
    publishAt: {
      type: String,
      default: ''
    },
    publishVk: {
      type: Boolean,
      default: false
    },
    errors: {
      type: Object,
      default: () => ({})
    },
    inputClass: {
      type: String,
      default: 'w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all'
    }
  },
  emits: ['update:publishAfter', 'update:publishAt', 'update:publishVk']
}
</script>
