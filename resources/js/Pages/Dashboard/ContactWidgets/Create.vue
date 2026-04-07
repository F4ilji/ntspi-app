<template>
  <div class="min-h-screen bg-background-2">
    <!-- Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.contact-widgets.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="plus" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Создание контактного виджета</h1>
              <p class="text-xs text-muted-foreground-1">Заполните информацию о новом виджете</p>
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
              @click="activeTab = 'main'"
              class="px-6 py-4 text-sm font-medium border-b-2 transition-all"
              :class="activeTab === 'main' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground-1 hover:text-foreground hover:border-line-2'"
            >
              <div class="flex items-center gap-2">
                <DashboardIcon name="information-circle" size="4" />
                Основная информация
              </div>
            </button>
            <button
              @click="activeTab = 'content'"
              class="px-6 py-4 text-sm font-medium border-b-2 transition-all"
              :class="activeTab === 'content' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground-1 hover:text-foreground hover:border-line-2'"
            >
              <div class="flex items-center gap-2">
                <DashboardIcon name="document-text" size="4" />
                Содержание
              </div>
            </button>
          </nav>
        </div>

        <form @submit.prevent="submit" class="p-6">
          <!-- Tab: Main Info -->
          <div v-if="activeTab === 'main'" class="space-y-6">
            <!-- Title -->
            <div>
              <label for="title" class="block text-sm font-medium text-foreground mb-2">
                Название ресурса <span class="text-rose-500">*</span>
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                @input="generateSlug"
                placeholder="Например: Контакты"
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
                URL-адрес (Slug) <span class="text-rose-500">*</span>
              </label>
              <input
                id="slug"
                v-model="form.slug"
                type="text"
                placeholder="Например: contacts"
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

            <!-- Is Active -->
            <div class="flex items-center gap-3">
              <input
                id="is_active"
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary"
              />
              <label for="is_active" class="text-sm font-medium text-foreground">
                Активность ресурса
              </label>
            </div>
          </div>

          <!-- Tab: Content -->
          <div v-if="activeTab === 'content'" class="space-y-6">
            <!-- Columns Repeater -->
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <label class="block text-sm font-medium text-foreground">
                  Столбцы с контактами <span class="text-rose-500">*</span>
                </label>
                <button
                  type="button"
                  @click="addColumn"
                  class="text-sm text-primary hover:text-primary-hover transition-colors"
                >
                  + Добавить столбец
                </button>
              </div>

              <div
                v-for="column in form.content"
                :key="column._uid"
                class="border border-layer-line rounded-lg overflow-hidden"
              >
                <!-- Column Header -->
                <div class="flex items-center gap-2 px-4 py-3 bg-muted/30 border-b border-layer-line">
                  <input
                    v-model="column.title"
                    type="text"
                    required
                    maxlength="255"
                    placeholder="Заголовок столбца (например: Контакты)"
                    class="flex-1 px-3 py-1.5 border border-layer-line rounded-lg bg-white text-foreground text-sm"
                  />
                  <button
                    type="button"
                    @click="removeColumn(columnIndex)"
                    class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
                    :disabled="form.content.length <= 1"
                    title="Удалить столбец"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>

                <!-- Column Items -->
                <div class="p-4 space-y-4">
                  <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-foreground">
                      Контактные блоки
                    </label>
                    <button
                      type="button"
                      @click="addItem(columnIndex)"
                      class="text-sm text-primary hover:text-primary-hover transition-colors"
                    >
                      + Добавить контактный блок
                    </button>
                  </div>

                  <div
                    v-for="(item, itemIndex) in column.items"
                    :key="item._uid"
                    class="border border-layer-line rounded-lg p-4 space-y-3"
                  >
                    <!-- Item Header -->
                    <div class="flex items-center gap-2">
                      <input
                        v-model="item.header"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="Заголовок контакта (например: Телефон)"
                        class="flex-1 px-3 py-1.5 border border-layer-line rounded-lg bg-white text-foreground text-sm"
                      />
                      <button
                        type="button"
                        @click="removeItem(columnIndex, itemIndex)"
                        class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
                        title="Удалить блок"
                      >
                        <DashboardIcon name="trash" size="4" />
                      </button>
                    </div>

                    <!-- Item Details -->
                    <div class="space-y-3">
                      <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-foreground">
                          Детали контакта
                        </label>
                        <button
                          type="button"
                          @click="addDetail(columnIndex, itemIndex)"
                          class="text-sm text-primary hover:text-primary-hover transition-colors"
                        >
                          + Добавить деталь
                        </button>
                      </div>

                      <div
                        v-for="(detail, detailIndex) in item.details"
                        :key="detail._uid"
                        class="flex items-center gap-2"
                      >
                        <div class="flex-1 grid grid-cols-2 gap-2">
                          <input
                            v-model="detail.content"
                            type="text"
                            required
                            placeholder="Значение (например: +7 (123) 456-78-90)"
                            class="px-3 py-1.5 border border-layer-line rounded-lg bg-white text-foreground text-sm"
                          />
                          <input
                            v-model="detail.url"
                            type="text"
                            placeholder="Ссылка (необязательно)"
                            class="px-3 py-1.5 border border-layer-line rounded-lg bg-white text-foreground text-sm"
                          />
                        </div>
                        <button
                          type="button"
                          @click="removeDetail(columnIndex, itemIndex, detailIndex)"
                          class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
                          title="Удалить деталь"
                        >
                          <DashboardIcon name="trash" size="4" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
            <a
              :href="route('dashboard.contact-widgets.index')"
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
              {{ processing ? 'Сохранение...' : 'Создать виджет' }}
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
  name: 'ContactWidgetsCreate',
  components: {
    DashboardIcon,
    FlashMessages
  },

  data() {
    return {
      activeTab: 'main',
      form: {
        title: '',
        slug: '',
        is_active: true,
        content: []
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Создание контактного виджета');
  },

  methods: {
    generateSlug() {
      if (!this.form.slug) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },

    addColumn() {
      this.form.content.push({
        _uid: `col-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`,
        title: '',
        items: []
      });
    },

    removeColumn(index) {
      if (this.form.content.length > 1) {
        this.form.content.splice(index, 1);
      }
    },

    addItem(columnIndex) {
      this.form.content[columnIndex].items.push({
        _uid: `item-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`,
        header: '',
        details: []
      });
    },

    removeItem(columnIndex, itemIndex) {
      this.form.content[columnIndex].items.splice(itemIndex, 1);
    },

    addDetail(columnIndex, itemIndex) {
      this.form.content[columnIndex].items[itemIndex].details.push({
        _uid: `detail-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`,
        content: '',
        url: ''
      });
    },

    removeDetail(columnIndex, itemIndex, detailIndex) {
      this.form.content[columnIndex].items[itemIndex].details.splice(detailIndex, 1);
    },

    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.post(route('dashboard.contact-widgets.store'), this.form, {
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
