<template>
  <div v-if="posts.length > 0" class="bg-layer border border-layer-line rounded-lg shadow-xs">
    <div class="p-5 border-b border-layer-line">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-purple-500/10 rounded-lg flex items-center justify-center">
            <DashboardIcon name="sparkles" size="4" class="text-purple-600" />
          </div>
          <div>
            <h3 class="text-sm font-semibold text-foreground">AI подготовленные публикации</h3>
            <p class="text-xs text-muted-foreground-1">Генерированы автоматически</p>
          </div>
        </div>
        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/10 text-purple-700">
          {{ posts.length }}
        </span>
      </div>
    </div>

    <ul class="divide-y divide-layer-line">
      <li
        v-for="post in posts"
        :key="post.id"
        class="p-4 hover:bg-muted/30 transition-colors"
      >
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1 min-w-0">
            <h4 class="text-sm font-medium text-foreground truncate mb-1">
              {{ post.title }}
            </h4>
            <p v-if="post.preview_text" class="text-xs text-muted-foreground-1 line-clamp-2">
              {{ post.preview_text }}
            </p>
            <div class="flex items-center gap-2 mt-2">
              <span
                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium"
                :class="{
                  'bg-amber-500/10 text-amber-700': post.status === 'verification',
                  'bg-gray-500/10 text-gray-700': post.status === 'rejected',
                }"
              >
                {{ STATUS_LABEL(post.status) }}
              </span>
              <span class="text-xs text-muted-foreground-3">•</span>
              <span class="text-xs text-muted-foreground-1">{{ FORMAT_DATE(post.created_at, 'short') }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            <Link
              :href="route('dashboard.posts.edit', post.id)"
              class="inline-flex items-center px-3 py-1.5 border border-primary/20 text-xs font-medium rounded-md text-primary hover:bg-primary/5 transition-colors"
            >
              Редактировать
            </Link>
          </div>
        </div>
      </li>
    </ul>

    <div class="p-4 border-t border-layer-line">
      <Link
        :href="route('dashboard.posts.ai-prepared')"
        class="text-sm text-primary hover:text-primary/80 transition-colors font-medium"
      >
        Посмотреть все →
      </Link>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import DashboardIcon from '../DashboardIcon.vue';

export default {
  name: 'AiPreparedWidget',
  components: {
    Link,
    DashboardIcon,
  },
  props: {
    posts: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
}
</script>
