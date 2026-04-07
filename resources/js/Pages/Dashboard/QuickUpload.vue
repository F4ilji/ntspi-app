<template>
  <DashboardLayout>
    <template #header-title>Быстрая загрузка файла</template>
    <template #header-subtitle>Загрузите файл и получите публичную ссылку</template>
    <template #header-icon>
      <DashboardIcon name="cloud-arrow-up" size="5" class="text-primary" />
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
          <h2 class="text-lg font-medium text-foreground mb-4">Загрузка файла</h2>
          <p class="text-sm text-muted-foreground-1 mb-6">
            Загрузите один файл. После загрузки вы сможете скопировать его публичный URL.
            Максимальный размер файла: 20 МБ.
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
              accept="application/pdf,image/*,video/*,audio/*,text/plain,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            />
            
            <div v-if="!selectedFile" class="space-y-3">
              <DashboardIcon name="cloud-arrow-up" size="12" class="mx-auto text-muted-foreground-1" />
              <div>
                <p class="text-sm font-medium text-foreground">
                  <span class="text-primary">Нажмите для загрузки</span> или перетащите файл
                </p>
                <p class="text-xs text-muted-foreground-1 mt-1">
                  PDF, изображения, видео, аудио, документы (макс. 20 МБ)
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
              <DashboardIcon v-else name="cloud-arrow-up" size="4" />
              {{ uploading ? 'Загрузка...' : 'Загрузить файл' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Result Card -->
      <div v-if="uploadedFileUrl" class="mt-6 bg-layer border border-layer-line rounded-lg shadow-sm">
        <div class="p-6">
          <h2 class="text-lg font-medium text-foreground mb-4">Результат загрузки</h2>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-muted-foreground-1 mb-2">Публичный URL</label>
              <div class="flex gap-2">
                <input
                  type="text"
                  :value="uploadedFileUrl"
                  readonly
                  class="flex-1 px-3 py-2 bg-background-2 border border-layer-line rounded-lg text-sm text-foreground"
                />
                <button
                  type="button"
                  class="inline-flex items-center gap-2 px-3 py-2 bg-primary/10 text-primary font-medium rounded-lg hover:bg-primary/20 transition-all duration-200"
                  @click="copyToClipboard"
                >
                  <DashboardIcon name="clipboard" size="4" />
                  {{ copied ? 'Скопировано!' : 'Копировать' }}
                </button>
              </div>
            </div>

            <div v-if="uploadedFileName" class="text-xs text-muted-foreground-1">
              <span class="font-medium">Имя файла:</span> {{ uploadedFileName }}
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
import { router } from '@inertiajs/vue3';

defineOptions({
  name: 'QuickUpload',
  components: {
    DashboardIcon,
  },
});

onMounted(() => {
  document.title = 'Быстрая загрузка файлов | Dashboard';
});

const fileInput = ref(null);
const selectedFile = ref(null);
const isDragging = ref(false);
const uploading = ref(false);
const uploadedFileUrl = ref(null);
const uploadedFileName = ref(null);
const successMessage = ref(null);
const errorMessage = ref(null);
const copied = ref(false);

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedFile.value = file;
    errorMessage.value = null;
  }
};

const handleDrop = (event) => {
  isDragging.value = false;
  const file = event.dataTransfer.files[0];
  if (file) {
    selectedFile.value = file;
    errorMessage.value = null;
  }
};

const clearFile = () => {
  selectedFile.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const upload = async () => {
  if (!selectedFile.value) return;

  uploading.value = true;
  errorMessage.value = null;
  successMessage.value = null;

  const formData = new FormData();
  formData.append('file', selectedFile.value);

  try {
    const response = await fetch(route('dashboard.quick-upload.store'), {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest',
      },
    });

    const result = await response.json();

    if (result.success) {
      uploadedFileUrl.value = result.data.url;
      uploadedFileName.value = result.data.original_name;
      successMessage.value = `Файл "${result.data.original_name}" успешно загружен`;
      selectedFile.value = null;
      if (fileInput.value) {
        fileInput.value.value = '';
      }
    } else {
      errorMessage.value = result.message || 'Ошибка при загрузке файла';
    }
  } catch (error) {
    errorMessage.value = 'Ошибка при загрузке файла. Попробуйте ещё раз.';
    console.error('Upload error:', error);
  } finally {
    uploading.value = false;
  }
};

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(uploadedFileUrl.value);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (error) {
    console.error('Copy error:', error);
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
