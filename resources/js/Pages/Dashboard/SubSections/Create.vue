<template>
  <div class="min-h-screen bg-background-2">
    <!-- Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.sub-sections.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="plus" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Создание подраздела</h1>
              <p class="text-xs text-muted-foreground-1">Заполните информацию о новом подразделе</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Flash Messages -->
      <FlashMessages />

      <!-- Form Card -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
        <div class="p-6 border-b border-line-2">
          <div class="flex items-center gap-2">
            <DashboardIcon name="document-text" size="5" class="text-primary" />
            <h2 class="text-base font-medium text-foreground">Основная информация</h2>
          </div>
          <p class="text-xs text-muted-foreground-1 mt-1">
            Заполните данные о подразделе
          </p>
        </div>

        <form @submit.prevent="submit" class="p-6">
          <div class="space-y-6">
            <!-- Title -->
            <div>
              <label for="title" class="block text-sm font-medium text-foreground mb-2">
                Название подраздела <span class="text-rose-500">*</span>
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                @input="generateSlug"
                placeholder="Например: Бакалавриат"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.title ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.title" class="mt-1.5 text-xs text-rose-500">{{ errors.title }}</p>
            </div>

            <!-- Slug -->
            <div>
              <label for="slug" class="block text-sm font-medium text-foreground mb-2">
                Текстовый идентификатор <span class="text-rose-500">*</span>
              </label>
              <input
                id="slug"
                v-model="form.slug"
                type="text"
                placeholder="Например: bachelor"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.slug ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.slug" class="mt-1.5 text-xs text-rose-500">{{ errors.slug }}</p>
              <p class="mt-1.5 text-xs text-muted-foreground-1">
                Автоматически генерируется из названия. Можно изменить.
              </p>
            </div>

            <!-- Main Section -->
            <div>
              <label for="main_section_id" class="block text-sm font-medium text-foreground mb-2">
                Главный раздел
              </label>
              <select
                id="main_section_id"
                v-model="form.main_section_id"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.main_section_id ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              >
                <option value="">Без главного раздела</option>
                <option v-for="(title, id) in mainSections" :key="id" :value="id">
                  {{ title }}
                </option>
              </select>
              <p v-if="errors.main_section_id" class="mt-1.5 text-xs text-rose-500">{{ errors.main_section_id }}</p>
              <p class="mt-1.5 text-xs text-muted-foreground-1">
                Можно привязать подраздел к главному разделу или оставить без привязки
              </p>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
            <a
              :href="route('dashboard.sub-sections.index')"
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
              {{ processing ? 'Сохранение...' : 'Создать подраздел' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

export default {
  name: 'SubSectionCreate',
  components: {
    DashboardIcon,
    FlashMessages
  },

  props: {
    mainSections: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      form: {
        title: '',
        slug: '',
        main_section_id: ''
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Создание подраздела');
  },

  methods: {
    generateSlug() {
      if (!this.form.slug) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },

    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.post(route('dashboard.sub-sections.store'), this.form, {
        onFinish: () => {
          this.processing = false;
        },
        onError: (errors) => {
          this.errors = errors;
        }
      });
    }
  }
}
</script>
