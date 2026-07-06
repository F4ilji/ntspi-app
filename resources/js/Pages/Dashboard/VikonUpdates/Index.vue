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
        <div class="px-6 py-4 border-b border-layer-line flex items-center justify-between">
          <h3 class="text-sm font-medium">Модули для обновления</h3>
          <button @click="updateAllModules" :disabled="updatingAll || !accessInfo.has_access"
            class="px-4 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-hover disabled:opacity-50 flex items-center gap-2">
            <svg v-if="updatingAll" class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <DashboardIcon v-else name="arrow-path" size="4" />
            {{ updatingAll ? 'Обновление...' : 'Обновить все' }}
          </button>
        </div>
        <div class="divide-y divide-line-2">
          <div v-for="(mod, id) in modules" :key="id" class="px-6 py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <DashboardIcon :name="moduleIcon(id)" size="6" class="text-primary" />
                <p class="text-sm font-medium">{{ moduleName(id) }}</p>
              </div>
              <button @click="updateModule(id)" :disabled="updating === id || updatingAll || !accessInfo.has_access || parts[id]?.disabled"
                class="px-4 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-hover disabled:opacity-50">
                <span v-if="updating === id">Обновление...</span>
                <span v-else>Обновить</span>
              </button>
            </div>
            <div v-if="parts && parts[id]?.disabled" class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
              <p class="text-sm text-yellow-800">Обновление модуля запрещено сервером VIKON</p>
            </div>
            <div v-else-if="parts && parts[id]?.parts?.length" class="mt-3">
              <div v-if="updating === id || updatingAll" class="flex items-center gap-2 text-sm text-muted-foreground-1 mb-2">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span>{{ updatePhase }}</span>
              </div>
              <div class="pl-5 space-y-1">
                <div v-for="p in parts[id]?.parts || []" :key="p.id"
                     class="flex items-center gap-2 text-sm"
                     :class="p.access ? '' : 'opacity-50'">
                  <input type="checkbox" :value="p.id" v-model="selectedParts[id]" :disabled="!p.access || updating === id || updatingAll" class="rounded" />
                  <span class="flex-1" :class="p.access ? '' : 'cursor-not-allowed'">{{ p.name }}</span>
                  <span v-if="partStatus[`${id}-${p.id}`] === 'loading'" class="text-primary">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  </span>
                  <span v-else-if="partStatus[`${id}-${p.id}`] === 'success'" class="text-green-600 text-xs">✓</span>
                  <span v-else-if="partStatus[`${id}-${p.id}`] === 'error'" class="text-red-600 text-xs" :title="partErrors[`${id}-${p.id}`]">✗</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ручное обновление -->
      <div class="bg-layer border border-layer-line rounded-lg p-6">
        <div class="flex items-center gap-3 mb-4">
          <DashboardIcon name="cloud-arrow-up" size="6" class="text-primary" />
          <div>
            <h3 class="text-sm font-medium">Ручное обновление</h3>
            <p class="text-xs text-muted-foreground-1">Загрузите ZIP-архив для обновления данных модуля</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <input ref="fileInput" type="file" accept=".zip" class="hidden" @change="handleFileSelect" />
          <button @click="$refs.fileInput.click()" :disabled="uploading"
            class="px-4 py-2 bg-surface border border-layer-line text-sm rounded-lg hover:bg-muted-hover disabled:opacity-50">
            {{ selectedFile ? selectedFile.name : 'Выберите ZIP-архив' }}
          </button>
          <button @click="uploadManual" :disabled="!selectedFile || uploading"
            class="px-4 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-hover disabled:opacity-50 flex items-center gap-2">
            <svg v-if="uploading" class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ uploading ? 'Загрузка...' : 'Загрузить' }}
          </button>
        </div>
        <div v-if="uploadResult" class="mt-4 p-3 rounded-lg" :class="uploadResult.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
          <p class="text-sm" :class="uploadResult.success ? 'text-green-800' : 'text-red-800'">{{ uploadResult.message }}</p>
          <div v-if="uploadResult.updated?.length" class="mt-2 flex flex-wrap gap-1">
            <span v-for="f in uploadResult.updated" :key="f" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">{{ f }}</span>
          </div>
        </div>
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
        <button @click="logout" class="px-4 py-2 bg-surface border border-layer-line rounded-lg hover:bg-muted-hover text-sm">
          Выйти из VIKON
        </button>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

const props = defineProps({
  is_authenticated: Boolean,
  current_version: String,
  modules: Object,
  parts: Object,
  vikon_api_domain: String,
  vikon_client_id: String,
});

const moduleIcons = { 1: 'document-text', 2: 'academic-cap', 6: 'globe-alt' };
const moduleNames = { 1: 'Сведения', 2: 'Абитуриент', 6: 'ВСОКО' };

