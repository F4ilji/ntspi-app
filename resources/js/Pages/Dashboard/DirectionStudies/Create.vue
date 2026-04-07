<template>
  <div class="min-h-screen bg-background-2">
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.direction-studies.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="plus" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Создание направления подготовки</h1>
              <p class="text-xs text-muted-foreground-1">Заполните информацию о новом направлении</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <FlashMessages />

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Main Info -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
          <div class="p-6 border-b border-line-2">
            <div class="flex items-center gap-2">
              <DashboardIcon name="information-circle" size="5" class="text-primary" />
              <h2 class="text-base font-medium text-foreground">Основная информация</h2>
            </div>
          </div>

          <div class="p-6">
            <div class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-foreground mb-2">
                    Название направления <span class="text-rose-500">*</span>
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    @blur="generateSlug"
                    placeholder="Например: Информатика и вычислительная техника"
                    :class="[
                      'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                      errors.name ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                    ]"
                  />
                  <p v-if="errors.name" class="mt-1.5 text-xs text-rose-500">{{ errors.name }}</p>
                </div>

                <div>
                  <label for="code" class="block text-sm font-medium text-foreground mb-2">
                    Код направления <span class="text-rose-500">*</span>
                  </label>
                  <input
                    id="code"
                    v-model="form.code"
                    type="text"
                    placeholder="Например: 09.03.01"
                    :class="[
                      'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                      errors.code ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                    ]"
                  />
                  <p v-if="errors.code" class="mt-1.5 text-xs text-rose-500">{{ errors.code }}</p>
                  <p class="mt-1 text-xs text-muted-foreground-1">Код направления по ФГОС</p>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="uuid" class="block text-sm font-medium text-foreground mb-2">
                    UUID <span class="text-rose-500">*</span>
                  </label>
                  <input
                    id="uuid"
                    v-model="form.uuid"
                    type="text"
                    placeholder="Введите UUID"
                    :class="[
                      'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                      errors.uuid ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                    ]"
                  />
                  <p v-if="errors.uuid" class="mt-1.5 text-xs text-rose-500">{{ errors.uuid }}</p>
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
              </div>

              <div>
                <label for="lvl_edu" class="block text-sm font-medium text-foreground mb-2">
                  Уровень образования <span class="text-rose-500">*</span>
                </label>
                <select
                  id="lvl_edu"
                  v-model="form.lvl_edu"
                  :class="[
                    'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                    errors.lvl_edu ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                  ]"
                >
                  <option value="">Выберите уровень</option>
                  <option v-for="level in educationLevels" :key="level.value" :value="level.value">
                    {{ level.label }}
                  </option>
                </select>
                <p v-if="errors.lvl_edu" class="mt-1.5 text-xs text-rose-500">{{ errors.lvl_edu }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3">
          <a
            :href="route('dashboard.direction-studies.index')"
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
            {{ processing ? 'Сохранение...' : 'Создать направление' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

export default {
  name: 'DirectionStudyCreate',
  components: {
    DashboardIcon,
    FlashMessages
  },

  props: {
    educationLevels: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      form: {
        name: '',
        uuid: '',
        slug: '',
        code: '',
        lvl_edu: '',
        info: []
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Создание направления подготовки');
  },

  methods: {
    generateSlug() {
      if (this.form.name) {
        this.form.slug = this.GENERATE_SLUG(this.form.name);
      }
    },

    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.post(route('dashboard.direction-studies.store'), this.form, {
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
