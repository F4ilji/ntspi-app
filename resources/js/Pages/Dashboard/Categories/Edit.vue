<template>
  <DashboardLayout>
    <template #header-title>Редактирование категории</template>
    <template #header-subtitle>{{ category.title }}</template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Form Card -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
      <div class="p-6 border-b border-layer-line">
        <div class="flex items-center gap-2">
          <DashboardIcon name="tag" size="5" class="text-primary" />
          <h2 class="text-base font-medium text-foreground">Основная информация</h2>
        </div>
        <p class="text-xs text-muted-foreground-1 mt-1">
          Обновите данные категории
        </p>
      </div>

      <form @submit.prevent="submit" class="p-6">
        <div class="space-y-6">
          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-foreground mb-2">
              Название категории <span class="text-rose-500">*</span>
            </label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              placeholder="Например: Новости института"
              :class="[
                'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                errors.title ? 'border-rose-500' : 'border-layer-line focus:border-primary'
              ]"
            />
            <p v-if="errors.title" class="mt-1.5 text-xs text-rose-500">{{ errors.title }}</p>
          </div>

          <!-- Slug (readonly) -->
          <div>
            <label for="slug" class="block text-sm font-medium text-foreground mb-2">
              Slug
            </label>
            <input
              id="slug"
              :value="category.slug"
              type="text"
              readonly
              class="w-full px-4 py-2.5 bg-muted border border-layer-line rounded-lg text-sm text-muted-foreground-1 cursor-not-allowed"
            />
            <p class="mt-1.5 text-xs text-muted-foreground-1">
              Генерируется автоматически из названия
            </p>
          </div>

          <!-- Is Active -->
          <div class="flex items-center gap-3">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="w-4 h-4 text-primary bg-surface border-layer-line rounded focus:ring-primary/20"
            />
            <label for="is_active" class="text-sm font-medium text-foreground">
              Активная категория
            </label>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-layer-line">
          <a
            :href="route('dashboard.categories.index')"
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
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

export default {
  name: 'CategoryEdit',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
  },
  props: {
    category: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      form: {
        title: this.category.title || '',
        is_active: Boolean(this.category.is_active),
      },
      errors: {},
      processing: false,
    };
  },
  mounted() {
    this.SET_DOCUMENT_TITLE('Редактирование категории');
  },
  methods: {
    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.put(route('dashboard.categories.update', this.category.id), this.form, {
        onFinish: () => {
          this.processing = false;
        },
        onError: (errors) => {
          this.errors = errors;
        },
      });
    },
  },
}
</script>
