<template>
  <div class="space-y-2">
    <div v-if="items.length === 0" class="text-xs text-muted-foreground-1 py-4 text-center">Нет данных</div>
    <div v-for="item in items" :key="item.source" class="group">
      <div class="flex items-center justify-between mb-1">
        <span class="text-xs font-medium text-foreground truncate">{{ item.source }}</span>
        <span class="text-xs text-muted-foreground-1 ml-2 flex-shrink-0">{{ item.hits }}</span>
      </div>
      <div class="h-1.5 bg-muted-hover rounded-full overflow-hidden">
        <div
          class="h-full bg-primary/60 rounded-full transition-all"
          :style="{ width: getWidth(item.hits) + '%' }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AnalyticsReferrers',
  props: {
    items: { type: Array, required: true },
  },
  computed: {
    maxHits() {
      return Math.max(...this.items.map(i => i.hits), 1);
    },
  },
  methods: {
    getWidth(hits) {
      return Math.max((hits / this.maxHits) * 100, 2);
    },
  },
};
</script>
