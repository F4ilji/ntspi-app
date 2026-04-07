<template>
  <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
    <div class="p-5 border-b border-layer-line">
      <h3 class="text-sm font-semibold text-foreground">Последняя активность</h3>
    </div>

    <div class="divide-y divide-layer-line">
      <!-- Recent Posts -->
      <div v-if="recentActivity.recent_posts.length > 0" class="p-5">
        <div class="flex items-center justify-between mb-3">
          <h4 class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Публикации</h4>
          <Link :href="route('dashboard.posts.index')" class="text-xs text-primary hover:text-primary/80 transition-colors">
            Все новости →
          </Link>
        </div>
        <ul class="space-y-2">
          <li v-for="post in recentActivity.recent_posts" :key="post.id" class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
              <div
                class="w-2 h-2 rounded-full"
                :class="{
                  'bg-amber-500': post.status === 'verification',
                  'bg-emerald-500': post.status === 'published',
                  'bg-gray-400': post.status === 'rejected',
                }"
              ></div>
            </div>
            <div class="flex-1 min-w-0">
              <Link
                :href="route('dashboard.posts.edit', post.id)"
                class="text-sm font-medium text-foreground hover:text-primary transition-colors truncate block"
              >
                {{ post.title }}
              </Link>
              <div class="flex items-center gap-2 mt-1">
                <span class="text-xs text-muted-foreground-1">{{ FORMAT_DATE(post.created_at, 'short') }}</span>
                <span
                  class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium"
                  :class="{
                    'bg-amber-500/10 text-amber-700': post.status === 'verification',
                    'bg-emerald-500/10 text-emerald-700': post.status === 'published',
                    'bg-gray-500/10 text-gray-700': post.status === 'rejected',
                  }"
                >
                  {{ STATUS_LABEL(post.status) }}
                </span>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <!-- Recent Schedules -->
      <div v-if="recentActivity.recent_schedules.length > 0" class="p-5">
        <div class="flex items-center justify-between mb-3">
          <h4 class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Расписания</h4>
          <Link :href="route('dashboard.schedules.index')" class="text-xs text-primary hover:text-primary/80 transition-colors">
            Все расписания →
          </Link>
        </div>
        <ul class="space-y-2">
          <li v-for="schedule in recentActivity.recent_schedules" :key="schedule.id" class="flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
              <DashboardIcon name="calendar" size="4" class="text-emerald-600 flex-shrink-0" />
              <span class="text-sm text-foreground truncate">
                {{ schedule.educational_group?.title || 'Без группы' }}
              </span>
            </div>
            <span class="text-xs text-muted-foreground-1 flex-shrink-0">{{ FORMAT_DATE(schedule.created_at, 'short') }}</span>
          </li>
        </ul>
      </div>

      <!-- Recent Sliders -->
      <div v-if="recentActivity.recent_sliders.length > 0" class="p-5">
        <div class="flex items-center justify-between mb-3">
          <h4 class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Слайдеры</h4>
          <Link :href="route('dashboard.sliders.index')" class="text-xs text-primary hover:text-primary/80 transition-colors">
            Все слайдеры →
          </Link>
        </div>
        <ul class="space-y-2">
          <li v-for="slider in recentActivity.recent_sliders" :key="slider.id" class="flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
              <DashboardIcon name="photo" size="4" class="text-rose-600 flex-shrink-0" />
              <div class="min-w-0">
                <span class="text-sm text-foreground block truncate">{{ slider.title }}</span>
                <span class="text-xs text-muted-foreground-1">{{ slider.slides_count }} слайд(ов)</span>
              </div>
            </div>
            <span class="text-xs text-muted-foreground-1 flex-shrink-0">{{ FORMAT_DATE(slider.created_at, 'short') }}</span>
          </li>
        </ul>
      </div>

      <!-- Empty State -->
      <div v-if="isEmpty" class="p-8 text-center">
        <DashboardIcon name="clock" size="8" class="text-muted-foreground-3 mx-auto mb-3" />
        <p class="text-sm text-muted-foreground-1">Пока нет активности</p>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import DashboardIcon from '../DashboardIcon.vue';

export default {
  name: 'RecentActivity',
  components: {
    Link,
    DashboardIcon,
  },
  props: {
    recentActivity: {
      type: Object,
      required: true,
      default: () => ({
        recent_posts: [],
        recent_schedules: [],
        recent_sliders: [],
      }),
    },
  },
  computed: {
    isEmpty() {
      return (
        this.recentActivity.recent_posts.length === 0 &&
        this.recentActivity.recent_schedules.length === 0 &&
        this.recentActivity.recent_sliders.length === 0
      );
    },
  },
}
</script>
