<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import DeployButton from './components/DeployButton.vue'
import DeployProgress from './components/DeployProgress.vue'
import DeployLogs from './components/DeployLogs.vue'

const status = ref({
  running: false,
  step: 0,
  total_steps: 12,
  current_step: '',
  logs: [],
  error: null,
})

let pollingInterval = null

const getCsrfToken = () => {
  const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
  return match ? decodeURIComponent(match[1]) : ''
}

const headers = {
  'Content-Type': 'application/json',
  'X-XSRF-TOKEN': getCsrfToken(),
  'Accept': 'application/json',
}

const fetchStatus = async () => {
  try {
    const response = await fetch('/api/deploy/status', {
      credentials: 'same-origin',
      headers,
    })
    status.value = await response.json()

    if (!status.value.running && pollingInterval) {
      clearInterval(pollingInterval)
      pollingInterval = null
    }
  } catch (error) {
    console.error('Failed to fetch status:', error)
  }
}

const startDeploy = async () => {
  try {
    const response = await fetch('/api/deploy', {
      method: 'POST',
      credentials: 'same-origin',
      headers,
    })
    const data = await response.json()

    if (response.ok) {
      startPolling()
    } else {
      alert(data.error || 'Failed to start deploy')
    }
  } catch (error) {
    alert('Failed to start deploy')
  }
}

const startPolling = () => {
  if (pollingInterval) return
  pollingInterval = setInterval(fetchStatus, 2000)
}

onMounted(() => {
  fetchStatus()
})

onUnmounted(() => {
  if (pollingInterval) {
    clearInterval(pollingInterval)
  }
})
</script>

<template>
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Deploy</h1>

    <DeployButton
      :running="status.running"
      @deploy="startDeploy"
    />

    <DeployProgress
      v-if="status.running || status.step > 0"
      :step="status.step"
      :total-steps="status.total_steps"
      :current-step="status.current_step"
      :success="status.success"
      :error="status.error"
    />

    <DeployLogs :logs="status.logs" />
  </div>
</template>
