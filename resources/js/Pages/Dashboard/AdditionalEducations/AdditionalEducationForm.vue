<template>
  <div>
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
      <!-- Tabs Navigation -->
      <div class="border-b border-line-2">
        <nav class="flex -mb-px px-6" aria-label="Tabs">
          <button
            type="button"
            @click="activeTab = 'main'"
            :class="[
              activeTab === 'main'
                ? 'border-primary text-primary'
                : 'border-transparent text-muted-foreground-1 hover:text-foreground hover:border-line-2',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors'
            ]"
          >
            <div class="flex items-center gap-2">
              <DashboardIcon name="information-circle" size="4" />
              Основные данные
            </div>
          </button>
          <button
            type="button"
            @click="activeTab = 'content'"
            :class="[
              activeTab === 'content'
                ? 'border-primary text-primary'
                : 'border-transparent text-muted-foreground-1 hover:text-foreground hover:border-line-2',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors'
            ]"
          >
            <div class="flex items-center gap-2">
              <DashboardIcon name="document-text" size="4" />
              Содержание программы
            </div>
          </button>
        </nav>
      </div>

      <form @submit.prevent="submit" class="p-6">
        <!-- Main Info Tab -->
        <div v-show="activeTab === 'main'" class="space-y-6">
          <!-- General Info Section -->
          <div>
            <h3 class="text-base font-medium text-foreground mb-4">Общая информация</h3>
            <p class="text-xs text-muted-foreground-1 mb-4">Основные сведения о программе</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Title -->
              <div>
                <label for="title" class="block text-sm font-medium text-foreground mb-2">
                  Название программы <span class="text-rose-500">*</span>
                </label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  @blur="generateSlug"
                  placeholder='Например: "Цифровые технологии в управлении"'
                  :class="[
                    'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                    errors.title ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                  ]"
                />
                <p v-if="errors.title" class="mt-1.5 text-xs text-rose-500">{{ errors.title }}</p>
                <p class="mt-1 text-xs text-muted-foreground-1">Полное официальное название программы</p>
              </div>

              <!-- Slug -->
              <div>
                <label for="slug" class="block text-sm font-medium text-foreground mb-2">
                  URL-адрес <span class="text-rose-500">*</span>
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
                <p class="mt-1 text-xs text-muted-foreground-1">Человеко-понятный URL для страницы программы</p>
              </div>
            </div>

            <!-- Category -->
            <div class="mt-6">
              <label for="category_id" class="block text-sm font-medium text-foreground mb-2">
                Категория <span class="text-rose-500">*</span>
              </label>
              <select
                id="category_id"
                v-model="form.category_id"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.category_id ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              >
                <option value="">Выберите категорию</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.title }}
                </option>
              </select>
              <p v-if="errors.category_id" class="mt-1.5 text-xs text-rose-500">{{ errors.category_id }}</p>
              <p class="mt-1 text-xs text-muted-foreground-1">К какой категории относится программа</p>
            </div>

            <!-- Target Group -->
            <div class="mt-6">
              <label for="target_group" class="block text-sm font-medium text-foreground mb-2">
                Целевая аудитория <span class="text-rose-500">*</span>
              </label>
              <input
                id="target_group"
                v-model="form.target_group"
                type="text"
                placeholder='Например: "Руководители среднего звена"'
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.target_group ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.target_group" class="mt-1.5 text-xs text-rose-500">{{ errors.target_group }}</p>
              <p class="mt-1 text-xs text-muted-foreground-1">Для кого предназначена эта программа</p>
            </div>

            <!-- Qualification -->
            <div class="mt-6">
              <label for="qualification" class="block text-sm font-medium text-foreground mb-2">
                Выдаваемый документ
              </label>
              <input
                id="qualification"
                v-model="form.qualification"
                type="text"
                placeholder='Например: "Удостоверение о повышении квалификации"'
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.qualification ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.qualification" class="mt-1.5 text-xs text-rose-500">{{ errors.qualification }}</p>
              <p class="mt-1 text-xs text-muted-foreground-1">Какой документ получат слушатели</p>
            </div>
          </div>

          <!-- Learning Parameters Section -->
          <div class="pt-6 border-t border-line-2">
            <h3 class="text-base font-medium text-foreground mb-4">Параметры обучения</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Price -->
              <div>
                <label for="price" class="block text-sm font-medium text-foreground mb-2">
                  Стоимость (руб) <span class="text-rose-500">*</span>
                </label>
                <input
                  id="price"
                  v-model="form.price"
                  type="number"
                  min="0"
                  placeholder="Укажите стоимость"
                  :class="[
                    'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                    errors.price ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                  ]"
                />
                <p v-if="errors.price" class="mt-1.5 text-xs text-rose-500">{{ errors.price }}</p>
                <p class="mt-1 text-xs text-muted-foreground-1">Полная стоимость программы</p>
              </div>

              <!-- Learning Time -->
              <div>
                <label for="learning_time" class="block text-sm font-medium text-foreground mb-2">
                  Объем (часов) <span class="text-rose-500">*</span>
                </label>
                <input
                  id="learning_time"
                  v-model="form.learning_time"
                  type="number"
                  min="1"
                  placeholder="Укажите количество часов"
                  :class="[
                    'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                    errors.learning_time ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                  ]"
                />
                <p v-if="errors.learning_time" class="mt-1.5 text-xs text-rose-500">{{ errors.learning_time }}</p>
                <p class="mt-1 text-xs text-muted-foreground-1">Общий объем программы в академических часах</p>
              </div>
            </div>

            <!-- Form Education -->
            <div class="mt-6">
              <label for="form_education" class="block text-sm font-medium text-foreground mb-2">
                Форма обучения <span class="text-rose-500">*</span>
              </label>
              <select
                id="form_education"
                v-model="form.form_education"
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.form_education ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              >
                <option value="">Выберите форму</option>
                <option v-for="formEducation in educationForms" :key="formEducation.value" :value="formEducation.value">
                  {{ formEducation.label }}
                </option>
              </select>
              <p v-if="errors.form_education" class="mt-1.5 text-xs text-rose-500">{{ errors.form_education }}</p>
              <p class="mt-1 text-xs text-muted-foreground-1">Основная форма проведения занятий</p>
            </div>

            <!-- Is Active -->
            <div class="mt-6 flex items-center gap-3">
              <label for="is_active" class="text-sm font-medium text-foreground">
                Активна для записи
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
              <span class="text-xs text-muted-foreground-1">Отображать ли программу на сайте</span>
            </div>
          </div>
        </div>

        <!-- Content Tab -->
        <div v-show="activeTab === 'content'">
          <div class="mb-4">
            <h3 class="text-base font-medium text-foreground mb-2">Описание программы</h3>
            <p class="text-xs text-muted-foreground-1">Создайте подробное описание программы с помощью конструктора</p>
          </div>

          <ContentBuilder
            v-model="form.content"
            label="Содержание программы"
          />
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
          <a
            :href="route('dashboard.additional-educations.index')"
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
            {{ processing ? 'Сохранение...' : submitLabel }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../Components/DashboardIcon.vue';
import ContentBuilder from '../Components/ContentBuilder/ContentBuilder.vue';

export default {
  name: 'AdditionalEducationForm',
  components: {
    DashboardIcon,
    ContentBuilder
  },

  props: {
    education: {
      type: Object,
      default: null
    },
    categories: {
      type: Array,
      required: true
    },
    educationForms: {
      type: Array,
      required: true
    },
    submitLabel: {
      type: String,
      default: 'Создать программу'
    },
    submitRoute: {
      type: String,
      required: true
    },
    submitMethod: {
      type: String,
      default: 'post'
    }
  },

  data() {
    return {
      activeTab: 'main',
      form: {
        title: this.education?.title || '',
        slug: this.education?.slug || '',
        category_id: this.education?.category_id || '',
        target_group: this.education?.target_group || '',
        qualification: this.education?.qualification || '',
        price: this.education?.price || '',
        learning_time: this.education?.learning_time || '',
        form_education: this.education?.form_education || '',
        is_active: this.education?.is_active ?? true,
        content: this.education?.content || []
      },
      errors: {},
      processing: false
    }
  },

  methods: {
    generateSlug() {
      if (this.form.title && !this.education) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },

    submit() {
      this.processing = true;
      this.errors = {};

      const routeName = this.submitRoute;
      const method = this.submitMethod;

      if (method === 'put') {
        this.$inertia.put(route(routeName, this.education.id), this.form, {
          onFinish: () => {
            this.processing = false;
          },
          onError: (errors) => {
            this.errors = errors;
          }
        });
      } else {
        this.$inertia.post(route(routeName), this.form, {
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
}
</script>
