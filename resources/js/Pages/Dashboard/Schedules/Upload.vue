<template>
  <div class="min-h-screen bg-background-2">
    <!-- Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.schedules.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="cloud-arrow-up" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Быстрая загрузка расписаний</h1>
              <p class="text-xs text-muted-foreground-1">Массовая загрузка файлов с автоматическим определением групп</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Results Section (показываем сверху после загрузки) -->
      <div v-if="showResults" class="mb-6 space-y-4">
        <!-- Success Results -->
        <div v-if="processedFiles.length > 0" class="bg-emerald-500/5 border border-emerald-500/20 rounded-lg shadow-xs">
          <div class="p-4 border-b border-emerald-500/20">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <DashboardIcon name="check-circle" size="5" class="text-emerald-500" />
                <h3 class="text-sm font-semibold text-emerald-700">
                  Успешно загружено: {{ processedFiles.length }}
                </h3>
              </div>
              <a
                :href="route('dashboard.schedules.index')"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-all"
              >
                <DashboardIcon name="list-bullet" size="4" />
                Перейти к расписаниям
              </a>
            </div>
          </div>
          <div class="p-4">
            <div class="space-y-2">
              <div
                v-for="(file, index) in processedFiles"
                :key="index"
                class="flex items-center gap-3 p-3 bg-white rounded-lg border border-emerald-500/20"
              >
                <div class="w-10 h-10 flex-shrink-0 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                  <DashboardIcon name="check-circle" size="5" class="text-emerald-600" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-foreground truncate">{{ file.filename }}</p>
                  <p class="text-xs text-emerald-600 font-medium">
                    → {{ file.group }}
                  </p>
                </div>
                <div class="flex-shrink-0">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                    Загружено
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Failed Files -->
        <div v-if="failedFiles.length > 0" class="bg-rose-500/5 border border-rose-500/20 rounded-lg shadow-xs">
          <div class="p-4 border-b border-rose-500/20">
            <div class="flex items-center gap-2">
              <DashboardIcon name="exclamation-circle" size="5" class="text-rose-500" />
              <h3 class="text-sm font-semibold text-rose-700">
                Ошибки при загрузке: {{ failedFiles.length }}
              </h3>
            </div>
          </div>
          <div class="p-4">
            <div class="space-y-2">
              <div
                v-for="(file, index) in failedFiles"
                :key="index"
                class="flex items-start gap-3 p-3 bg-white rounded-lg border border-rose-500/20"
              >
                <div class="w-10 h-10 flex-shrink-0 rounded-lg bg-rose-500/10 flex items-center justify-center">
                  <DashboardIcon name="x-mark" size="5" class="text-rose-600" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-foreground truncate">{{ file.filename }}</p>
                  <p class="text-xs text-rose-600 mt-1">{{ file.error }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-4">
          <div class="flex items-center justify-between">
            <p class="text-sm text-foreground">
              <span class="font-medium">Итого обработано:</span> {{ processedFiles.length + failedFiles.length }} файлов
            </p>
            <button
              type="button"
              @click="resetResults"
              class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-medium rounded-lg transition-all"
            >
              <DashboardIcon name="arrow-path" size="4" />
              Загрузить ещё
            </button>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="flashMessage && !showResults" :class="flashMessageClasses" class="mb-4 p-4 rounded-lg border">
          <div class="flex items-start gap-3">
            <DashboardIcon v-if="flashMessageType === 'success'" name="check-circle" size="5" class="text-emerald-600 flex-shrink-0 mt-0.5" />
            <DashboardIcon v-else-if="flashMessageType === 'error'" name="exclamation-circle" size="5" class="text-rose-600 flex-shrink-0 mt-0.5" />
            <DashboardIcon v-else name="exclamation-triangle" size="5" class="text-amber-600 flex-shrink-0 mt-0.5" />
            <span class="text-sm text-foreground font-medium">{{ flashMessage }}</span>
          </div>
        </div>
      </transition>

      <!-- Info Card -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs mb-6">
        <div class="p-4 border-b border-line-2">
          <div class="flex items-center gap-2">
            <DashboardIcon name="information-circle" size="5" class="text-primary" />
            <h2 class="text-base font-medium text-foreground">Как это работает</h2>
          </div>
        </div>
        <div class="p-4">
          <ul class="space-y-2 text-sm text-foreground">
            <li class="flex items-start gap-2">
              <DashboardIcon name="check" size="4" class="text-primary mt-0.5 flex-shrink-0" />
              <span>Загрузите несколько PDF файлов одновременно</span>
            </li>
            <li class="flex items-start gap-2">
              <DashboardIcon name="check" size="4" class="text-primary mt-0.5 flex-shrink-0" />
              <span>Система автоматически определит учебную группу по названию файла</span>
            </li>
            <li class="flex items-start gap-2">
              <DashboardIcon name="check" size="4" class="text-primary mt-0.5 flex-shrink-0" />
              <span>Название файла должно содержать код группы (например: "Нт-102 Экзамены.pdf")</span>
            </li>
            <li class="flex items-start gap-2">
              <DashboardIcon name="check" size="4" class="text-primary mt-0.5 flex-shrink-0" />
              <span>Максимальный размер файла: 10MB</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Upload Form -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
        <div class="p-6 border-b border-line-2">
          <div class="flex items-center gap-2">
            <DashboardIcon name="cloud-arrow-up" size="5" class="text-primary" />
            <h2 class="text-base font-medium text-foreground">Загрузка файлов</h2>
          </div>
          <p class="text-xs text-muted-foreground-1 mt-1">
            Выберите PDF файлы для загрузки
          </p>
        </div>

        <div class="p-6">
          <!-- Drop Zone -->
          <div
            class="relative border-2 border-dashed rounded-lg p-8 transition-all cursor-pointer"
            :class="[
              isDragging ? 'border-primary bg-primary/5' : 'border-layer-line hover:border-primary/50',
              errors.files ? 'border-rose-500' : ''
            ]"
            @dragover.prevent="isDragging = true"
            @dragleave="isDragging = false"
            @drop.prevent="handleDrop"
            @click="$refs.fileInput.click()"
          >
            <input
              ref="fileInput"
              type="file"
              accept=".pdf,application/pdf"
              multiple
              class="hidden"
              @change="handleFileChange"
            />
            <div class="text-center">
              <DashboardIcon name="cloud-arrow-up" size="16" class="mx-auto text-muted-foreground-2" />
              <p class="mt-4 text-sm text-foreground">
                <span class="font-medium text-primary">Нажмите для выбора</span> или перетащите файлы
              </p>
              <p class="mt-2 text-xs text-muted-foreground-1">
                Только PDF файлы, макс. размер 10MB каждый
              </p>
            </div>
          </div>

          <!-- Selected Files List -->
          <div v-if="selectedFiles.length > 0" class="mt-6">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-sm font-medium text-foreground">
                Выбрано файлов: {{ selectedFiles.length }}
              </h3>
              <button
                type="button"
                @click="clearAllFiles"
                class="text-xs text-rose-500 hover:text-rose-600 hover:underline"
              >
                Очистить все
              </button>
            </div>
            <div class="space-y-2 max-h-64 overflow-y-auto">
              <div
                v-for="(file, index) in selectedFiles"
                :key="index"
                class="flex items-center gap-3 p-3 bg-surface border border-layer-line rounded-lg"
              >
                <div class="w-10 h-10 flex-shrink-0 rounded-lg bg-rose-500/10 flex items-center justify-center">
                  <DashboardIcon name="document" size="5" class="text-rose-500" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-foreground truncate">{{ file.name }}</p>
                  <p class="text-xs text-muted-foreground-1">{{ formatFileSize(file.size) }}</p>
                </div>
                <button
                  type="button"
                  @click.stop="removeFile(index)"
                  class="p-2 text-muted-foreground-1 hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all"
                >
                  <DashboardIcon name="x-mark" size="4" />
                </button>
              </div>
            </div>
          </div>

          <!-- Error Message -->
          <p v-if="errors.files" class="mt-2 text-xs text-rose-500">{{ errors.files }}</p>

          <!-- Submit Button -->
          <div class="flex items-center justify-end mt-6 pt-6 border-t border-line-2">
            <button
              type="button"
              @click="submit"
              :disabled="processing || selectedFiles.length === 0"
              class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              <DashboardIcon v-else name="cloud-arrow-up" size="4" />
              {{ processing ? 'Загрузка...' : 'Загрузить файлы' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../Components/DashboardIcon.vue';

export default {
  name: 'UploadSchedules',
  components: {
    DashboardIcon,
  },

  props: {
    flash: {
      type: Object,
      default: null
    },
    data: {
      type: Object,
      default: null
    }
  },

  data() {
    return {
      selectedFiles: [],
      isDragging: false,
      processing: false,
      errors: {},
      flashMessage: null,
      flashMessageType: 'success',
      showResults: false,
      processedFiles: [],
      failedFiles: []
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Загрузка расписаний');
  },

  watch: {
    flash: {
      handler(newFlash) {
        if (newFlash) {
          this.flashMessage = newFlash.message;
          this.flashMessageType = newFlash.type;
        }
      },
      immediate: true
    },
    data: {
      handler(newData) {
        if (newData) {
          this.processedFiles = newData.processed || [];
          this.failedFiles = newData.failed || [];
          this.showResults = this.processedFiles.length > 0 || this.failedFiles.length > 0;

          // Очищаем выбранные файлы после загрузки
          if (this.processedFiles.length > 0 || this.failedFiles.length > 0) {
            this.selectedFiles = [];
          }
        }
      },
      immediate: true
    }
  },

  computed: {
    flashMessageClasses() {
      const baseClasses = 'border';
      const typeClasses = {
        success: 'bg-emerald-500/10 border-emerald-500/20',
        error: 'bg-rose-500/10 border-rose-500/20',
        warning: 'bg-amber-500/10 border-amber-500/20'
      };
      return `${baseClasses} ${typeClasses[this.flashMessageType]}`;
    }
  },

  methods: {
    handleFileChange(event) {
      const files = Array.from(event.target.files);
      this.addFiles(files);
      event.target.value = '';
    },

    handleDrop(event) {
      this.isDragging = false;
      const files = Array.from(event.dataTransfer.files);
      this.addFiles(files);
    },

    addFiles(files) {
      const validFiles = [];
      const errors = [];

      files.forEach(file => {
        if (file.type !== 'application/pdf') {
          errors.push(`Файл "${file.name}" не является PDF`);
          return;
        }

        if (file.size > 10 * 1024 * 1024) {
          errors.push(`Файл "${file.name}" превышает 10MB`);
          return;
        }

        validFiles.push(file);
      });

      if (errors.length > 0) {
        this.errors.files = errors.join('. ');
      } else {
        this.errors.files = '';
      }

      this.selectedFiles = [...this.selectedFiles, ...validFiles];
    },

    removeFile(index) {
      this.selectedFiles.splice(index, 1);
    },

    clearAllFiles() {
      this.selectedFiles = [];
      this.errors.files = '';
    },

    resetResults() {
      this.showResults = false;
      this.processedFiles = [];
      this.failedFiles = [];
      this.flashMessage = null;
      this.flashMessageType = 'success';
      this.errors = {};
      // Прокручиваем к началу страницы
      this.$nextTick(() => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    },

    formatFileSize(bytes) {
      if (!bytes) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    },

    async submit() {
      if (this.selectedFiles.length === 0) {
        this.errors.files = 'Выберите файлы для загрузки';
        return;
      }

      this.processing = true;
      this.errors = {};
      this.showResults = false;
      this.processedFiles = [];
      this.failedFiles = [];

      const formData = new FormData();
      this.selectedFiles.forEach(file => {
        formData.append('files[]', file);
      });

      this.$inertia.post(route('dashboard.schedules.upload.store'), formData, {
        preserveScroll: true,
        onError: (errors) => {
          this.errors = { ...errors };
          const firstError = Object.values(errors)[0];
          this.flashMessage = firstError || 'Ошибка при загрузке файлов';
          this.flashMessageType = 'error';
        },
        onFinish: () => {
          this.processing = false;
        }
      });
    },

    route(name, params) {
      return route(name, params);
    }
  }
}
</script>
