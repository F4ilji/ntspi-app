<template>
  <DashboardLayout>
    <template #header-title>Обновить Sveden</template>
    <template #header-subtitle>Загрузите ZIP-архив для обновления данных</template>
    <template #header-icon>
      <DashboardIcon name="arrow-path" size="5" class="text-primary" />
    </template>

    <div class="max-w-2xl mx-auto">
      <!-- Flash Messages -->
      <transition
        enter-active-class="transition duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="successMessage" class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
          <div class="flex items-start gap-3">
            <DashboardIcon name="check-circle" size="5" class="text-emerald-600 flex-shrink-0 mt-0.5" />
            <p class="text-sm font-medium text-emerald-800">{{ successMessage }}</p>
          </div>
        </div>
      </transition>

      <transition
        enter-active-class="transition duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="errorMessage" class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-lg">
          <div class="flex items-start gap-3">
            <DashboardIcon name="x-circle" size="5" class="text-rose-600 flex-shrink-0 mt-0.5" />
            <p class="text-sm font-medium text-rose-800">{{ errorMessage }}</p>
          </div>
        </div>
      </transition>

      <!-- Upload Card -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-sm">
        <div class="p-6">
          <h2 class="text-lg font-medium text-foreground mb-4">Загрузка архива</h2>
          <p class="text-sm text-muted-foreground-1 mb-6">
            Загрузите ZIP-архив containing папки <code class="px-1.5 py-0.5 bg-background-2 rounded text-xs">sveden</code> и/или <code class="px-1.5 py-0.5 bg-background-2 rounded text-xs">abitur</code>.
            Архив будет распакован, а содержимое папок заменит текущие файлы в <code class="px-1.5 py-0.5 bg-background-2 rounded text-xs">public/</code>.
          </p>

          <!-- Dropzone -->
          <div
            class="relative border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200"
            :class="[
              isDragging
                ? 'border-primary bg-primary/5'
                : 'border-layer-line hover:border-primary/50 hover:bg-primary/5'
            ]"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
          >
            <input
              ref="fileInput"
              type="file"
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              @change="handleFileSelect"
              accept=".zip"
            />

            <div v-if="!selectedFile" class="space-y-3">
              <DashboardIcon name="cloud-arrow-up" size="12" class="mx-auto text-muted-foreground-1" />
              <div>
                <p class="text-sm font-medium text-foreground">
                  <span class="text-primary">Нажмите для загрузки</span> или перетащите ZIP-архив
                </p>
                <p class="text-xs text-muted-foreground-1 mt-1">
                  Только ZIP-файлы (макс. 100 МБ)
                </p>
              </div>
            </div>

            <div v-else class="space-y-3">
              <DashboardIcon name="check-circle" size="12" class="mx-auto text-primary" />
              <div>
                <p class="text-sm font-medium text-foreground">{{ selectedFile.name }}</p>
                <p class="text-xs text-muted-foreground-1 mt-1">{{ formatFileSize(selectedFile.size) }}</p>
              </div>
              <button
                type="button"
                class="text-xs text-primary hover:text-primary/80"
                @click.stop="clearFile"
              >
                Удалить и выбрать другой файл
              </button>
            </div>
          </div>

          <!-- Upload Button -->
          <div class="mt-6 flex justify-end">
            <button
              type="button"
              :disabled="!selectedFile || uploading"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
              @click="upload"
            >
              <DashboardIcon v-if="uploading" name="spinner" size="4" class="animate-spin" />
              <DashboardIcon v-else name="arrow-path" size="4" />
              {{ uploading ? 'Загрузка...' : 'Обновить' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Result Card -->
      <div v-if="result" class="mt-6 bg-layer border border-layer-line rounded-lg shadow-sm">
        <div class="p-6">
          <h2 class="text-lg font-medium text-foreground mb-4">Результат обновления</h2>

          <div class="space-y-4">
            <div v-if="result.updated && result.updated.length > 0">
              <label class="block text-sm font-medium text-muted-foreground-1 mb-2">Обновлено:</label>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="folder in result.updated"
                  :key="folder"
                  class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-800 text-sm font-medium rounded-lg"
                >
                  <DashboardIcon name="check-circle" size="3" class="text-emerald-600" />
                  {{ folder }}
                </span>
              </div>
            </div>

            <div v-if="result.skipped && result.skipped.length > 0">
              <label class="block text-sm font-medium text-muted-foreground-1 mb-2">Пропущено (не sveden/abitur):</label>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="folder in result.skipped"
                  :key="folder"
                  class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 text-amber-800 text-sm font-medium rounded-lg"
                >
                  <DashboardIcon name="exclamation-triangle" size="3" class="text-amber-600" />
                  {{ folder }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from './Components/DashboardLayout.vue';
import DashboardIcon from './Components/DashboardIcon.vue';

defineOptions({
  name: 'Sveden',
});

onMounted(() => {
  document.title = 'Обновить Sveden | Dashboard';
});

const fileInput = ref(null);
const selectedFile = ref(null);
const isDragging = ref(false);
const uploading = ref(false);
const successMessage = ref(null);
const errorMessage = ref(null);
const result = ref(null);

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedFile.value = file;
    errorMessage.value = null;
    result.value = null;
  }
};

const handleDrop = (event) => {
  isDragging.value = false;
  const file = event.dataTransfer.files[0];
  if (file) {
    selectedFile.value = file;
    errorMessage.value = null;
    result.value = null;
  }
};

const clearFile = () => {
  selectedFile.value = null;
  result.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const upload = async () => {
  if (!selectedFile.value) return;

  uploading.value = true;
  errorMessage.value = null;
  successMessage.value = null;
  result.value = null;

  const formData = new FormData();
  formData.append('archive', selectedFile.value);

  try {
    const response = await fetch(route('dashboard.sveden.store'), {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest',
      },
    });

    const data = await response.json();

    if (data.success) {
      result.value = data;
      successMessage.value = 'Архив успешно обновлён';
      selectedFile.value = null;
      if (fileInput.value) {
        fileInput.value.value = '';
      }
    } else {
      errorMessage.value = data.message || 'Ошибка при обновлении';
    }
  } catch (error) {
    const details = error.message || String(error);
    errorMessage.value = 'Ошибка при загрузке файла: ' + details;
    console.error('Upload error:', error);
  } finally {
    uploading.value = false;
  }
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Б';
  const k = 1024;
  const sizes = ['Б', 'КБ', 'МБ', 'ГБ'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};
</script>
