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
              <DashboardIcon name="pencil-square" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Редактирование приемной кампании</h1>
              <p class="text-xs text-muted-foreground-1">{{ campaign?.name }}</p>
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
              <DashboardIcon name="document-text" size="5" class="text-primary" />
              <h2 class="text-base font-medium text-foreground">Основная информация</h2>
            </div>
          </div>

          <div class="p-6">
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
          </div>
        </div>

        <!-- Info Repeater -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
          <div class="p-6 border-b border-line-2">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <DashboardIcon name="academic-cap" size="5" class="text-primary" />
                <h2 class="text-base font-medium text-foreground">Информация о наборе</h2>
              </div>
              <button
                type="button"
                @click="addInfoItem"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all shadow-sm"
              >
                <DashboardIcon name="plus" size="4" />
                Добавить уровень образования
              </button>
            </div>
            <p class="text-xs text-muted-foreground-1 mt-1">
              Данные о программах и местах для разных уровней образования
            </p>
          </div>

          <div class="p-6">
            <div v-if="form.info.length === 0" class="text-center py-12 bg-surface border border-layer-line rounded-lg">
              <DashboardIcon name="academic-cap" size="12" class="text-muted-foreground-2 mx-auto mb-4" />
              <p class="text-foreground font-medium">Нет уровней образования</p>
              <p class="text-sm text-muted-foreground-1 mt-1 mb-4">Добавьте уровни образования для формирования набора</p>
              <button
                type="button"
                @click="addInfoItem"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all"
              >
                <DashboardIcon name="plus" size="4" />
                Добавить первый уровень
              </button>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="(item, index) in form.info"
                :key="index"
                class="group bg-layer border border-layer-line rounded-lg overflow-hidden"
              >
                <!-- Item Header -->
                <div class="flex items-center justify-between px-4 py-3 bg-surface/50 border-b border-line-2">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-surface-muted text-muted-foreground-1 text-xs font-medium rounded">
                      #{{ index + 1 }}
                    </span>
                    <span class="text-sm font-medium text-foreground">
                      {{ getLevelEducationalLabel(item.edu_name) || 'Уровень образования' }}
                    </span>
                  </div>
                  <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button
                      type="button"
                      @click="duplicateInfoItem(index)"
                      class="p-1.5 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded transition-all"
                      title="Дублировать"
                    >
                      <DashboardIcon name="square-2-stack" size="4" />
                    </button>
                    <button
                      type="button"
                      @click="removeInfoItem(index)"
                      class="p-1.5 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded transition-all"
                      title="Удалить"
                    >
                      <DashboardIcon name="trash" size="4" />
                    </button>
                  </div>
                </div>

                <!-- Item Content -->
                <div class="p-4 space-y-4">
                  <!-- Level Education -->
                  <div>
                    <label :for="`edu_name_${index}`" class="block text-xs font-medium text-muted-foreground-1 mb-1.5">
                      Уровень образования <span class="text-rose-500">*</span>
                    </label>
                    <select
                      :id="`edu_name_${index}`"
                      v-model="item.edu_name"
                      class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                      <option value="">Выберите уровень</option>
                      <option v-for="level in levelEducationalOptions" :key="level.value" :value="level.value">
                        {{ level.label }}
                      </option>
                    </select>
                  </div>

                  <!-- Programs Count -->
                  <div>
                    <label :for="`total_programs_${index}`" class="block text-xs font-medium text-muted-foreground-1 mb-1.5">
                      Количество программ <span class="text-rose-500">*</span>
                    </label>
                    <input
                      :id="`total_programs_${index}`"
                      v-model.number="item.total_programs"
                      type="number"
                      min="0"
                      class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    />
                  </div>

                  <!-- Places Distribution -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label :for="`och_count_${index}`" class="block text-xs font-medium text-muted-foreground-1 mb-1.5">
                        Очная форма <span class="text-rose-500">*</span>
                      </label>
                      <input
                        :id="`och_count_${index}`"
                        v-model.number="item.och_count"
                        type="number"
                        min="0"
                        class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                      />
                    </div>

                    <div>
                      <label :for="`zaoch_count_${index}`" class="block text-xs font-medium text-muted-foreground-1 mb-1.5">
                        Заочная форма <span class="text-rose-500">*</span>
                      </label>
                      <input
                        :id="`zaoch_count_${index}`"
                        v-model.number="item.zaoch_count"
                        type="number"
                        min="0"
                        class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                      />
                    </div>

                    <div>
                      <label :for="`budget_places_${index}`" class="block text-xs font-medium text-muted-foreground-1 mb-1.5">
                        Бюджетные места <span class="text-rose-500">*</span>
                      </label>
                      <input
                        :id="`budget_places_${index}`"
                        v-model.number="item.budget_places"
                        type="number"
                        min="0"
                        class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                      />
                    </div>

                    <div>
                      <label :for="`non_budget_places_${index}`" class="block text-xs font-medium text-muted-foreground-1 mb-1.5">
                        Платные места <span class="text-rose-500">*</span>
                      </label>
                      <input
                        :id="`non_budget_places_${index}`"
                        v-model.number="item.non_budget_places"
                        type="number"
                        min="0"
                        class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3">
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
            {{ processing ? 'Сохранение...' : 'Сохранить изменения' }}
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
  name: 'AdmissionCampaignEdit',
  components: {
    DashboardIcon,
    FlashMessages
  },

  props: {
    campaign: {
      type: Object,
      required: true
    },
    statuses: {
      type: Array,
      required: true
    },
    academicYears: {
      type: Array,
      required: true
    },
    levelEducationalOptions: {
      type: Array,
      default: () => [
        { value: 1, label: 'Подготовка квалифицированных рабочих, служащих' },
        { value: 2, label: 'Среднее профессиональное образование' },
        { value: 3, label: 'Бакалавриат' },
        { value: 4, label: 'Магистратура' },
        { value: 5, label: 'Специалитет' },
        { value: 6, label: 'Аспирантура' },
        { value: 7, label: 'Адъюнктура' },
        { value: 8, label: 'Ординатура' },
        { value: 9, label: 'Ассистентура - стажировка' },
        { value: 10, label: 'Профессиональная подготовка по профессиям рабочих, должностям служащих' },
        { value: 11, label: 'Переподготовка рабочих, служащих' },
        { value: 12, label: 'Повышение квалификации рабочих, служащих' },
        { value: 13, label: 'Дополнительная общеразвивающая программа' },
        { value: 14, label: 'Дополнительная предпрофессиональная программа' },
        { value: 15, label: 'Дополнительная предпрофессиональная программа в сфере искусств' },
        { value: 16, label: 'Повышение квалификации' },
        { value: 17, label: 'Профессиональная переподготовка' },
        { value: 18, label: 'Дошкольное образование' },
        { value: 19, label: 'Начальное общее образование' },
        { value: 20, label: 'Основное общее образование' },
        { value: 21, label: 'Среднее общее образование' },
        { value: 22, label: 'Интернатура' },
        { value: 23, label: 'Дополнительная предпрофессиональная программа в сфере физической культуры и спорта' },
        { value: 24, label: 'Базовое высшее образование' },
        { value: 25, label: 'Специализированное высшее образование' }
      ]
    }
  },

  data() {
    return {
      form: {
        name: this.campaign?.name || '',
        academic_year: this.campaign?.academic_year || '',
        status: this.campaign?.status || '',
        info: Array.isArray(this.campaign?.info) ? JSON.parse(JSON.stringify(this.campaign.info)) : []
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование кампании - ${this.campaign?.name}`);
  },

  methods: {
    getLevelEducationalLabel(value) {
      const level = this.levelEducationalOptions.find(l => l.value === value);
      return level ? level.label : null;
    },

    addInfoItem() {
      this.form.info.push({
        edu_name: '',
        total_programs: 0,
        och_count: 0,
        zaoch_count: 0,
        budget_places: 0,
        non_budget_places: 0
      });
    },

    removeInfoItem(index) {
      this.form.info.splice(index, 1);
    },

    duplicateInfoItem(index) {
      const item = JSON.parse(JSON.stringify(this.form.info[index]));
      this.form.info.splice(index + 1, 0, item);
    },

    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.put(route('dashboard.admission-campaigns.update', this.campaign.id), this.form, {
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
