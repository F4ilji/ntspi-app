<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="photo" size="5" class="text-primary" />
    </template>
    <template #header-title>Создание слайдера</template>
    <template #header-subtitle>Заполните основные параметры</template>
    <template #header-actions>
      <a
        :href="route('dashboard.sliders.index')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all"
      >
        <DashboardIcon name="arrow-left" size="4" />
        Назад
      </a>
    </template>

    <div class="max-w-2xl mx-auto">
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-line-2">
          <h3 class="text-base font-semibold text-foreground">Основные настройки</h3>
        </div>
        <form @submit.prevent="submit" class="p-6 space-y-4">
          <div>
            <label for="title" class="block text-sm font-medium text-foreground mb-1.5">
              Название слайдера *
            </label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              required
              @input="generateSlug"
              class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
              placeholder="Например: Главный слайдер"
            />
          </div>

          <div>
            <label for="slug" class="block text-sm font-medium text-foreground mb-1.5">
              URL-идентификатор *
            </label>
            <input
              id="slug"
              v-model="form.slug"
              type="text"
              required
              class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
              placeholder="main-slider"
            />
          </div>

          <div class="flex items-center gap-3">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20"
            />
            <label for="is_active" class="text-sm font-medium text-foreground">
              Активный слайдер
            </label>
          </div>

          <div class="pt-4 flex gap-3">
            <button
              type="submit"
              :disabled="form.processing"
              class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Создать слайдер
            </button>
            <a
              :href="route('dashboard.sliders.index')"
              class="px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-colors"
            >
              Отмена
            </a>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';

export default {
  name: 'CreateSlider',
  components: {
    DashboardLayout,
    DashboardIcon
  },
  data() {
    return {
      form: {
        title: '',
        slug: '',
        is_active: true,
        processing: false
      }
    }
  },
  mounted() {
    this.SET_DOCUMENT_TITLE('Создание слайдера');
  },
  methods: {
    generateSlug() {
      if (this.form.title && !this.form.slug) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },
    submit() {
      this.form.processing = true
      this.$inertia.post(route('dashboard.sliders.store'), this.form, {
        onSuccess: () => {
          this.form.processing = false
        },
        onError: () => {
          this.form.processing = false
        }
      })
    }
  }
}
</script>
