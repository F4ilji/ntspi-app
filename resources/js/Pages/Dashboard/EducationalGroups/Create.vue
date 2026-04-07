<template>
  <div class="min-h-screen bg-background-2">
    <!-- Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.educational-groups.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="plus" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Создание учебной группы</h1>
              <p class="text-xs text-muted-foreground-1">Заполните информацию о новой группе</p>
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
            <DashboardIcon name="x-circle" size="5" class="text-rose-600 flex-shrink-0 mt-0.5" />
            <span class="text-sm text-foreground font-medium">{{ $page.props.flash.error }}</span>
          </div>
        </div>
      </transition>

      <!-- Form Card -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
        <div class="p-6 border-b border-line-2">
          <div class="flex items-center gap-2">
            <DashboardIcon name="document-text" size="5" class="text-primary" />
            <h2 class="text-base font-medium text-foreground">Основная информация</h2>
          </div>
          <p class="text-xs text-muted-foreground-1 mt-1">
            Заполните данные о учебной группе
          </p>
        </div>

        <form @submit.prevent="submit" class="p-6">
          <div class="space-y-6">
            <!-- Title -->
            <div>
              <label for="title" class="block text-sm font-medium text-foreground mb-2">
                Название группы <span class="text-rose-500">*</span>
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                placeholder="Например: ИВТ-21-1"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.title ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.title" class="mt-1.5 text-xs text-rose-500">{{ errors.title }}</p>
            </div>

            <!-- Faculty -->
            <div>
              <label for="faculty_id" class="block text-sm font-medium text-foreground mb-2">
                Факультет <span class="text-rose-500">*</span>
              </label>
              <select
                id="faculty_id"
                v-model="form.faculty_id"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.faculty_id ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              >
                <option value="">Выберите факультет</option>
                <option v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
                  {{ faculty.title }}
                </option>
              </select>
              <p v-if="errors.faculty_id" class="mt-1.5 text-xs text-rose-500">{{ errors.faculty_id }}</p>
            </div>

            <!-- Education Form -->
            <div>
              <label for="education_form_id" class="block text-sm font-medium text-foreground mb-2">
                Форма обучения <span class="text-rose-500">*</span>
              </label>
              <select
                id="education_form_id"
                v-model="form.education_form_id"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.education_form_id ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              >
                <option value="">Выберите форму обучения</option>
                <option v-for="formEducation in educationForms" :key="formEducation.value" :value="formEducation.value">
                  {{ formEducation.label }}
                </option>
              </select>
              <p v-if="errors.education_form_id" class="mt-1.5 text-xs text-rose-500">{{ errors.education_form_id }}</p>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
            <a
              :href="route('dashboard.educational-groups.index')"
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
              {{ processing ? 'Сохранение...' : 'Создать группу' }}
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
  name: 'EducationalGroupCreate',
  components: {
    DashboardIcon
  },

  props: {
    faculties: {
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
        title: '',
        faculty_id: '',
        education_form_id: ''
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Создание учебной группы');
  },

  methods: {
    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.post(route('dashboard.educational-groups.store'), this.form, {
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
