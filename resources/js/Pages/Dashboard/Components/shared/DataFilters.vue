<template>
  <div class="bg-layer border border-layer-line rounded-lg shadow-xs mb-6">
    <div class="p-4 border-b border-line-2">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-muted-foreground-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        <h2 class="text-sm font-medium text-foreground">{{ title }}</h2>
      </div>
    </div>
    <div class="p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <slot></slot>
        
        <!-- Reset Button -->
        <div class="flex items-end">
          <button
            type="button"
            @click="$emit('reset')"
            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ resetText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DataFilters',
  props: {
    title: {
      type: String,
      default: 'Фильтры'
    },
    resetText: {
      type: String,
      default: 'Сбросить фильтры'
    }
  },
  emits: ['reset']
}
</script>
