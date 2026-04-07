<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div
      v-for="card in statCards"
      :key="card.label"
      class="bg-layer border border-layer-line rounded-lg p-5 shadow-xs"
    >
      <div class="flex items-center justify-between mb-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="card.bgClass">
          <DashboardIcon :name="card.icon" size="5" :class="card.iconClass" />
        </div>
        <span
          v-if="card.weekCount > 0"
          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
          :class="card.badgeClass"
        >
          +{{ card.weekCount }}
        </span>
      </div>
      <div class="space-y-1">
        <p class="text-2xl font-semibold text-foreground">{{ card.total }}</p>
        <p class="text-xs text-muted-foreground-1">{{ card.label }}</p>
      </div>
      <div v-if="card.link" class="mt-3 pt-3 border-t border-layer-line">
        <a
          :href="route(card.link.route)"
          class="text-xs font-medium transition-colors"
          :class="card.link.class"
        >
          {{ card.link.text }}
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../DashboardIcon.vue';

export default {
  name: 'StatsOverview',
  components: {
    DashboardIcon,
  },
  props: {
    stats: {
      type: Object,
      required: true,
    },
  },
  computed: {
    statCards() {
      return [
        {
          icon: 'document',
          bgClass: 'bg-blue-500/10',
          iconClass: 'text-blue-600',
          badgeClass: 'bg-blue-500/10 text-blue-700',
          total: this.stats.posts.total,
          weekCount: this.stats.posts.week,
          label: 'Всего публикаций',
          link: this.stats.posts.verification > 0
            ? {
                route: 'dashboard.posts.index',
                text: `${this.stats.posts.verification} на модерации →`,
                class: 'text-amber-600 hover:text-amber-700',
              }
            : null,
        },
        {
          icon: 'calendar',
          bgClass: 'bg-emerald-500/10',
          iconClass: 'text-emerald-600',
          badgeClass: 'bg-emerald-500/10 text-emerald-700',
          total: this.stats.schedules.total,
          weekCount: this.stats.schedules.week,
          label: 'Расписаний',
          link: null,
        },
        {
          icon: 'academic-cap',
          bgClass: 'bg-violet-500/10',
          iconClass: 'text-violet-600',
          badgeClass: 'bg-violet-500/10 text-violet-700',
          total: this.stats.educational_groups.total,
          weekCount: this.stats.educational_groups.week,
          label: 'Учебных групп',
          link: null,
        },
        {
          icon: 'photo',
          bgClass: 'bg-rose-500/10',
          iconClass: 'text-rose-600',
          badgeClass: 'bg-rose-500/10 text-rose-700',
          total: this.stats.sliders.total,
          weekCount: this.stats.sliders.week,
          label: 'Слайдеров',
          link: null,
        },
      ];
    },
  },
}
</script>
