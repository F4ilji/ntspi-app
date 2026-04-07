<template>
  <div class="min-h-screen bg-background-2">
    <!-- Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.custom-forms.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="plus" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Создание пользовательской формы</h1>
              <p class="text-xs text-muted-foreground-1">Заполните конфигурацию новой формы</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Flash Messages -->
      <FlashMessages />

      <!-- Form Card -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
        <!-- Tabs Navigation -->
        <div class="border-b border-line-2">
          <nav class="flex -mb-px">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              @click="activeTab = tab.key"
              class="px-6 py-4 text-sm font-medium border-b-2 transition-all"
              :class="activeTab === tab.key ? 'border-primary text-primary' : 'border-transparent text-muted-foreground-1 hover:text-foreground hover:border-line-2'"
            >
              <div class="flex items-center gap-2">
                <DashboardIcon :name="tab.icon" size="4" />
                {{ tab.label }}
              </div>
            </button>
          </nav>
        </div>

        <form @submit.prevent="submit" class="p-6">
          <!-- Tab: Main Info -->
          <div v-if="activeTab === 'main'" class="space-y-6">
            <div>
              <label for="title" class="block text-sm font-medium text-foreground mb-2">
                Название формы <span class="text-rose-500">*</span>
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                @input="generateFormId"
                placeholder="Например: Заявка на обучение"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.title ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.title" class="mt-1.5 text-xs text-rose-500">{{ errors.title }}</p>
            </div>

            <div>
              <label for="form_id" class="block text-sm font-medium text-foreground mb-2">
                Уникальный ID формы <span class="text-rose-500">*</span>
              </label>
              <input
                id="form_id"
                v-model="form.form_id"
                type="text"
                placeholder="application-form-1234567890"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.form_id ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.form_id" class="mt-1.5 text-xs text-rose-500">{{ errors.form_id }}</p>
              <p class="mt-1.5 text-xs text-muted-foreground-1">
                Автоматически генерируется из названия. Можно изменить.
              </p>
            </div>

            <div>
              <label for="description" class="block text-sm font-medium text-foreground mb-2">
                Описание формы <span class="text-rose-500">*</span>
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                placeholder="Опишите назначение этой формы"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none',
                  errors.description ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              ></textarea>
              <p v-if="errors.description" class="mt-1.5 text-xs text-rose-500">{{ errors.description }}</p>
            </div>

            <div>
              <label for="status" class="block text-sm font-medium text-foreground mb-2">
                Статус формы <span class="text-rose-500">*</span>
              </label>
              <select
                id="status"
                v-model="form.status"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.status ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              >
                <option value="published">Опубликовано</option>
                <option value="hidden">Скрыто</option>
              </select>
              <p v-if="errors.status" class="mt-1.5 text-xs text-rose-500">{{ errors.status }}</p>
            </div>
          </div>

          <!-- Tab: Fields -->
          <div v-if="activeTab === 'fields'" class="space-y-6">
            <FormBuilder
              v-model="form.columns"
              label="Конструктор полей формы"
            />
          </div>

          <!-- Tab: Submit Button -->
          <div v-if="activeTab === 'button'" class="space-y-6">
            <div>
              <label for="button" class="block text-sm font-medium text-foreground mb-2">
                Текст кнопки отправки <span class="text-rose-500">*</span>
              </label>
              <input
                id="button"
                v-model="form.button"
                type="text"
                placeholder="Например: Отправить заявку"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.button ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.button" class="mt-1.5 text-xs text-rose-500">{{ errors.button }}</p>
            </div>

            <div>
              <label for="send_message" class="block text-sm font-medium text-foreground mb-2">
                Сообщение после отправки <span class="text-rose-500">*</span>
              </label>
              <textarea
                id="send_message"
                v-model="form.send_message"
                rows="3"
                placeholder="Спасибо! Ваша заявка принята."
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none',
                  errors.send_message ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              ></textarea>
              <p v-if="errors.send_message" class="mt-1.5 text-xs text-rose-500">{{ errors.send_message }}</p>
            </div>
          </div>

          <!-- Tab: Settings -->
          <div v-if="activeTab === 'settings'" class="space-y-6">
            <div class="space-y-4">
              <div class="flex items-center gap-3">
                <input
                  id="personal_data"
                  v-model="form.settings.personal_data"
                  type="checkbox"
                  class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary"
                />
                <label for="personal_data" class="text-sm font-medium text-foreground">
                  Согласие на обработку персональных данных
                </label>
              </div>

              <div class="flex items-center gap-3">
                <input
                  id="captcha"
                  v-model="form.settings.captcha"
                  type="checkbox"
                  class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary"
                />
                <label for="captcha" class="text-sm font-medium text-foreground">
                  Защита CAPTCHA
                </label>
              </div>
            </div>
          </div>

          <!-- Tab: Mail Settings -->
          <div v-if="activeTab === 'mail'" class="space-y-6">
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <label class="block text-sm font-medium text-foreground">
                  Настройки уведомлений
                </label>
                <button
                  type="button"
                  @click="addMailRecipient"
                  class="text-sm text-primary hover:text-primary-hover transition-colors"
                >
                  + Добавить получателя
                </button>
              </div>

              <div
                v-for="(recipient, index) in form.mail_settings"
                :key="index"
                class="border border-layer-line rounded-lg p-4 space-y-3"
              >
                <div class="flex items-center justify-between">
                  <span class="text-sm font-medium text-foreground">Получатель #{{ index + 1 }}</span>
                  <button
                    type="button"
                    @click="removeMailRecipient(index)"
                    class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Email</label>
                    <input
                      v-model="recipient.target"
                      type="email"
                      placeholder="email@example.com"
                      class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm"
                    />
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Тема письма</label>
                    <input
                      v-model="recipient.topic"
                      type="text"
                      placeholder="Новая заявка"
                      class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm"
                    />
                  </div>
                </div>
              </div>

              <div v-if="form.mail_settings.length === 0" class="text-center py-8 text-muted-foreground-1 text-sm">
                Нет получателей. Добавьте первого получателя уведомлений.
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
            <a
              :href="route('dashboard.custom-forms.index')"
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
              {{ processing ? 'Сохранение...' : 'Создать форму' }}
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
import FormBuilder from '../Components/ContentBuilder/FormBuilder.vue';

