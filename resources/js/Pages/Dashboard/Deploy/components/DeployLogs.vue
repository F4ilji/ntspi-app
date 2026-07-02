<script setup>
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
  logs: Array,
})

const logsContainer = ref(null)

watch(() => props.logs, async () => {
  await nextTick()
  if (logsContainer.value) {
    logsContainer.value.scrollTop = logsContainer.value.scrollHeight
  }
}, { deep: true })
</script>

<template>
  <div class="mt-6">
    <h3 class="text-lg font-medium mb-3">Logs</h3>
    <div
      ref="logsContainer"
      class="bg-gray-900 text-green-400 p-4 rounded-lg h-64 overflow-y-auto font-mono text-sm"
    >
      <div v-if="logs.length === 0" class="text-gray-500">
        No logs yet...
      </div>
      <div v-for="(log, index) in logs" :key="index">
        {{ log }}
      </div>
    </div>
  </div>
</template>
