<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="building-office" size="5" class="text-primary" />
    </template>
    <template #header-title>
      <div class="flex items-center gap-2">
        <span>Редактирование кафедры</span>
        <span class="text-muted-foreground-1">/</span>
        <span class="text-primary">{{ department.title }}</span>
      </div>
    </template>
    <template #header-subtitle>
      <div class="flex items-center gap-3">
        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-primary/10 text-primary border border-primary/20">
          {{ department.faculty?.abbreviation || department.faculty?.title || '—' }}
        </span>
        <span class="text-muted-foreground-1">•</span>
        <span :class="department.is_active ? 'text-success' : 'text-danger'" class="flex items-center gap-1">
          <DashboardIcon v-if="department.is_active" name="check" size="3" class="text-success" />
          <DashboardIcon v-else name="x-mark" size="3" class="text-danger" />
          {{ department.is_active ? 'Активная' : 'Неактивная' }}
        </span>
      </div>
    </template>
    <template #header-actions>
      <a
        :href="route('dashboard.departments.index')"
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
                  Полное название <span class="text-danger">*</span>
                </label>
                <input
                  v-model="form.title"
                  type="text"
                  required
                  maxlength="255"
                  placeholder="Например: Кафедра программной инженерии"
                  class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
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

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-foreground mb-1">
                    Факультет <span class="text-danger">*</span>
                  </label>
                  <select
                    v-model="form.faculty_id"
                    required
                    class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  >
                    <option value="">Выберите факультет</option>
                    <option v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
                      {{ faculty.title }}
                    </option>
                  </select>
                  <p v-if="errors.faculty_id" class="mt-1 text-sm text-danger">{{ errors.faculty_id }}</p>
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
                      Активная кафедра
                    </label>
                  </div>
                  <p class="mt-1 text-xs text-muted-foreground-1">Отображать ли кафедру на сайте</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Контент -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-foreground">Контент</h3>
            <ContentBuilder
              v-model="form.content"
              label="Содержимое страницы"
            />
          </div>

          <!-- Сотрудники кафедры -->
          <DepartmentWorkers
            v-if="department.id"
            :department="department"
            :workers="workers"
            :available-users="availableWorkers"
          />

          <!-- Преподаватели кафедры -->
          <DepartmentTeachers
            v-if="department.id"
            :department="department"
            :teachers="teachers"
            :available-users="availableTeachers"
          />

          <!-- Образовательные программы -->
          <DepartmentPrograms
            v-if="department.id"
            :department="department"
            :programs="programs"
            :available-programs="availablePrograms"
          />
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-surface/50 border-t border-layer-line flex items-center justify-end gap-3">
          <a
            :href="route('dashboard.departments.index')"
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
import DepartmentWorkers from './Workers/Index.vue';
import DepartmentTeachers from './Teachers/Index.vue';
import DepartmentPrograms from './Programs/Index.vue';

export default {
  name: 'DepartmentEdit',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
    ContentBuilder,
    DepartmentWorkers,
    DepartmentTeachers,
    DepartmentPrograms,
  },

  props: {
    department: {
      type: Object,
      required: true
    },
    faculties: {
      type: Array,
      required: true
    },
    workers: {
      type: Object,
      default: () => ({ data: [], total: 0 })
    },
    availableWorkers: {
      type: Array,
      default: () => []
    },
    teachers: {
      type: Object,
      default: () => ({ data: [], total: 0 })
    },
    availableTeachers: {
      type: Array,
      default: () => []
    },
    programs: {
      type: Object,
      default: () => ({ data: [], total: 0 })
    },
    availablePrograms: {
      type: Array,
      default: () => []
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      form: {
        title: this.department.title || '',
        slug: this.department.slug || '',
        faculty_id: this.department.faculty_id || '',
        is_active: Boolean(this.department.is_active ?? true),
        content: this.department.content || []
      },
      processing: false,
      baseUrl: this.GET_BASE_URL() + 'department'
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование кафедры - ${this.department.title}`);
  },

  methods: {
    submit() {
      this.processing = true;
      this.$inertia.put(route('dashboard.departments.update', this.department.id), this.form, {
        onFinish: () => {
          this.processing = false;
        }
      });
    }
  }
}
</script>
