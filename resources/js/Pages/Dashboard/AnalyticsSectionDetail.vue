<template>
  <DashboardLayout>
    <template #header-title>{{ section.label }}</template>
    <template #header-subtitle>Статистика раздела</template>
    <template #header-actions>
      <div class="flex items-center gap-2">
        <AnalyticsPeriodFilter :model-value="period" @update:model-value="changePeriod" />
        <a
          :href="route('dashboard.analytics.index', { days: period })"
          class="px-3 py-1.5 text-xs font-medium text-muted-foreground-1 hover:text-foreground border border-layer-line rounded-md transition-colors"
        >
          ← Назад
        </a>
      </div>
    </template>

    <FlashMessages />

    <!-- Breadcrumbs -->
    <nav v-if="section.breadcrumbs && section.breadcrumbs.length" class="flex items-center gap-1.5 mb-4 text-xs text-muted-foreground-1">
      <a :href="section.breadcrumbs[0].url" class="hover:text-primary transition-colors">{{ section.breadcrumbs[0].title }}</a>
      <span v-for="(crumb, i) in section.breadcrumbs.slice(1)" :key="i" class="flex items-center gap-1.5">
        <span>|</span>
        <a :href="crumb.url" class="hover:text-primary transition-colors">{{ crumb.title }}</a>
      </span>
      <span class="flex items-center gap-1.5">
        <span>/</span>
        <span class="text-foreground font-medium">{{ section.label }}</span>
      </span>
    </nav>

    <!-- Children (main_section → sub_sections, sub_section → pages) -->
    <div v-if="section.children && section.children.length" class="bg-layer border border-layer-line rounded-lg shadow-xs p-5 mb-6">
      <h3 class="text-sm font-semibold text-foreground mb-3">
        {{ section.type === 'main_section' ? 'Подразделы' : 'Страницы' }}
      </h3>
      <div class="space-y-1">
        <a
          v-for="child in section.children"
          :key="child.id"
          :href="sectionUrl(child.prefix || child.url)"
          class="relative flex items-center justify-between py-2 px-2 rounded-md overflow-hidden hover:bg-muted-hover transition-colors group"
        >
          <div
            class="absolute inset-y-0 left-0 bg-primary/5 rounded-md"
            :style="{ width: getChildWidth(child.views) + '%' }"
          ></div>
          <div class="flex items-center gap-2 relative min-w-0">
            <span class="text-xs font-medium text-foreground truncate">{{ child.title }}</span>
            <span v-if="child.pages_count" class="text-[10px] text-muted-foreground-2">({{ child.pages_count }} стр.)</span>
          </div>
          <div class="flex items-center gap-1.5 relative flex-shrink-0">
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-muted-hover text-[11px] text-muted-foreground-1">
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
              {{ child.views }}
            </span>
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-muted-hover text-[11px] text-muted-foreground-1">
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
              {{ child.visitors }}
            </span>
            <svg class="w-3 h-3 text-muted-foreground-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </a>
      </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-2 gap-4 mb-6">
      <div class="bg-layer border border-layer-line rounded-lg p-5 shadow-xs">
        <p class="text-2xl font-semibold text-foreground">{{ formatNumber(section.total_views) }}</p>
        <p class="text-xs text-muted-foreground-1">Просмотров</p>
      </div>
      <div class="bg-layer border border-layer-line rounded-lg p-5 shadow-xs">
        <p class="text-2xl font-semibold text-foreground">{{ formatNumber(section.unique_visitors) }}</p>
        <p class="text-xs text-muted-foreground-1">Уникальных посетителей</p>
      </div>
    </div>

    <!-- Chart -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-5 mb-6">
      <AnalyticsChart :data="section.daily_stats" />
    </div>

    <!-- Widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-5">
        <h3 class="text-sm font-semibold text-foreground mb-3">Источники трафика</h3>
        <AnalyticsReferrers :items="section.referrers" />
      </div>

      <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-5">
        <h3 class="text-sm font-semibold text-foreground mb-3">Устройства</h3>
        <div class="space-y-2">
          <div v-for="(count, type) in section.devices" :key="type" class="flex items-center justify-between">
            <span class="text-xs font-medium text-foreground">{{ deviceLabel(type) }}</span>
            <div class="flex items-center gap-2">
              <div class="w-20 h-1.5 bg-muted-hover rounded-full overflow-hidden">
                <div class="h-full bg-primary/60 rounded-full" :style="{ width: getDevicePercent(count) + '%' }"></div>
              </div>
              <span class="text-[11px] text-muted-foreground-1 w-8 text-right">{{ count }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Exit pages (only for page type) -->
      <div v-if="section.type === 'page' && section.exit_pages && section.exit_pages.length" class="bg-layer border border-layer-line rounded-lg shadow-xs p-5">
        <h3 class="text-sm font-semibold text-foreground mb-3">Куда ушли</h3>
        <div class="space-y-1.5">
          <div v-for="ep in section.exit_pages" :key="ep.url" class="flex items-center justify-between">
            <span class="text-[11px] font-medium text-foreground truncate">{{ ep.url }}</span>
            <span class="text-[11px] text-muted-foreground-1">{{ ep.count }}</span>
          </div>
        </div>
      </div>

      <!-- Top pages (not for page type) -->
      <div v-if="section.type !== 'page' && section.top_pages && section.top_pages.length" class="bg-layer border border-layer-line rounded-lg shadow-xs p-5">
        <h3 class="text-sm font-semibold text-foreground mb-3">Топ страниц</h3>
        <AnalyticsTopPages :pages="section.top_pages" />
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from './Components/DashboardLayout.vue';
import FlashMessages from './Components/shared/FlashMessages.vue';
import AnalyticsChart from './Components/shared/AnalyticsChart.vue';
import AnalyticsTopPages from './Components/shared/AnalyticsTopPages.vue';
import AnalyticsReferrers from './Components/shared/AnalyticsReferrers.vue';
import AnalyticsPeriodFilter from './Components/shared/AnalyticsPeriodFilter.vue';

export default {
  name: 'AnalyticsSectionDetail',
  components: {
    DashboardLayout,
    FlashMessages,
    AnalyticsChart,
    AnalyticsTopPages,
    AnalyticsReferrers,
    AnalyticsPeriodFilter,
  },
  props: {
    section: { type: Object, required: true },
    period: { type: Number, default: 30 },
  },
  computed: {
    totalDevices() {
      return Object.values(this.section.devices || {}).reduce((a, b) => a + b, 0) || 1;
    },
    maxChildViews() {
      if (!this.section.children || !this.section.children.length) return 1;
      return Math.max(...this.section.children.map(c => c.views), 1);
    },
  },
  mounted() {
    this.SET_DOCUMENT_TITLE(this.section.label + ' — Аналитика');
  },
  methods: {
    formatNumber(n) {
      return new Intl.NumberFormat('ru-RU').format(n);
    },
    deviceLabel(type) {
      const map = { desktop: 'Десктоп', mobile: 'Мобильные', tablet: 'Планшеты' };
      return map[type] || type;
    },
    getDevicePercent(count) {
      return Math.round((count / this.totalDevices) * 100);
    },
    getChildWidth(views) {
      return Math.max((views / this.maxChildViews) * 100, 2);
    },
    sectionUrl(prefix) {
      return route('dashboard.analytics.section', { prefix, days: this.period });
    },
    changePeriod(days) {
      this.$inertia.get(route('dashboard.analytics.section'), { prefix: this.section.prefix, days }, { preserveState: true });
    },
  },
};
</script>