export default {
  name: 'CustomFormsCreate',
  components: {
    DashboardIcon,
    FlashMessages,
    FormBuilder
  },

  data() {
    return {
      activeTab: 'main',
      tabs: [
        { key: 'main', label: 'Основная информация', icon: 'information-circle' },
        { key: 'fields', label: 'Поля формы', icon: 'view-columns' },
        { key: 'button', label: 'Кнопка отправки', icon: 'paper-airplane' },
        { key: 'settings', label: 'Настройки', icon: 'cog-6-tooth' },
        { key: 'mail', label: 'Настройки почты', icon: 'envelope' },
      ],
      form: {
        title: '',
        form_id: '',
        description: '',
        status: 'published',
        button: 'Отправить',
        send_message: 'Спасибо! Ваша заявка принята.',
        columns: [],
        settings: {
          personal_data: false,
          captcha: false
        },
        mail_settings: []
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Создание пользовательской формы');
  },

  methods: {
    generateFormId() {
      if (!this.form.form_id) {
        this.form.form_id = this.GENERATE_SLUG(this.form.title) + Date.now();
      }
    },

    addMailRecipient() {
      this.form.mail_settings.push({
        target: '',
        topic: 'Новая заявка с формы',
        data: []
      });
    },

    removeMailRecipient(index) {
      this.form.mail_settings.splice(index, 1);
    },

    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.post(route('dashboard.custom-forms.store'), this.form, {
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
