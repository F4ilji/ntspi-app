<template>
  <div class="min-h-screen bg-background-2">
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a :href="route('dashboard.educational-programs.index')" class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all">
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="plus" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Создание образовательной программы</h1>
              <p class="text-xs text-muted-foreground-1">Заполните информацию о новой программе</p>
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
              <div>
                <label for="name" class="block text-sm font-medium text-foreground mb-2">
                  Название программы <span class="text-rose-500">*</span>
                </label>
                <input id="name" v-model="form.name" type="text" placeholder="Введите полное название программы"
                  :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all', errors.name ? 'border-rose-500' : 'border-layer-line focus:border-primary']" />
                <p v-if="errors.name" class="mt-1.5 text-xs text-rose-500">{{ errors.name }}</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="lvl_edu" class="block text-sm font-medium text-foreground mb-2">
                    Уровень образования <span class="text-rose-500">*</span>
                  </label>
                  <select id="lvl_edu" v-model="form.lvl_edu"
                    :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all', errors.lvl_edu ? 'border-rose-500' : 'border-layer-line focus:border-primary']">
                    <option value="">Выберите уровень</option>
                    <option v-for="level in educationLevels" :key="level.value" :value="level.value">{{ level.label }}</option>
                  </select>
                  <p v-if="errors.lvl_edu" class="mt-1.5 text-xs text-rose-500">{{ errors.lvl_edu }}</p>
                </div>

                <div>
                  <label for="status" class="block text-sm font-medium text-foreground mb-2">
                    Статус программы <span class="text-rose-500">*</span>
                  </label>
                  <select id="status" v-model="form.status"
                    :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all', errors.status ? 'border-rose-500' : 'border-layer-line focus:border-primary']">
                    <option value="">Выберите статус</option>
                    <option v-for="status in statuses" :key="status.value" :value="status.value">{{ getShortStatusLabel(status.label) }}</option>
                  </select>
                  <p v-if="errors.status" class="mt-1.5 text-xs text-rose-500">{{ errors.status }}</p>
                </div>
              </div>

              <div>
                <label for="lang_stud" class="block text-sm font-medium text-foreground mb-2">
                  Язык обучения <span class="text-rose-500">*</span>
                </label>
                <input id="lang_stud" v-model="form.lang_stud" type="text" placeholder="Например: русский, английский"
                  :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all', errors.lang_stud ? 'border-rose-500' : 'border-layer-line focus:border-primary']" />
                <p v-if="errors.lang_stud" class="mt-1.5 text-xs text-rose-500">{{ errors.lang_stud }}</p>
              </div>

              <div>
                <label for="direction_study_id" class="block text-sm font-medium text-foreground mb-2">
                  Направление подготовки
                </label>
                <select id="direction_study_id" v-model="form.direction_study_id"
                  class="w-full px-4 py-2.5 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                  <option value="">Выберите направление (необязательно)</option>
                  <option v-for="direction in directionStudies" :key="direction.id" :value="direction.id">{{ direction.code }} {{ direction.name }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3">
          <a :href="route('dashboard.educational-programs.index')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all">Отмена</a>
          <button type="submit" :disabled="processing" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed">
            <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" /></svg>
            <DashboardIcon v-else name="check" size="4" />
            {{ processing ? 'Сохранение...' : 'Создать программу' }}
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
  name: 'EducationalProgramCreate',
  components: { DashboardIcon, FlashMessages },
  props: {
    statuses: { type: Array, required: true },
    educationLevels: { type: Array, required: true },
    directionStudies: { type: Array, required: true }
  },
  data() {
    return {
      form: { name: '', lvl_edu: '', status: '', lang_stud: '', direction_study_id: '', about_program: [], program_features: [] },
      errors: {},
      processing: false
    }
  },
  mounted() { this.SET_DOCUMENT_TITLE('Создание образовательной программы'); },
  methods: {
    getShortStatusLabel(label) { return label ? label.split('.')[0] : '—'; },
    submit() {
      this.processing = true;
      this.errors = {};
      this.$inertia.post(route('dashboard.educational-programs.store'), this.form, {
        onFinish: () => { this.processing = false; },
        onError: (errors) => { this.errors = errors; }
      });
    }
  }
}
</script>
