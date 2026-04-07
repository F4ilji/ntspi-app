<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="pencil-square" size="5" class="text-primary" />
    </template>
    <template #header-title>Редактирование научного журнала</template>
    <template #header-subtitle>Изменение данных журнала "{{ journal.title }}"</template>
    <template #header-actions>
      <a
        :href="route('dashboard.academic-journals.index')"
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
          <!-- Основные данные -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-foreground">Основные данные</h3>

            <div class="grid grid-cols-1 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  Название журнала <span class="text-danger">*</span>
                </label>
                <input
                  v-model="form.title"
                  type="text"
                  required
                  maxlength="255"
                  placeholder="Введите полное название журнала"
                  class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  @input="generateSlug"
                />
                <p class="mt-1 text-xs text-muted-foreground-1">Официальное название журнала как в регистрационных документах</p>
                <p v-if="errors.title" class="mt-1 text-sm text-danger">{{ errors.title }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  URL-адрес <span class="text-danger">*</span>
                </label>
                <div class="flex items-center">
                  <span class="inline-flex items-center px-3 py-2 border border-r-0 border-layer-line rounded-l-lg bg-muted text-muted-foreground-1 text-sm">
                    {{ baseUrl }}/
                  </span>
                  <input
                    v-model="form.slug"
                    type="text"
                    required
                    readonly
                    class="flex-1 px-3 py-2 border border-layer-line rounded-r-lg bg-muted text-muted-foreground-1 text-sm cursor-not-allowed"
                  />
                </div>
                <p class="mt-1 text-xs text-muted-foreground-1">Формируется автоматически из названия</p>
                <p v-if="errors.slug" class="mt-1 text-sm text-danger">{{ errors.slug }}</p>
              </div>
            </div>
          </div>

          <!-- Основная информация (Content Builder) -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-foreground">Описание журнала</h3>
            <ContentBuilder
              v-model="form.main_info"
              label="Описание журнала"
            />
            <p class="text-xs text-muted-foreground-1">Добавьте полное описание журнала, его историю и основные направления</p>
          </div>

          <!-- Редакционная коллегия -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-foreground">Редакционная коллегия</h3>

            <!-- Главный редактор -->
            <div class="border border-layer-line rounded-lg p-4">
              <h4 class="text-sm font-medium text-foreground mb-3">Главный редактор</h4>
              <div class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">
                      ФИО <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.chief_editor[0].name"
                      type="text"
                      maxlength="255"
                      placeholder="Иванов Иван Иванович"
                      class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">
                      Учёная степень <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.chief_editor[0].academicTitle"
                      type="text"
                      maxlength="255"
                      placeholder="д.т.н., профессор"
                      class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">
                      Должность <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.chief_editor[0].position"
                      type="text"
                      maxlength="255"
                      placeholder="Главный научный сотрудник"
                      class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">
                      Учреждение <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.chief_editor[0].institution"
                      type="text"
                      maxlength="255"
                      placeholder="МГУ имени М.В. Ломоносова"
                      class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Редакционная коллегия -->
            <div class="border border-layer-line rounded-lg p-4">
              <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-medium text-foreground">Члены редакционной коллегии</h4>
                <button
                  type="button"
                  @click="addEditor"
                  class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-all"
                >
                  <DashboardIcon name="plus" size="4" />
                  Добавить редактора
                </button>
              </div>

              <div class="space-y-3">
                <div
                  v-for="(editor, index) in form.editors"
                  :key="index"
                  class="bg-muted/30 border border-layer-line rounded-lg p-3"
                >
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-foreground">Редактор #{{ index + 1 }}</span>
                    <button
                      type="button"
                      @click="removeEditor(index)"
                      class="text-muted-foreground-1 hover:text-danger transition-colors"
                    >
                      <DashboardIcon name="x-mark" size="4" />
                    </button>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-foreground mb-1">
                        ФИО <span class="text-danger">*</span>
                      </label>
                      <input
                        v-model="editor.name"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="Петров Петр Петрович"
                        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                      />
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-foreground mb-1">
                        Учёная степень <span class="text-danger">*</span>
                      </label>
                      <input
                        v-model="editor.academicTitle"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="к.ф.-м.н., доцент"
                        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                      />
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-foreground mb-1">
                        Должность <span class="text-danger">*</span>
                      </label>
                      <input
                        v-model="editor.position"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="Доцент кафедры"
                        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                      />
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-foreground mb-1">
                        Учреждение <span class="text-danger">*</span>
                      </label>
                      <input
                        v-model="editor.institution"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="СПбГУ"
                        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                      />
                    </div>
                  </div>
                </div>

                <p v-if="form.editors.length === 0" class="text-xs text-muted-foreground-1 text-center py-4">
                  Добавьте членов редакционной коллегии журнала
                </p>
              </div>
            </div>
          </div>

          <!-- Информация для авторов (Content Builder) -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-foreground">Информация для авторов</h3>
            <ContentBuilder
              v-model="form.for_authors"
              label="Требования к статьям"
            />
            <p class="text-xs text-muted-foreground-1">Разместите требования к статьям, правила оформления и сроки подачи</p>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-surface/50 border-t border-layer-line flex items-center justify-end gap-3">
          <a
            :href="route('dashboard.academic-journals.index')"
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
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import ContentBuilder from '../Components/ContentBuilder/ContentBuilder.vue';

export default {
  name: 'AcademicJournalEdit',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
    ContentBuilder,
  },

  props: {
    journal: {
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
        title: this.journal.title || '',
        slug: this.journal.slug || '',
        main_info: this.journal.main_info || [],
        chief_editor: (this.journal.chief_editor && this.journal.chief_editor.length > 0)
          ? this.journal.chief_editor
          : [{ name: '', academicTitle: '', position: '', institution: '' }],
        editors: this.journal.editors || [],
        for_authors: this.journal.for_authors || []
      },
      processing: false,
      baseUrl: this.GET_BASE_URL() + 'academic-journals'
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Редактирование научного журнала');
  },

  methods: {
    generateSlug() {
      if (this.form.title) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },

    addEditor() {
      this.form.editors.push({
        name: '',
        academicTitle: '',
        position: '',
        institution: ''
      });
    },

    removeEditor(index) {
      this.form.editors.splice(index, 1);
    },

    submit() {
      this.processing = true;
      this.$inertia.put(route('dashboard.academic-journals.update', this.journal.id), this.form, {
        onFinish: () => {
          this.processing = false;
        }
      });
    }
  }
}
</script>
