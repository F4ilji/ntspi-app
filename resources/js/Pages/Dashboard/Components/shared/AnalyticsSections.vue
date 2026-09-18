<template>
  <div>
    <div v-if="items.length === 0" class="text-xs text-muted-foreground-1 py-4 text-center">Нет данных</div>
    <div v-else class="space-y-1">
      <a
        v-for="item in items"
        :key="item.prefix"
        :href="sectionUrl(item.prefix)"
        class="relative flex items-center justify-between py-2 px-2 rounded-md overflow-hidden hover:bg-muted-hover transition-colors group"
      >
        <div
          class="absolute inset-y-0 left-0 bg-primary/5 rounded-md"
          :style="{ width: getWidth(item.views) + '%' }"
        ></div>
        <div class="flex items-center gap-2 min-w-0 flex-1 relative">
          <span class="text-xs font-medium text-foreground">{{ item.label }}</span>
          <svg class="w-3 h-3 text-muted-foreground-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0 relative">
          <span class="text-[11px] text-muted-foreground-1">{{ item.views }}</span>
          <span class="text-[10px] text-muted-foreground-2">{{ item.visitors }} uniq</span>
        </div>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AnalyticsSections',
  props: {
    items: { type: Array, required: true },
    period: { type: Number, default: 30 },
  },
  computed: {
    maxViews() {
      return Math.max(...this.items.map(i => i.views), 1);
    },
  },
  methods: {
    getWidth(views) {
      return Math.max((views / this.maxViews) * 100, 2);
    },
    sectionUrl(prefix) {
      return route('dashboard.analytics.section', { prefix, days: this.period });
    },
  },
};
</script>
