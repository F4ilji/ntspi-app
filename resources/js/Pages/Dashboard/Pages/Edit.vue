<template>
  <div class="min-h-screen bg-background-2">
    <!-- Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-3">
            <a
              :href="route('dashboard.pages.index')"
              class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
            >
              <DashboardIcon name="arrow-left" size="5" />
            </a>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                <DashboardIcon name="pencil-square" size="5" class="text-primary" />
              </div>
              <div>
                <h1 class="text-lg font-medium text-foreground">Редактирование страницы</h1>
                <p class="text-xs text-muted-foreground-1">{{ page.title }}</p>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <a
              :href="route('dashboard.pages.index')"
              class="inline-flex items-center gap-2 px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all"
            >
              Отмена
            </a>
            <button
              type="button"
              @click="submit"
              :disabled="processing"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              <DashboardIcon v-else name="check" size="4" />
              {{ processing ? 'Сохранение...' : 'Сохранить' }}
            </button>
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
            <button
              @click="activeTab = 'settings'"
              class="px-6 py-4 text-sm font-medium border-b-2 transition-all"
              :class="activeTab === 'settings' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground-1 hover:text-foreground hover:border-line-2'"
            >
              <div class="flex items-center gap-2">
                <DashboardIcon name="cog-6-tooth" size="4" />
                Дополнительные настройки
              </div>
            </button>
          </nav>
        </div>

        <form @submit.prevent="submit" class="p-6">
          <!-- Tab: Main Info -->
          <div v-show="activeTab === 'main'" class="space-y-6">
            <!-- Title -->
            <div>
              <label for="title" class="block text-sm font-medium text-foreground mb-2">
                Заголовок страницы <span class="text-rose-500">*</span>
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                @input="generateSlug"
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
                URL-адрес страницы <span class="text-rose-500">*</span>
              </label>
              <input
                id="slug"
                v-model="form.slug"
                type="text"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.slug ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.slug" class="mt-1.5 text-xs text-rose-500">{{ errors.slug }}</p>
            </div>

            <!-- Path (read-only) -->
            <div>
              <label for="path" class="block text-sm font-medium text-foreground mb-2">
                Путь страницы
              </label>
              <input
                id="path"
                v-model="form.path"
                type="text"
                readonly
                class="w-full px-4 py-2.5 bg-muted border border-layer-line rounded-lg text-sm text-muted-foreground-1 cursor-not-allowed"
              />
              <p class="mt-1.5 text-xs text-muted-foreground-1">
                Генерируется автоматически на основе подраздела
              </p>
            </div>

            <!-- SubSection -->
            <div>
              <label for="sub_section_id" class="block text-sm font-medium text-foreground mb-2">
                Родительский подраздел
              </label>
              <select
                id="sub_section_id"
                v-model="form.sub_section_id"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.sub_section_id ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              >
                <option value="">Без подраздела</option>
                <option v-for="subSection in subSections" :key="subSection.id" :value="subSection.id">
                  {{ subSection.title }}
                </option>
              </select>
              <p v-if="errors.sub_section_id" class="mt-1.5 text-xs text-rose-500">{{ errors.sub_section_id }}</p>
            </div>

            <!-- Code -->
            <div>
              <label for="code" class="block text-sm font-medium text-foreground mb-2">
                HTTP статус страницы <span class="text-rose-500">*</span>
              </label>
              <select
                id="code"
                v-model="form.code"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.code ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              >
                <option value="200">Обычная страница (200 OK)</option>
                <option value="404">Страница не найдена (404 Not Found)</option>
                <option value="500">Технические работы (500 Server Error)</option>
              </select>
              <p v-if="errors.code" class="mt-1.5 text-xs text-rose-500">{{ errors.code }}</p>
            </div>

            <!-- Searchable -->
            <div class="flex items-center gap-3">
              <input
                id="searchable"
                v-model="form.searchable"
                type="checkbox"
                class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary"
              />
              <label for="searchable" class="text-sm font-medium text-foreground">
                Индексировать в поиске
              </label>
            </div>
          </div>

          <!-- Tab: Content -->
          <div v-show="activeTab === 'content'" class="space-y-6">
            <ContentBuilder
              v-model="form.content"
              label="Содержание страницы"
            />
          </div>

          <!-- Tab: Settings -->
          <div v-show="activeTab === 'settings'" class="space-y-6">
            <!-- Display Settings -->
            <div class="border border-layer-line rounded-lg">
              <div class="px-4 py-3 border-b border-line-2 bg-surface/50">
                <h3 class="text-sm font-medium text-foreground">Отображение элементов</h3>
                <p class="text-xs text-muted-foreground-1 mt-0.5">Управление видимостью элементов на странице</p>
              </div>
              <div class="p-4 space-y-4">
                <div class="flex items-center gap-3">
                  <input
                    id="hide_sub_section_links"
                    v-model="form.settings.hide_page_sub_section_links"
                    type="checkbox"
                    class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary"
                  />
                  <label for="hide_sub_section_links" class="text-sm text-foreground">
                    Скрыть боковую панель с ссылками на страницы раздела
                  </label>
                </div>
                <div class="flex items-center gap-3">
                  <input
                    id="hide_page_navigate_links"
                    v-model="form.settings.hide_page_navigate_links"
                    type="checkbox"
                    class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary"
                  />
                  <label for="hide_page_navigate_links" class="text-sm text-foreground">
                    Скрыть навигацию по странице
                  </label>
                </div>
                <div class="flex items-center gap-3">
                  <input
                    id="hide_breadcrumbs"
                    v-model="form.settings.hide_breadcrumbs"
                    type="checkbox"
                    class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary"
                  />
                  <label for="hide_breadcrumbs" class="text-sm text-foreground">
                    Скрыть хлебные крошки
                  </label>
                </div>
              </div>
            </div>

            <!-- Floating Form Settings -->
            <div class="border border-layer-line rounded-lg">
              <div class="px-4 py-3 border-b border-line-2 bg-surface/50">
                <h3 class="text-sm font-medium text-foreground">Плавающая форма на странице</h3>
                <p class="text-xs text-muted-foreground-1 mt-0.5">Управление плавающей формой на странице</p>
              </div>
              <div class="p-4 space-y-4">
                <div>
                  <label for="form_id" class="block text-sm font-medium text-foreground mb-2">
                    Прикрепить форму для страницы
                  </label>
                  <input
                    id="form_id"
                    v-model="form.settings.form.id"
                    type="text"
                    placeholder="ID формы"
                    class="w-full px-4 py-2.5 bg-surface border border-layer-line rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                  />
                </div>
                <div>
                  <label for="form_title" class="block text-sm font-medium text-foreground mb-2">
                    Заголовок плавающего окна
                  </label>
                  <input
                    id="form_title"
                    v-model="form.settings.form.title"
                    type="text"
                    placeholder="Заголовок"
                    class="w-full px-4 py-2.5 bg-surface border border-layer-line rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                  />
                </div>
                <div>
                  <label for="form_description" class="block text-sm font-medium text-foreground mb-2">
                    Описание плавающего окна
                  </label>
                  <input
                    id="form_description"
                    v-model="form.settings.form.description"
                    type="text"
                    placeholder="Описание"
                    class="w-full px-4 py-2.5 bg-surface border border-layer-line rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                  />
                </div>
                <div>
                  <label for="form_button" class="block text-sm font-medium text-foreground mb-2">
                    Текст кнопки
                  </label>
                  <input
                    id="form_button"
                    v-model="form.settings.form.button"
                    type="text"
                    placeholder="Текст кнопки"
                    class="w-full px-4 py-2.5 bg-surface border border-layer-line rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                  />
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import ContentBuilder from '../Components/ContentBuilder/ContentBuilder.vue';

export default {
  name: 'PagesEdit',
  components: {
    DashboardIcon,
    FlashMessages,
    ContentBuilder
  },

  props: {
    page: {
      type: Object,
      required: true
    },
    subSections: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      activeTab: 'main',
      form: {
        title: this.page.title,
        slug: this.page.slug,
        path: this.page.path,
        sub_section_id: this.page.sub_section_id || '',
        code: this.page.code || '200',
        searchable: this.page.searchable === 1 || this.page.searchable === true || this.page.searchable === '1',
        content: this.page.content || [],
        settings: {
          hide_page_sub_section_links: this.page.settings?.hide_page_sub_section_links || false,
          hide_page_navigate_links: this.page.settings?.hide_page_navigate_links || false,
          hide_breadcrumbs: this.page.settings?.hide_breadcrumbs || false,
          form: {
            id: this.page.settings?.form?.id || '',
            title: this.page.settings?.form?.title || '',
            description: this.page.settings?.form?.description || '',
            button: this.page.settings?.form?.button || ''
          }
        }
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование: ${this.page.title}`);
  },

  methods: {
    generateSlug() {
      if (!this.form.slug || this.form.slug === this.page.slug) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },

    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.put(route('dashboard.pages.update', this.page.id), this.form, {
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
