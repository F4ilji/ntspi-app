<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="arrow-down-tray" size="5" class="text-primary" />
    </template>
    <template #header-title>Обновления VIKON</template>
    <template #header-subtitle>Управление модулями</template>

    <FlashMessages />

    <!-- Не авторизован -->
    <div v-if="!isAuthenticated" class="bg-layer border border-layer-line rounded-lg p-8 text-center">
      <DashboardIcon name="shield-exclamation" size="16" class="text-muted-foreground-1 mx-auto mb-4" />
      <h3 class="text-lg font-medium mb-2">Требуется авторизация VIKON</h3>
      <p class="text-sm text-muted-foreground-1 mb-6">Войдите через систему VIKON для управления обновлениями</p>
      <a :href="authUrl" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-hover">
        <DashboardIcon name="arrow-right-on-rectangle" size="5" />
        Войти через VIKON
      </a>
    </div>

    <!-- Авторизован -->
    <div v-else class="space-y-6">
      <!-- Версия + Статус -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-layer border border-layer-line rounded-lg p-6">
          <div class="flex items-center gap-3 mb-4">
            <DashboardIcon name="information-circle" size="6" class="text-primary" />
            <div>
              <h3 class="text-sm font-medium">Текущая версия</h3>
              <p class="text-2xl font-semibold mt-1">{{ currentVersion }}</p>
            </div>
          </div>
          <div v-if="versionInfo.has_update" class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg mt-4">
            <p class="text-sm font-medium text-yellow-800">Доступно обновление: {{ versionInfo.latest_version }}</p>
          </div>
          <div v-else-if="versionInfo.latest_version" class="p-3 bg-green-50 border border-green-200 rounded-lg mt-4">
            <p class="text-sm font-medium text-green-800">Установлена последняя версия</p>
          </div>
          <button @click="checkVersion" :disabled="checkingVersion"
            class="mt-4 w-full px-4 py-2 bg-surface border border-layer-line rounded-lg hover:bg-muted-hover disabled:opacity-50">
            {{ checkingVersion ? 'Проверка...' : 'Проверить обновления' }}
          </button>
        </div>

        <div class="bg-layer border border-layer-line rounded-lg p-6">
          <div class="flex items-center gap-3 mb-4">
            <DashboardIcon name="shield-check" size="6" class="text-primary" />
            <div>
              <h3 class="text-sm font-medium">Статус доступа</h3>
              <p class="text-sm text-muted-foreground-1 mt-1">{{ accessInfo.has_access ? 'Доступ разрешён' : 'Доступ запрещён' }}</p>
            </div>
          </div>
          <div v-if="accessInfo.error" class="p-3 bg-red-50 border border-red-200 rounded-lg mt-4">
            <p class="text-sm text-red-800">{{ accessInfo.error }}</p>
          </div>
          <button @click="checkAccess" :disabled="checkingAccess"
            class="mt-4 w-full px-4 py-2 bg-surface border border-layer-line rounded-lg hover:bg-muted-hover disabled:opacity-50">
            {{ checkingAccess ? 'Проверка...' : 'Проверить права' }}
          </button>
        </div>
      </div>

      <!-- Модули -->
      <div class="bg-layer border border-layer-line rounded-lg">
        <div class="px-6 py-4 border-b border-layer-line">
          <h3 class="text-sm font-medium">Модули для обновления</h3>
        </div>
        <div class="divide-y divide-line-2">
          <div v-for="(mod, id) in modules" :key="id" class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <DashboardIcon name="cube" size="6" class="text-primary" />
              <div>
                <p class="text-sm font-medium">{{ mod.name }}</p>
                <p class="text-xs text-muted-foreground-1">ID: {{ id }}</p>
              </div>
            </div>
            <button @click="updateModule(id)" :disabled="updating || !accessInfo.has_access"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover disabled:opacity-50">
              {{ updating ? 'Обновление...' : 'Обновить' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Прогресс -->
      <div v-if="updating" class="bg-layer border border-layer-line rounded-lg p-6">
        <div class="flex items-center gap-3 mb-3">
          <DashboardIcon name="arrow-path" size="5" class="text-primary animate-spin" />
          <p class="text-sm font-medium">Обновление...</p>
        </div>
        <div class="w-full bg-muted rounded-full h-2.5">
          <div class="bg-primary h-2.5 rounded-full transition-all" :style="{ width: progress + '%' }"></div>
        </div>
        <p class="text-xs text-muted-foreground-1 mt-2">{{ progress }}%</p>
      </div>

      <!-- Ошибка -->
      <div v-if="updateError" class="bg-layer border border-red-200 rounded-lg p-6">
        <div class="flex items-start gap-3">
          <DashboardIcon name="x-circle" size="6" class="text-red-600" />
          <div>
            <h4 class="text-sm font-medium text-red-800">Ошибка</h4>
            <p class="text-sm text-red-700 mt-1">{{ updateError }}</p>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <button @click="logout" class="px-4 py-2 bg-surface border border-layer-line rounded-lg hover:bg-muted-hover">
          Выйти из VIKON
        </button>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

const props = defineProps({
  is_authenticated: Boolean,
  current_version: String,
  modules: Object,
  vikon_api_domain: String,
  vikon_client_id: String,
});

const isAuthenticated = ref(props.is_authenticated);
const currentVersion = ref(props.current_version);
const checkingVersion = ref(false);
const checkingAccess = ref(false);
const updating = ref(false);
const progress = ref(0);
const updateError = ref(null);
const versionInfo = ref({ current_version: props.current_version, has_update: false, latest_version: null });
const accessInfo = ref({ has_access: false, error: null });

const authUrl = computed(() => {
  const redirect = encodeURIComponent(`${window.location.origin}/vikon_core/update/index.php`);
  return `${props.vikon_api_domain}oauth2/authorize?client_id=${props.vikon_client_id}&redirect_uri=${redirect}&response_type=code`;
});

onMounted(() => {
  const code = new URLSearchParams(window.location.search).get('code');
  if (code) {
    authenticate(code);
    return;
  }
  if (isAuthenticated.value) {
    checkAccess();
    checkVersion();
  }
});

async function authenticate(code) {
  try {
    const res = await axios.post(route('dashboard.vikon-updates.authenticate'), {
      code,
      redirect_uri: `${window.location.origin}/vikon_core/update/index.php`,
    });
    if (res.data.success) {
      isAuthenticated.value = true;
      window.history.replaceState({}, document.title, window.location.pathname);
      checkAccess();
      checkVersion();
    }
  } catch (e) {
    console.error('Auth failed:', e);
  }
}

async function checkAccess() {
  checkingAccess.value = true;
  try {
    const res = await axios.post(route('dashboard.vikon-updates.check-access'));
    accessInfo.value = res.data;
  } catch (e) {
    accessInfo.value = { has_access: false, error: 'Ошибка проверки прав' };
  } finally {
    checkingAccess.value = false;
  }
}

async function checkVersion() {
  checkingVersion.value = true;
  try {
    const res = await axios.post(route('dashboard.vikon-updates.check-version'));
    versionInfo.value = res.data;
  } catch (e) {
    console.error('Version check failed:', e);
  } finally {
    checkingVersion.value = false;
  }
}

async function updateModule(moduleId) {
  if (!confirm('Обновить модуль?')) return;

  updating.value = true;
  progress.value = 0;
  updateError.value = null;

  const timer = setInterval(() => {
    if (progress.value < 90) progress.value += 10;
  }, 500);

  try {
    const res = await axios.post(route('dashboard.vikon-updates.update-module'), { module_id: moduleId });
    progress.value = 100;
    clearInterval(timer);

    if (res.data.success) {
      setTimeout(() => router.reload({ preserveState: true }), 500);
    } else {
      updateError.value = res.data.message;
      updating.value = false;
    }
  } catch (e) {
    clearInterval(timer);
    updating.value = false;
    updateError.value = e.response?.data?.message || 'Ошибка обновления';
  }
}

async function logout() {
  try {
    await axios.post(route('dashboard.vikon-updates.logout'));
    isAuthenticated.value = false;
    accessInfo.value = { has_access: false, error: null };
    versionInfo.value = { current_version: currentVersion.value, has_update: false, latest_version: null };
  } catch (e) {
    console.error('Logout failed:', e);
  }
}
</script>
