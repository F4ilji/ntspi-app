<template>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
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
          v-if="card.change.direction !== 'neutral'"
          class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium"
          :class="badgeClass(card)"
        >
          {{ card.change.direction === 'up' ? '↑' : '↓' }}
          {{ formatChange(card) }}
        </span>
      </div>
      <div class="space-y-1">
        <p class="text-2xl font-semibold text-foreground">{{ formatValue(card.value, card.format) }}</p>
        <p class="text-xs text-muted-foreground-1">{{ card.label }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../DashboardIcon.vue';

export default {
  name: 'AnalyticsOverview',
  components: { DashboardIcon },
  props: {
    overview: { type: Object, required: true },
  },
  computed: {
    statCards() {
      return [
        {
          icon: 'users',
          bgClass: 'bg-blue-500/10',
          iconClass: 'text-blue-600',
          label: 'Уникальные посетители',
          value: this.overview.unique_visitors.value,
          change: this.overview.unique_visitors,
          changeSuffix: '%',
          format: 'number',
        },
        {
          icon: 'eye',
          bgClass: 'bg-emerald-500/10',
          iconClass: 'text-emerald-600',
          label: 'Просмотры страниц',
          value: this.overview.page_views.value,
          change: this.overview.page_views,
          changeSuffix: '%',
          format: 'number',
        },
        {
          icon: 'clock',
          bgClass: 'bg-violet-500/10',
          iconClass: 'text-violet-600',
          label: 'Среднее время',
          value: this.overview.avg_time.value,
          change: this.overview.avg_time,
          changeSuffix: 'с',
          format: 'duration',
        },
        {
          icon: 'arrow-uturn-left',
          bgClass: 'bg-rose-500/10',
          iconClass: 'text-rose-600',
          label: 'Отказы (Bounce Rate)',
          value: this.overview.bounce_rate.value,
          change: this.overview.bounce_rate,
          changeSuffix: '%',
          format: 'percent',
          invertColor: true,
        },
      ];
    },
  },
  methods: {
    formatValue(value, format) {
      if (format === 'duration') {
        const min = Math.floor(value / 60);
        const sec = value % 60;
        return min > 0 ? `${min}м ${sec}с` : `${sec}с`;
      }
      if (format === 'percent') return `${value}%`;
      return new Intl.NumberFormat('ru-RU').format(value);
    },
    formatChange(card) {
      const val = Math.abs(card.change.change);
      if (card.format === 'duration') {
        const min = Math.floor(val / 60);
        const sec = val % 60;
        return min > 0 ? `${min}м ${sec}с` : `${val}с`;
      }
      return `${val}${card.changeSuffix}`;
    },
    badgeClass(card) {
      const isUp = card.change.direction === 'up';
      const bad = card.invertColor ? isUp : !isUp;
      return bad
        ? 'bg-red-500/10 text-red-600'
        : 'bg-emerald-500/10 text-emerald-600';
    },
  },
};
</script>
