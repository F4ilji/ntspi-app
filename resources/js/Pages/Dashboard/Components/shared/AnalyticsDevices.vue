<template>
  <div>
    <div class="flex gap-1 mb-4 bg-layer border border-layer-line rounded-md p-0.5">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="activeTab = tab.key"
        class="flex-1 px-2 py-1 text-[11px] font-medium rounded transition-colors"
        :class="activeTab === tab.key ? 'bg-primary text-white' : 'text-muted-foreground-1 hover:text-foreground'"
      >
        {{ tab.label }}
      </button>
    </div>

    <div v-if="activeTab === 'devices'">
      <div v-if="Object.keys(devices).length === 0" class="text-xs text-muted-foreground-1 py-4 text-center">Нет данных</div>
      <div v-else class="space-y-3">
        <div v-for="(count, type) in devices" :key="type" class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <DashboardIcon :name="deviceIcon(type)" size="5" class="text-muted-foreground-2" />
            <span class="text-xs font-medium text-foreground">{{ deviceLabel(type) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-24 h-1.5 bg-muted-hover rounded-full overflow-hidden">
              <div class="h-full bg-primary/60 rounded-full" :style="{ width: getDevicePercent(count) + '%' }"></div>
            </div>
            <span class="text-[11px] text-muted-foreground-1 w-12 text-right">{{ getDevicePercent(count) }}%</span>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="activeTab === 'browsers'">
      <div v-if="Object.keys(browsers).length === 0" class="text-xs text-muted-foreground-1 py-4 text-center">Нет данных</div>
      <div v-else class="space-y-2">
        <div v-for="(count, name) in browsers" :key="name" class="flex items-center justify-between">
          <span class="text-xs font-medium text-foreground">{{ name }}</span>
          <div class="flex items-center gap-2">
            <div class="w-20 h-1.5 bg-muted-hover rounded-full overflow-hidden">
              <div class="h-full bg-primary/60 rounded-full" :style="{ width: getBrowserPercent(count) + '%' }"></div>
            </div>
            <span class="text-[11px] text-muted-foreground-1 w-8 text-right">{{ count }}</span>
          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <div v-if="Object.keys(oses).length === 0" class="text-xs text-muted-foreground-1 py-4 text-center">Нет данных</div>
      <div v-else class="space-y-2">
        <div v-for="(count, name) in oses" :key="name" class="flex items-center justify-between">
          <span class="text-xs font-medium text-foreground">{{ name }}</span>
          <div class="flex items-center gap-2">
            <div class="w-20 h-1.5 bg-muted-hover rounded-full overflow-hidden">
              <div class="h-full bg-primary/60 rounded-full" :style="{ width: getOsPercent(count) + '%' }"></div>
            </div>
            <span class="text-[11px] text-muted-foreground-1 w-8 text-right">{{ count }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../DashboardIcon.vue';

export default {
  name: 'AnalyticsDevices',
  components: { DashboardIcon },
  props: {
    devices: { type: Object, required: true },
    browsers: { type: Object, required: true },
    oses: { type: Object, required: true },
  },
  data() {
    return {
      activeTab: 'devices',
      tabs: [
        { key: 'devices', label: 'Устройства' },
        { key: 'browsers', label: 'Браузеры' },
        { key: 'oses', label: 'ОС' },
      ],
    };
  },
  computed: {
    totalDevices() {
      return Object.values(this.devices).reduce((a, b) => a + b, 0) || 1;
    },
    maxBrowser() {
      return Math.max(...Object.values(this.browsers), 1);
    },
    maxOs() {
      return Math.max(...Object.values(this.oses), 1);
    },
  },
  methods: {
    deviceIcon(type) {
      const map = { desktop: 'computer-desktop', mobile: 'device-phone-mobile', tablet: 'device-tablet' };
      return map[type] || 'computer-desktop';
    },
    deviceLabel(type) {
      const map = { desktop: 'Десктоп', mobile: 'Мобильные', tablet: 'Планшеты' };
      return map[type] || type;
    },
    getDevicePercent(count) {
      return Math.round((count / this.totalDevices) * 100);
    },
    getBrowserPercent(count) {
      return Math.round((count / this.maxBrowser) * 100);
    },
    getOsPercent(count) {
      return Math.round((count / this.maxOs) * 100);
    },
  },
};
</script>
