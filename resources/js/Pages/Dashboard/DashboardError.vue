<template>
  <Head>
    <title>Ошибка {{ status }} — Панель управления</title>
  </Head>

  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="exclamation-triangle" size="5" class="text-red-500" />
    </template>
    <template #header-title>Ошибка {{ status }}</template>
    <template #header-subtitle>{{ statusDescription }}</template>

    <div class="space-y-6">
      <!-- Error Code Card -->
      <div class="bg-layer rounded-xl border border-layer-line p-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-red-50 rounded-xl flex items-center justify-center">
            <span class="text-3xl font-bold text-red-600">{{ status }}</span>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-foreground">{{ statusDescription }}</h2>
            <p class="text-sm text-muted-foreground-1">{{ message }}</p>
          </div>
        </div>
      </div>

      <!-- Request Info Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- URL -->
        <div class="bg-layer rounded-xl border border-layer-line p-4">
          <h3 class="text-xs font-semibold text-muted-foreground-1 uppercase tracking-wide mb-2">
            URL запроса
          </h3>
          <p class="text-sm text-foreground font-mono break-all">{{ url }}</p>
        </div>

        <!-- Method -->
        <div class="bg-layer rounded-xl border border-layer-line p-4">
          <h3 class="text-xs font-semibold text-muted-foreground-1 uppercase tracking-wide mb-2">
            HTTP Метод
          </h3>
          <p class="text-sm text-foreground font-mono">{{ method }}</p>
        </div>

        <!-- User -->
        <div class="bg-layer rounded-xl border border-layer-line p-4">
          <h3 class="text-xs font-semibold text-muted-foreground-1 uppercase tracking-wide mb-2">
            Пользователь
          </h3>
          <p v-if="user" class="text-sm text-foreground">{{ user.name }} ({{ user.email }})</p>
          <p v-else class="text-sm text-muted-foreground-2">Не авторизован</p>
        </div>

        <!-- Timestamp -->
        <div class="bg-layer rounded-xl border border-layer-line p-4">
          <h3 class="text-xs font-semibold text-muted-foreground-1 uppercase tracking-wide mb-2">
            Время ошибки
          </h3>
          <p class="text-sm text-foreground">{{ formattedTimestamp }}</p>
        </div>
      </div>

      <!-- Stack Trace (non-production only) -->
      <div v-if="stackTrace" class="bg-layer rounded-xl border border-layer-line p-4">
        <button
          @click="showStack = !showStack"
          class="flex items-center gap-2 text-sm font-semibold text-foreground hover:text-primary transition-colors"
        >
          <DashboardIcon
            :name="showStack ? 'chevron-down' : 'chevron-right'"
            size="4"
          />
          Stack Trace
        </button>
        <div v-if="showStack" class="mt-4 overflow-x-auto">
          <pre class="text-xs text-muted-foreground-1 font-mono whitespace-pre-wrap bg-background-2 rounded-lg p-4">{{ stackTrace }}</pre>
        </div>
      </div>

      <!-- Request Params (non-production only) -->
      <div v-if="requestParams && Object.keys(requestParams).length" class="bg-layer rounded-xl border border-layer-line p-4">
        <button
          @click="showParams = !showParams"
          class="flex items-center gap-2 text-sm font-semibold text-foreground hover:text-primary transition-colors"
        >
          <DashboardIcon
            :name="showParams ? 'chevron-down' : 'chevron-right'"
            size="4"
          />
          Параметры запроса
        </button>
        <div v-if="showParams" class="mt-4 overflow-x-auto">
          <pre class="text-xs text-muted-foreground-1 font-mono whitespace-pre-wrap bg-background-2 rounded-lg p-4">{{ JSON.stringify(requestParams, null, 2) }}</pre>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-3">
        <button
          @click="goBack"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-layer border border-layer-line rounded-lg text-sm font-medium text-foreground hover:bg-primary-50 hover:text-primary transition-all"
        >
          <DashboardIcon name="arrow-left" size="4" />
          Назад
        </button>
        <a
          :href="route('dashboard.index')"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-light transition-all"
        >
          <DashboardIcon name="home" size="4" />
          На главную
        </a>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from './Components/DashboardLayout.vue';
import DashboardIcon from './Components/DashboardIcon.vue';

const props = defineProps({
  status: { type: Number, required: true },
  message: { type: String, default: '' },
  url: { type: String, default: '' },
  method: { type: String, default: 'GET' },
  stackTrace: { type: String, default: null },
  requestParams: { type: Object, default: () => ({}) },
  user: { type: Object, default: null },
  timestamp: { type: String, default: '' },
});

const showStack = ref(false);
const showParams = ref(false);

const statusDescription = computed(() => ({
  503: 'Сервис временно недоступен',
  500: 'Внутренняя ошибка сервера',
  404: 'Страница не найдена',
  403: 'Доступ запрещён',
})[props.status] || 'Неизвестная ошибка');

const formattedTimestamp = computed(() => {
  if (!props.timestamp) return '—';
  return new Date(props.timestamp).toLocaleString('ru-RU', {
    dateStyle: 'full',
    timeStyle: 'long',
  });
});

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    window.location.href = route('dashboard.index');
  }
};
</script>
