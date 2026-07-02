<script setup>
defineProps({
  running: Boolean,
})

defineEmits(['deploy'])
</script>

<template>
  <button
    @click="$emit('deploy')"
    :disabled="running"
    class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium
           hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed
           transition-colors"
  >
    <span v-if="running">Deploying...</span>
    <span v-else>🚀 Deploy to Production</span>
  </button>
</template>
