<template>
  <div class="flex items-center gap-3 px-4 h-16 border-b border-layer-line">
    <div class="w-9 h-9 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
      <DashboardIcon name="document-text" size="5" class="text-primary" />
    </div>
    <div class="flex flex-col">
      <span class="text-sm font-semibold text-foreground">NTSPI</span>
      <span class="text-xs text-muted-foreground-1">Dashboard</span>
    </div>
  </div>
</template>

<script setup>
import DashboardIcon from './DashboardIcon.vue';
</script>
