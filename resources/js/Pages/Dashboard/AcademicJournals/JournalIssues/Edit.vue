<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="pencil-square" size="5" class="text-primary" />
    </template>
    <template #header-title>Редактирование выпуска журнала</template>
    <template #header-subtitle>{{ journal.title }} — {{ issue.title }}</template>
    <template #header-actions>
      <a
        :href="route('dashboard.academic-journals.issues.index', journal.id)"
        class="inline-flex items-center gap-2 px-4 py-2 bg-surface text-foreground text-sm font-medium rounded-lg border border-layer-line hover:bg-muted-hover transition-all duration-200"
      >
        <DashboardIcon name="arrow-left" size="4" />
        Назад к списку
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Form Card -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <form @submit.prevent="submit">
        <div class="p-6 space-y-6">
          <!-- Основные поля -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-foreground">Основная информация</h3>

            <div class="grid grid-cols-1 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  Название выпуска <span class="text-danger">*</span>
                </label>
                <input
                  v-model="form.title"
                  type="text"
                  required
                  maxlength="255"
                  placeholder='Например: "Том 15, №3 (2023)"'
                  class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
                <p class="mt-1 text-xs text-muted-foreground-1">Например: "Том 15, №3 (2023)" или специальное название выпуска</p>
                <p v-if="errors.title" class="mt-1 text-sm text-danger">{{ errors.title }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  Год публикации <span class="text-danger">*</span>
                </label>
                <input
                  v-model.number="form.year_publication"
                  type="number"
                  required
                  :min="1900"
                  :max="maxYear"
                  placeholder="Укажите год выпуска"
                  class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
                <p class="mt-1 text-xs text-muted-foreground-1">Год должен быть в диапазоне от 1900 до {{ maxYear }}</p>
                <p v-if="errors.year_publication" class="mt-1 text-sm text-danger">{{ errors.year_publication }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  Файл выпуска <span class="text-danger">*</span>
                </label>

                <!-- Текущий файл -->
                <div v-if="form.existing_file" class="mb-2 p-3 bg-muted/30 border border-layer-line rounded-lg">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <DashboardIcon name="document-text" size="5" class="text-muted-foreground-1" />
                      <div>
                        <p class="text-sm font-medium text-foreground">{{ getFileName(form.existing_file) }}</p>
                        <a
                          :href="RESOLVE_ASSET_URL(form.existing_file)"
                          target="_blank"
                          class="text-xs text-primary hover:underline"
                        >
                          Открыть файл
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <input
                  ref="fileInput"
                  type="file"
                  @change="handleFileChange"
                  accept=".pdf,.docx,.xlsx,.pptx,.zip"
                  class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
                <p class="mt-1 text-xs text-muted-foreground-1">
                  Оставьте пустым, чтобы сохранить текущий файл. Максимальный размер: 256MB.
                </p>
                <p v-if="fileError" class="mt-1 text-sm text-danger">{{ fileError }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  Статус
                </label>
                <div class="flex items-center mt-2">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 text-primary focus:ring-primary border-layer-line rounded"
                  />
                  <label class="ml-2 text-sm text-foreground">
                    Активный выпуск
                  </label>
                </div>
                <p class="mt-1 text-xs text-muted-foreground-1">Активные выпуски отображаются на сайте</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-surface/50 border-t border-layer-line flex items-center justify-end gap-3">
          <a
            :href="route('dashboard.academic-journals.issues.index', journal.id)"
            class="px-4 py-2 text-sm font-medium text-foreground bg-surface border border-layer-line rounded-lg hover:bg-muted-hover transition-all"
          >
            Отмена
          </a>
          <button
            type="submit"
            :disabled="processing"
            class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ processing ? 'Сохранение...' : 'Сохранить изменения' }}
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../Components/DashboardLayout.vue';
import DashboardIcon from '../../Components/DashboardIcon.vue';
import FlashMessages from '../../Components/shared/FlashMessages.vue';

export default {
  name: 'JournalIssueEdit',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
  },

  props: {
    journal: {
      type: Object,
      required: true
    },
    issue: {
      type: Object,
      required: true
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      form: {
        title: this.issue.title || '',
        year_publication: this.issue.year_publication || new Date().getFullYear(),
        path_file: null,
        existing_file: this.issue.path_file || null,
        is_active: this.issue.is_active ?? true
      },
      fileError: null,
      processing: false,
      maxYear: new Date().getFullYear() + 1
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Редактирование выпуска журнала');
  },

  methods: {
    getFileName(path) {
      if (!path) return '';
      return path.split('/').pop();
    },

    handleFileChange(event) {
      const file = event.target.files[0];
      this.fileError = null;

      if (!file) {
        this.form.path_file = null;
        return;
      }

      // Проверка размера (256MB)
      const maxSize = 256 * 1024 * 1024;
      if (file.size > maxSize) {
        this.fileError = 'Размер файла не должен превышать 256MB';
        event.target.value = '';
        return;
      }

      // Проверка формата
      const allowedTypes = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/zip'
      ];

      const allowedExtensions = ['.pdf', '.docx', '.xlsx', '.pptx', '.zip'];
      const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

      if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
        this.fileError = 'Неподдерживаемый формат файла. Допустимые: PDF, DOCX, XLSX, PPTX, ZIP';
        event.target.value = '';
        return;
      }

      this.form.path_file = file;
    },

    submit() {
      this.processing = true;

      // Создаем FormData для загрузки файла
      const formData = new FormData();
      formData.append('title', this.form.title);
      formData.append('year_publication', this.form.year_publication);
      formData.append('is_active', this.form.is_active ? 1 : 0);
      formData.append('_method', 'PUT'); // Laravel method spoofing

      if (this.form.path_file instanceof File) {
        formData.append('path_file', this.form.path_file);
      } else if (this.form.existing_file) {
        formData.append('path_file', this.form.existing_file);
      }

      this.$inertia.post(
        route('dashboard.academic-journals.issues.update', {
          academicJournal: this.journal.id,
          issue: this.issue.id
        }),
        formData,
        {
          forceFormData: true,
          onFinish: () => {
            this.processing = false;
          }
        }
      );
    }
  }
}
</script>
