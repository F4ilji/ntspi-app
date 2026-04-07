<template>
  <div v-if="data.last_page > 1" class="px-6 py-4 border-t border-line-2 bg-surface/50">
    <div class="flex items-center justify-between">
      <div class="text-sm text-muted-foreground-1">
        Показано <span class="font-medium text-foreground">{{ data.from }}</span> – <span class="font-medium text-foreground">{{ data.to }}</span> из <span class="font-medium text-foreground">{{ data.total }}</span>
      </div>
      <div class="flex items-center gap-2">
        <template v-for="(link, index) in data.links" :key="index">
          <a
            v-if="link.url"
            :href="link.url"
            :class="[
              'px-3 py-1.5 text-sm font-medium rounded-lg border transition-all duration-200',
              link.active
                ? 'bg-primary text-white border-primary'
                : 'bg-surface text-foreground border-layer-line hover:bg-muted-hover hover:border-primary/30'
            ]"
            v-html="link.label"
          ></a>
          <span
            v-else
            class="px-3 py-1.5 text-sm text-muted-foreground-2 bg-surface border border-layer-line rounded-lg"
            v-html="link.label"
          ></span>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    data: {
      type: Object,
      required: true
    }
  }
}
</script>