function moduleIcon(id) { return moduleIcons[id] || 'cube'; }
function moduleName(id) { return moduleNames[id] || `Модуль ${id}`; }

const isAuthenticated = ref(props.is_authenticated);
const currentVersion = ref(props.current_version);
const checkingAccess = ref(false);
const updating = ref(null);
const updatingAll = ref(false);
const updatePhase = ref('');
const updateError = ref(null);
const accessInfo = ref({ has_access: false, error: null });
const selectedParts = ref({});
if (props.parts) {
  for (const [id, moduleData] of Object.entries(props.parts)) {
    if (!moduleData.disabled && moduleData.parts) {
      selectedParts.value[id] = moduleData.parts.filter(p => p.access).map(p => p.id);
    }
  }
}
const partStatus = ref({});
const partErrors = ref({});

const fileInput = ref(null);
const selectedFile = ref(null);
const uploading = ref(false);
const uploadResult = ref(null);

const authUrl = `${props.vikon_api_domain}oauth2/authorize?client_id=${props.vikon_client_id}&redirect_uri=${encodeURIComponent(window.location.origin + '/vikon_core/update/index.php')}&response_type=code`;

onMounted(() => {
  const url = new URL(window.location.href);
  if (url.pathname.endsWith('/callback') && url.searchParams.get('code')) {
    window.history.replaceState({}, document.title, url.pathname);
  }
  if (isAuthenticated.value) {
    checkAccess();
  }
});

async function checkAccess() {
  checkingAccess.value = true;
  try {
    const res = await axios.post(route('dashboard.vikon-updates.check-access'));
    accessInfo.value = res.data;
  } catch {
    accessInfo.value = { has_access: false, error: 'Ошибка проверки прав' };
  } finally {
    checkingAccess.value = false;
  }
}

async function updateModule(moduleId) {
  const partsToUpdate = selectedParts.value[moduleId] || [];
  const msg = partsToUpdate.length
    ? `Обновить модуль и ${partsToUpdate.length} раздел(ов)?`
    : 'Обновить модуль (только ядро и файлы)?';
  if (!confirm(msg)) return;

  updating.value = moduleId;
  updatePhase.value = 'Обновление ядра модуля...';
  updateError.value = null;
  for (const p of partsToUpdate) {
    delete partStatus.value[`${moduleId}-${p}`];
    delete partErrors.value[`${moduleId}-${p}`];
  }

  try {
    const res = await axios.post(route('dashboard.vikon-updates.update-module'), { module_id: moduleId });
    if (!res.data.success) {
      updateError.value = res.data.message;
      return;
    }

    if (partsToUpdate.length) {
      updatePhase.value = 'Обновление разделов...';
      for (const part of partsToUpdate) {
        const key = `${moduleId}-${part}`;
        partStatus.value[key] = 'loading';
        try {
          await axios.post(route('dashboard.vikon-updates.update-part'), { module_id: moduleId, part });
          partStatus.value[key] = 'success';
        } catch (e) {
          partStatus.value[key] = 'error';
          partErrors.value[key] = e.response?.data?.message || e.message;
        }
      }
    }
    alert('Модуль обновлён.');
  } catch (e) {
    updateError.value = e.response?.data?.message || e.message || 'Неизвестная ошибка';
  } finally {
    updating.value = null;
    updatePhase.value = '';
  }
}

async function updateAllModules() {
  if (!confirm('Обновить все модули?')) return;

  updatingAll.value = true;
  updateError.value = null;

  for (const [id] of Object.entries(props.parts || {})) {
    const moduleId = Number(id);
    if (props.parts[id]?.disabled) continue;
    await updateModule(moduleId);
  }

  updatingAll.value = false;
  alert('Все модули обновлены.');
}

function handleFileSelect(e) {
  const file = e.target.files[0];
  if (file) {
    selectedFile.value = file;
    uploadResult.value = null;
  }
}

async function uploadManual() {
  if (!selectedFile.value) return;
  uploading.value = true;
  uploadResult.value = null;

  const formData = new FormData();
  formData.append('archive', selectedFile.value);

  try {
    const res = await fetch(route('dashboard.sveden.store'), {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest',
      },
    });
    const data = await res.json();
    uploadResult.value = data;
    if (data.success) {
      selectedFile.value = null;
      if (fileInput.value) fileInput.value.value = '';
    }
  } catch (e) {
    uploadResult.value = { success: false, message: 'Ошибка загрузки: ' + e.message };
  } finally {
    uploading.value = false;
  }
}

async function logout() {
  try {
    await axios.post(route('dashboard.vikon-updates.logout'));
    isAuthenticated.value = false;
    accessInfo.value = { has_access: false, error: null };
  } catch {}
}
</script>
