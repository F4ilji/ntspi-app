<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import DashboardLayout from '../Components/DashboardLayout.vue'
import FlashMessages from '../Components/shared/FlashMessages.vue'

const props = defineProps({
  history: { type: Array, default: () => [] },
  status: { type: Object, default: () => ({}) },
})

const deployStatus = ref(props.status)
const deployLog = ref('')
const isDeploying = ref(false)
const isPolling = ref(false)
let pollInterval = null

const statusColors = {
  idle: 'text-gray-500',
  running: 'text-blue-600',
  completed: 'text-green-600',
  failed: 'text-red-600',
  unknown: 'text-yellow-600',
  disabled: 'text-gray-400',
}

const statusLabels = {
  idle: 'Ожидание',
  running: 'Выполняется',
  completed: 'Завершён',
  failed: 'Ошибка',
  unknown: 'Неизвестно',
  disabled: 'Отключено',
}

const statusBgColors = {
  idle: 'bg-gray-100',
  running: 'bg-blue-100',
  completed: 'bg-green-100',
  failed: 'bg-red-100',
  unknown: 'bg-yellow-100',
  disabled: 'bg-gray-100',
}

async function startDeploy() {
  if (!confirm('Запустить деплой? Приложение будет обновлено.')) {
    return
  }

  try {
    const response = await fetch(route('dashboard.deploy'), {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-XSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
      },
    })

    const data = await response.json()

    if (data.success) {
      isDeploying.value = true
      deployStatus.value = { status: 'running', message: 'Деплой запущен...' }
      startPolling()
    } else {
      alert(data.message || 'Ошибка запуска деплоя')
    }
  } catch (error) {
    console.error('Deploy error:', error)
    alert('Ошибка соединения с сервером: ' + error)
  }
}

function startPolling() {
  if (isPolling.value) return
  isPolling.value = true

  pollInterval = setInterval(async () => {
    try {
      const statusRes = await fetch(route('dashboard.deploy.status'), {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' },
      })
      const statusData = await statusRes.json()
      deployStatus.value = statusData

      const logRes = await fetch(route('dashboard.deploy.log') + '?lines=100', {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' },
      })
      const logData = await logRes.json()
      deployLog.value = logData.full_log || logData.log || ''

      if (statusData.status !== 'running') {
        stopPolling()
        isDeploying.value = false
        router.reload({ only: ['history'] })
      }
    } catch (error) {
      console.error('Polling error:', error)
    }
  }, 2000)
}

function stopPolling() {
  if (pollInterval) {
    clearInterval(pollInterval)
    pollInterval = null
  }
  isPolling.value = false
}

async function clearLog() {
  try {
    await fetch(route('dashboard.deploy.clear'), {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'X-XSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
      },
    })
    deployLog.value = ''
    deployStatus.value = { status: 'idle', message: 'Деплой не запущен' }
  } catch (error) {
    console.error('Clear error:', error)
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const d = new Date(dateStr)
  return d.toLocaleString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(() => {
  if (deployStatus.value.status === 'running') {
    startPolling()
  }
  if (deployStatus.value.log) {
    deployLog.value = deployStatus.value.log
  }
})

onUnmounted(() => {
  stopPolling()
})
</script>

<template>
  <DashboardLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <FlashMessages />

        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Деплой сайта</h1>
          <p class="mt-1 text-sm text-gray-600">
            Управление обновлением сайта на production сервере
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Статус</h3>
            <span
              :class="[
                'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                statusBgColors[deployStatus.status] || 'bg-gray-100',
                statusColors[deployStatus.status] || 'text-gray-500',
              ]"
            >
              {{ statusLabels[deployStatus.status] || deployStatus.status }}
            </span>
            <p class="mt-2 text-sm text-gray-600">{{ deployStatus.message }}</p>
          </div>

          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Действия</h3>
            <div class="flex gap-3">
              <button
                @click="startDeploy"
                :disabled="isDeploying || deployStatus.status === 'running'"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="isDeploying" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ isDeploying ? 'Выполняется...' : 'Запустить деплой' }}
              </button>
              <button
                @click="clearLog"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Очистить лог
              </button>
            </div>
          </div>

          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Информация</h3>
            <dl class="space-y-1">
              <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Деплоев выполнено:</dt>
                <dd class="font-medium text-gray-900">{{ history.length }}</dd>
              </div>
              <div v-if="history.length > 0" class="flex justify-between text-sm">
                <dt class="text-gray-500">Последний деплой:</dt>
                <dd class="font-medium text-gray-900">{{ formatDate(history[0]?.timestamp) }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <div v-if="deployLog" class="bg-white shadow rounded-lg mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Лог деплоя</h3>
          </div>
          <div class="p-6">
            <pre class="bg-gray-900 text-green-400 rounded-lg p-4 overflow-auto max-h-96 text-sm font-mono whitespace-pre-wrap">{{ deployLog }}</pre>
          </div>
        </div>

        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">История деплоев</h3>
          </div>
          <div v-if="history.length === 0" class="p-6 text-center text-gray-500">
            Деплои ещё не выполнялись
          </div>
          <table v-else class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Коммит</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Запущен</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(item, index) in history" :key="index">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(item.timestamp) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      item.status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
                    ]"
                  >
                    {{ item.status === 'success' ? 'Успешно' : 'Ошибка' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ item.commit }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.triggered_by }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>
