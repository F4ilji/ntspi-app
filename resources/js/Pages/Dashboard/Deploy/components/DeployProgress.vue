<script setup>
import { computed } from 'vue'

const props = defineProps({
  step: Number,
  totalSteps: Number,
  currentStep: String,
  success: Boolean,
  error: String,
})

const progress = computed(() => (props.step / props.totalSteps) * 100)
</script>

<template>
  <div class="mt-6 p-4 bg-white rounded-lg shadow">
    <div class="mb-2 flex justify-between text-sm">
      <span>{{ currentStep }}</span>
      <span>{{ step }}/{{ totalSteps }}</span>
    </div>

    <div class="w-full bg-gray-200 rounded-full h-3">
      <div
        class="h-3 rounded-full transition-all duration-500"
        :class="{
          'bg-blue-600': !success && !error,
          'bg-green-600': success,
          'bg-red-600': error
        }"
        :style="{ width: progress + '%' }"
      />
    </div>

    <div v-if="error" class="mt-3 p-3 bg-red-100 text-red-700 rounded">
      ❌ {{ error }}
    </div>

    <div v-else-if="success" class="mt-3 p-3 bg-green-100 text-green-700 rounded">
      ✅ Deploy completed successfully
    </div>
  </div>
</template>
