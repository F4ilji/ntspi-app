<template>
  <div class="min-h-screen bg-background-2">
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.additional-educations.directions.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="pencil-square" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Редактирование направления ДПО</h1>
              <p class="text-xs text-muted-foreground-1">{{ direction?.title }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <FlashMessages />

      <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
        <div class="p-6 border-b border-line-2">
          <div class="flex items-center gap-2">
            <DashboardIcon name="document-text" size="5" class="text-primary" />
            <h2 class="text-base font-medium text-foreground">Основная информация</h2>
          </div>
        </div>

        <form @submit.prevent="submit" class="p-6">
          <div class="space-y-6">
            <div>
              <label for="title" class="block text-sm font-medium text-foreground mb-2">
                Название направления <span class="text-rose-500">*</span>
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                @blur="generateSlug"
                placeholder='Например: "Информационные технологии"'
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.title ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.title" class="mt-1.5 text-xs text-rose-500">{{ errors.title }}</p>
            </div>

            <div>
              <label for="slug" class="block text-sm font-medium text-foreground mb-2">
                URL-идентификатор <span class="text-rose-500">*</span>
              </label>
              <input
                id="slug"
                v-model="form.slug"
                type="text"
                readonly
                :class="[
                  'w-full px-4 py-2.5 bg-muted/30 border rounded-lg text-sm text-foreground cursor-not-allowed',
                  errors.slug ? 'border-rose-500' : 'border-layer-line'
                ]"
              />
              <p v-if="errors.slug" class="mt-1.5 text-xs text-rose-500">{{ errors.slug }}</p>
            </div>

            <div class="flex items-center gap-3">
              <label for="is_active" class="text-sm font-medium text-foreground">
                Активное направление
              </label>
              <button
                type="button"
                id="is_active"
                @click="form.is_active = !form.is_active"
                :class="[
                  'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                  form.is_active ? 'bg-primary' : 'bg-muted'
                ]"
              >
                <span
                  :class="[
                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                    form.is_active ? 'translate-x-6' : 'translate-x-1'
                  ]"
                />
              </button>
              <span class="text-xs text-muted-foreground-1">Отображать ли направление на сайте</span>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
            <a
              :href="route('dashboard.additional-educations.directions.index')"
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
import DashboardIcon from '../../Components/DashboardIcon.vue';
import FlashMessages from '../../Components/shared/FlashMessages.vue';

export default {
  name: 'DirectionEdit',
  components: {
    DashboardIcon,
    FlashMessages
  },

  props: {
    direction: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      form: {
        title: this.direction?.title || '',
        slug: this.direction?.slug || '',
        is_active: this.direction?.is_active ?? true
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование направления - ${this.direction?.title}`);
  },

  methods: {
    generateSlug() {
      // Не перегенерируем slug при редактировании если он уже есть
      if (this.form.title && !this.form.slug) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },

    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.put(route('dashboard.additional-educations.directions.update', this.direction.id), this.form, {
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
