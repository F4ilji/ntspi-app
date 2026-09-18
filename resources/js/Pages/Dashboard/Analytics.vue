<template>
  <DashboardLayout>
    <template #header-title>Аналитика сайта</template>
    <template #header-subtitle>Статистика посещений и активности пользователей</template>
    <template #header-actions>
      <div class="flex items-center gap-2">
        <AnalyticsPeriodFilter :model-value="period" @update:model-value="changePeriod" />
        <div class="relative" ref="settingsWrap">
          <button
            @click="showSettings = !showSettings"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </button>
          <div
            v-if="showSettings"
            class="absolute right-0 top-full mt-1 bg-layer border border-layer-line rounded-lg shadow-lg p-2 z-10 min-w-[160px]"
          >
            <button
              @click="clearData"
              class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 rounded-md transition-colors"
            >
              Очистить данные
            </button>
          </div>
        </div>
      </div>
    </template>

    <FlashMessages />

    <AnalyticsOverview :overview="overview" class="mb-6" />

    <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-5 mb-6">
      <AnalyticsChart :data="dailyStats" />
    </div>

    <!-- Groups -->
    <div v-for="groupKey in groupKeys" :key="groupKey" class="bg-layer border border-layer-line rounded-lg shadow-xs p-5 mb-6">
      <h3 class="text-sm font-semibold text-foreground mb-3">{{ navigation.groupLabels[groupKey] || groupKey }}</h3>
      <AnalyticsNavigationSections :navigation="navigation" :period="period" :group="groupKey" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-5">
        <h3 class="text-sm font-semibold text-foreground mb-3">Топ страниц</h3>
        <AnalyticsTopPages :pages="topPages" />
      </div>

      <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-5">
        <h3 class="text-sm font-semibold text-foreground mb-3">Источники трафика</h3>
        <AnalyticsReferrers :items="referrers" />
      </div>

      <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-5">
        <h3 class="text-sm font-semibold text-foreground mb-3">Устройства</h3>
        <AnalyticsDevices :devices="devices.devices" :browsers="devices.browsers" :oses="devices.oses" />
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from './Components/DashboardLayout.vue';
import FlashMessages from './Components/shared/FlashMessages.vue';
import AnalyticsOverview from './Components/shared/AnalyticsOverview.vue';
import AnalyticsChart from './Components/shared/AnalyticsChart.vue';
import AnalyticsTopPages from './Components/shared/AnalyticsTopPages.vue';
import AnalyticsReferrers from './Components/shared/AnalyticsReferrers.vue';
import AnalyticsDevices from './Components/shared/AnalyticsDevices.vue';
import AnalyticsPeriodFilter from './Components/shared/AnalyticsPeriodFilter.vue';
import AnalyticsNavigationSections from './Components/shared/AnalyticsNavigationSections.vue';

export default {
  name: 'Analytics',
  components: {
    DashboardLayout,
    FlashMessages,
    AnalyticsOverview,
    AnalyticsChart,
    AnalyticsTopPages,
    AnalyticsReferrers,
    AnalyticsDevices,
    AnalyticsPeriodFilter,
    AnalyticsNavigationSections,
  },
  props: {
    overview: { type: Object, required: true },
    topPages: { type: Array, required: true },
    dailyStats: { type: Array, required: true },
    referrers: { type: Array, required: true },
    devices: { type: Object, required: true },
    navigation: { type: Object, required: true },
    period: { type: Number, default: 30 },
  },
  data() {
    return { showSettings: false };
  },
  computed: {
    groupKeys() {
      const keys = new Set(this.navigation.sections.map(s => s.group).filter(Boolean));
      return [...keys];
    },
  },
  mounted() {
    this.SET_DOCUMENT_TITLE('Аналитика');
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside);
  },
  methods: {
    changePeriod(days) {
      this.$inertia.get(route('dashboard.analytics.index'), { days }, { preserveState: true });
    },
    clearData() {
      this.showSettings = false;
      if (confirm('Удалить все данные аналитики? Это действие необратимо.')) {
        this.$inertia.post(route('dashboard.analytics.clear'));
      }
    },
    handleClickOutside(e) {
      if (this.$refs.settingsWrap && !this.$refs.settingsWrap.contains(e.target)) {
        this.showSettings = false;
      }
    },
  },
};
</script>
