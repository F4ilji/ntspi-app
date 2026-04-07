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
              <DashboardIcon name="pencil-square" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Редактирование расписания</h1>
              <p class="text-xs text-muted-foreground-1">{{ schedule.file?.[0]?.title || 'Расписание' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Flash Messages -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-rose-500/10 border border-rose-500/20 rounded-lg">
          <div class="flex items-start gap-3">
            <DashboardIcon name="exclamation-circle" size="5" class="text-rose-600 flex-shrink-0 mt-0.5" />
            <span class="text-sm text-foreground font-medium">{{ $page.props.flash.error }}</span>
          </div>
        </div>
      </transition>

      <!-- Form Card -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
        <div class="p-6 border-b border-line-2">
          <div class="flex items-center gap-2">
            <DashboardIcon name="document" size="5" class="text-primary" />
            <h2 class="text-base font-medium text-foreground">Основная информация</h2>
          </div>
          <p class="text-xs text-muted-foreground-1 mt-1">
            Измените данные о расписании
          </p>
        </div>

        <form @submit.prevent="submit" class="p-6">
          <div class="space-y-6">
            <!-- Educational Group -->
            <div>
              <label for="educational_group_id" class="block text-sm font-medium text-foreground mb-2">
                Учебная группа <span class="text-rose-500">*</span>
              </label>
              <select
                id="educational_group_id"
                v-model="form.educational_group_id"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.educational_group_id ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              >
                <option value="">Выберите группу</option>
                <option v-for="group in educationalGroups" :key="group.id" :value="group.id">
                  {{ group.title }}
                </option>
              </select>
              <p v-if="errors.educational_group_id" class="mt-1.5 text-xs text-rose-500">{{ errors.educational_group_id }}</p>
            </div>

            <!-- File Upload -->
            <div>
              <label class="block text-sm font-medium text-foreground mb-2">
                Файл расписания
              </label>
              <div class="space-y-3">
                <!-- Current File Info -->
                <div v-if="currentFilePath" class="p-3 bg-surface border border-layer-line rounded-lg">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 flex-shrink-0 rounded-lg bg-rose-500/10 flex items-center justify-center">
                        <DashboardIcon name="document" size="5" class="text-rose-500" />
                      </div>
                      <div>
                        <p class="text-sm font-medium text-foreground">Текущий файл</p>
                        <a
                          :href="getPdfUrl(currentFilePath)"
                          target="_blank"
                          class="text-xs text-primary hover:underline"
                        >
                          Скачать текущий файл
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Title Input -->
                <input
                  v-model="form.file[0].title"
                  type="text"
                  placeholder="Название файла"
                  :class="[
                    'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                    errors['file.0.title'] ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                  ]"
                />
                <p v-if="errors['file.0.title']" class="text-xs text-rose-500">{{ errors['file.0.title'] }}</p>

                <!-- File Input -->
                <div
                  class="relative border-2 border-dashed rounded-lg p-6 transition-all"
                  :class="[
                    isDragging ? 'border-primary bg-primary/5' : 'border-layer-line hover:border-primary/50',
                    errors['file.0.path'] ? 'border-rose-500' : ''
                  ]"
                  @dragover.prevent="isDragging = true"
                  @dragleave="isDragging = false"
                  @drop.prevent="handleDrop"
                >
                  <input
                    ref="fileInput"
                    type="file"
                    accept=".pdf,application/pdf"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    @change="handleFileChange"
                  />
                  <div class="text-center">
                    <DashboardIcon name="cloud-arrow-up" size="12" class="mx-auto text-muted-foreground-2" />
                    <p class="mt-2 text-sm text-foreground">
                      <span class="font-medium text-primary">Нажмите для загрузки</span> нового файла или перетащите
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground-1">
                      Только PDF файлы, макс. размер 10MB. Оставьте пустым, чтобы сохранить текущий файл.
                    </p>
                  </div>
                </div>

                <!-- Selected File Preview -->
                <div v-if="selectedFile" class="flex items-center gap-3 p-3 bg-surface border border-layer-line rounded-lg">
                  <div class="w-10 h-10 flex-shrink-0 rounded-lg bg-rose-500/10 flex items-center justify-center">
                    <DashboardIcon name="document" size="5" class="text-rose-500" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-foreground truncate">{{ selectedFile.name }}</p>
                    <p class="text-xs text-muted-foreground-1">{{ formatFileSize(selectedFile.size) }}</p>
                  </div>
                  <button
                    type="button"
                    @click="removeFile"
                    class="p-2 text-muted-foreground-1 hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all"
                  >
                    <DashboardIcon name="x-mark" size="4" />
                  </button>
                </div>

                <p v-if="errors['file.0.path']" class="text-xs text-rose-500">{{ errors['file.0.path'] }}</p>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
            <a
              :href="route('dashboard.schedules.index')"
              class="inline-flex items-center gap-2 px-4 py-2.5 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all"
            >
              Отмена
            </a>
            <button
              type="submit"
              :disabled="processing"
              class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              <DashboardIcon v-else name="check" size="4" />
              {{ processing ? 'Сохранение...' : 'Сохранить изменения' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../Components/DashboardIcon.vue';

export default {
  name: 'ScheduleEdit',
  components: {
    DashboardIcon,
  },

  props: {
    schedule: {
      type: Object,
      required: true
    },
    educationalGroups: {
      type: Array,
      required: true
    },
    educationForms: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      form: {
        educational_group_id: this.schedule?.educational_group_id || '',
        file: [
          {
            title: this.schedule?.file?.[0]?.title || '',
            path: null
          }
        ]
      },
      currentFilePath: this.schedule?.file?.[0]?.path || null,
      selectedFile: null,
      isDragging: false,
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование расписания - ${this.schedule?.educational_group?.title}`);
  },

  methods: {
    handleFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.validateFile(file);
      }
    },

    handleDrop(event) {
      this.isDragging = false;
      const file = event.dataTransfer.files[0];
      if (file) {
        this.validateFile(file);
      }
    },

    validateFile(file) {
      // Проверка типа файла
      if (file.type !== 'application/pdf') {
        this.errors['file.0.path'] = 'Разрешены только PDF файлы';
        return;
      }

      // Проверка размера (10MB)
      if (file.size > 10 * 1024 * 1024) {
        this.errors['file.0.path'] = 'Размер файла не должен превышать 10MB';
        return;
      }

      this.errors['file.0.path'] = '';
      this.selectedFile = file;
      this.form.file[0].path = file;

      // Автозаполнение названия если пустое
      if (!this.form.file[0].title) {
        this.form.file[0].title = file.name.replace('.pdf', '');
      }
    },

    removeFile() {
      this.selectedFile = null;
      this.form.file[0].path = null;
      this.$refs.fileInput.value = '';
    },

    formatFileSize(bytes) {
      if (!bytes) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    },

    getPdfUrl(path) {
      if (!path) return '#';
      return `/storage/${path}`;
    },

    submit() {
      if (!this.form.educational_group_id) {
        this.errors.educational_group_id = 'Необходимо выбрать учебную группу';
        return;
      }

      this.processing = true;
      this.errors = {};

      const formData = new FormData();
      formData.append('educational_group_id', this.form.educational_group_id);

      if (this.form.file[0].title) {
        formData.append('file[0][title]', this.form.file[0].title);
      }

      if (this.form.file[0].path) {
        formData.append('file[0][path]', this.form.file[0].path);
      }

      this.$inertia.post(route('dashboard.schedules.update', this.schedule.id), formData, {
        _method: 'PUT',
        onFinish: () => {
          this.processing = false;
        },
        onError: (errors) => {
          this.errors = errors;
        }
      });
    },

    route(name, params) {
      return route(name, params);
    }
  }
}
</script>
