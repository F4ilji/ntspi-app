<template>
  <div class="space-y-1.5">
    <div v-if="pages.length === 0" class="text-xs text-muted-foreground-1 py-4 text-center">Нет данных</div>
    <div
      v-for="page in pages"
      :key="page.url"
      class="relative group"
    >
      <div class="absolute inset-0 bg-primary/5 rounded-md" :style="{ width: getWidth(page.views) + '%' }"></div>
      <div class="relative flex items-center justify-between py-1.5 px-2 rounded-md">
        <div class="flex items-center gap-2 min-w-0 flex-1">
          <a
            :href="page.url"
            target="_blank"
            class="text-[11px] font-medium text-foreground truncate hover:text-primary transition-colors"
            :title="page.url"
          >
            {{ page.url }}
          </a>
          <svg class="w-3 h-3 text-muted-foreground-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0 ml-2">
          <span class="text-[11px] text-muted-foreground-1">{{ page.views }}</span>
          <span class="text-[10px] text-muted-foreground-2">{{ page.unique_visitors }} uniq</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AnalyticsTopPages',
  props: {
    pages: { type: Array, required: true },
  },
  computed: {
    maxViews() {
      return Math.max(...this.pages.map(p => p.views), 1);
    },
  },
  methods: {
    getWidth(views) {
      return Math.max((views / this.maxViews) * 100, 2);
    },
  },
};
</script>
