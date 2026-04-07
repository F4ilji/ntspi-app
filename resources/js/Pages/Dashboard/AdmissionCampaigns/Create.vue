<template>
  <div class="min-h-screen bg-background-2">
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.admission-campaigns.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="plus" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Создание приемной кампании</h1>
              <p class="text-xs text-muted-foreground-1">Заполните информацию о новой кампании</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
              <label for="name" class="block text-sm font-medium text-foreground mb-2">
                Название кампании <span class="text-rose-500">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                placeholder='Например: "Приемная кампания 2024"'
                :class="[
                  'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                  errors.name ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                ]"
              />
              <p v-if="errors.name" class="mt-1.5 text-xs text-rose-500">{{ errors.name }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="academic_year" class="block text-sm font-medium text-foreground mb-2">
                  Академический год <span class="text-rose-500">*</span>
                </label>
                <select
                  id="academic_year"
                  v-model="form.academic_year"
                  :class="[
                    'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                    errors.academic_year ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                  ]"
                >
                  <option value="">Выберите учебный год</option>
                  <option v-for="year in academicYears" :key="year" :value="year">
                    {{ year }}
                  </option>
                </select>
                <p v-if="errors.academic_year" class="mt-1.5 text-xs text-rose-500">{{ errors.academic_year }}</p>
              </div>

              <div>
                <label for="status" class="block text-sm font-medium text-foreground mb-2">
                  Статус кампании <span class="text-rose-500">*</span>
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  :class="[
                    'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                    errors.status ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                  ]"
                >
                  <option value="">Выберите статус</option>
                  <option v-for="status in statuses" :key="status.value" :value="status.value">
                    {{ status.label }}
                  </option>
                </select>
                <p v-if="errors.status" class="mt-1.5 text-xs text-rose-500">{{ errors.status }}</p>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
            <a
              :href="route('dashboard.admission-campaigns.index')"
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
              {{ processing ? 'Сохранение...' : 'Создать кампанию' }}
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
  name: 'AdmissionCampaignCreate',
  components: {
    DashboardIcon,
    FlashMessages
  },

  props: {
    statuses: {
      type: Array,
      required: true
    },
    academicYears: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      form: {
        name: '',
        academic_year: '',
        status: '',
        info: []
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Создание приемной кампании');
  },

  methods: {
    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.post(route('dashboard.admission-campaigns.store'), this.form, {
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
