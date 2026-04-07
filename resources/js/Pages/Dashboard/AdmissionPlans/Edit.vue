<template>
  <div class="min-h-screen bg-background-2">
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a :href="route('dashboard.admission-plans.index')" class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all">
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon :name="isEdit ? 'pencil-square' : 'plus'" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">{{ isEdit ? 'Редактирование плана приема' : 'Создание плана приема' }}</h1>
              <p class="text-xs text-muted-foreground-1">{{ isEdit ? plan?.educational_program?.name : 'Заполните все необходимые поля' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <FlashMessages />

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Main Selection -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
          <div class="p-6 border-b border-line-2">
            <div class="flex items-center gap-2">
              <DashboardIcon name="information-circle" size="5" class="text-primary" />
              <h2 class="text-base font-medium text-foreground">Основные настройки</h2>
            </div>
          </div>
          <div class="p-6 space-y-6">
            <div>
              <label for="educational_programs_id" class="block text-sm font-medium text-foreground mb-2">
                Образовательная программа <span class="text-rose-500">*</span>
              </label>
              <select id="educational_programs_id" v-model="form.educational_programs_id"
                :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all', errors.educational_programs_id ? 'border-rose-500' : 'border-layer-line focus:border-primary']">
                <option value="">Выберите образовательную программу</option>
                <option v-for="program in educationalPrograms" :key="program.id" :value="program.id">{{ program.name }}</option>
              </select>
              <p v-if="errors.educational_programs_id" class="mt-1.5 text-xs text-rose-500">{{ errors.educational_programs_id }}</p>
            </div>

            <div>
              <label for="admission_campaigns_id" class="block text-sm font-medium text-foreground mb-2">
                Приемная кампания <span class="text-rose-500">*</span>
              </label>
              <select id="admission_campaigns_id" v-model="form.admission_campaigns_id"
                :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all', errors.admission_campaigns_id ? 'border-rose-500' : 'border-layer-line focus:border-primary']">
                <option value="">Выберите приемную кампанию</option>
                <option v-for="campaign in admissionCampaigns" :key="campaign.id" :value="campaign.id">{{ campaign.name }} ({{ campaign.academic_year }})</option>
              </select>
              <p v-if="errors.admission_campaigns_id" class="mt-1.5 text-xs text-rose-500">{{ errors.admission_campaigns_id }}</p>
            </div>
          </div>
        </div>

        <!-- Exams Repeater -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
          <div class="p-6 border-b border-line-2">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <DashboardIcon name="document-check" size="5" class="text-primary" />
                <h2 class="text-base font-medium text-foreground">Вступительные испытания</h2>
              </div>
              <button type="button" @click="addExam" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all shadow-sm">
                <DashboardIcon name="plus" size="4" />
                Добавить испытание
              </button>
            </div>
            <p class="text-xs text-muted-foreground-1 mt-1">Добавьте все необходимые вступительные испытания</p>
          </div>

          <div class="p-6">
            <div v-if="form.exams.length === 0" class="text-center py-8 bg-surface border border-layer-line rounded-lg">
              <DashboardIcon name="document-check" size="10" class="text-muted-foreground-2 mx-auto mb-3" />
              <p class="text-foreground font-medium">Нет вступительных испытаний</p>
              <button type="button" @click="addExam" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all">
                <DashboardIcon name="plus" size="4" />
                Добавить первое испытание
              </button>
            </div>

            <div v-else class="space-y-4">
              <div v-for="(exam, eIdx) in form.exams" :key="eIdx" class="group bg-layer border border-layer-line rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 bg-surface/50 border-b border-line-2">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-surface-muted text-muted-foreground-1 text-xs font-medium rounded">#{{ eIdx + 1 }}</span>
                    <span class="text-sm font-medium text-foreground">{{ exam.title || 'Новое испытание' }}</span>
                  </div>
                  <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button type="button" @click="duplicateExam(eIdx)" class="p-1.5 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded transition-all" title="Дублировать">
                      <DashboardIcon name="square-2-stack" size="4" />
                    </button>
                    <button type="button" @click="removeExam(eIdx)" class="p-1.5 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded transition-all" title="Удалить">
                      <DashboardIcon name="trash" size="4" />
                    </button>
                  </div>
                </div>

                <div class="p-4 space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs font-medium text-muted-foreground-1 mb-1.5">Название предмета <span class="text-rose-500">*</span></label>
                      <input v-model="exam.title" type="text" placeholder="Например: Математика" class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-muted-foreground-1 mb-1.5">Приоритет <span class="text-rose-500">*</span></label>
                      <input v-model.number="exam.priority" type="number" min="0" class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
                    </div>
                  </div>

                  <!-- Exam Types -->
                  <div>
                    <div class="flex items-center justify-between mb-2">
                      <label class="block text-xs font-medium text-muted-foreground-1">Виды вступительного испытания</label>
                      <button type="button" @click="addExamType(eIdx)" :disabled="(exam.types || []).length >= 2" class="text-xs text-primary hover:text-primary-hover disabled:opacity-50 disabled:cursor-not-allowed">
                        + Добавить вид
                      </button>
                    </div>
                    <div v-for="(type, tIdx) in (exam.types || [])" :key="tIdx" class="flex items-center gap-3 p-3 bg-surface border border-layer-line rounded-lg mb-2">
                      <select v-model="type.type" class="flex-1 px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">Тип испытания</option>
                        <option :value="1">ЕГЭ</option>
                        <option :value="2">ВИ, проводимое организацией самостоятельно</option>
                        <option :value="3">Ср. балл документа об образовании</option>
                        <option :value="4">Аккредитация</option>
                      </select>
                      <input v-model.number="type.min_ball" type="number" min="0" max="100" placeholder="Мин. балл" class="w-24 px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
                      <button type="button" @click="removeExamType(eIdx, tIdx)" class="p-1.5 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded transition-all">
                        <DashboardIcon name="x-mark" size="4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Contests Repeater -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
          <div class="p-6 border-b border-line-2">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <DashboardIcon name="trophy" size="5" class="text-primary" />
                <h2 class="text-base font-medium text-foreground">Условия поступления</h2>
              </div>
              <button type="button" @click="addContest" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all shadow-sm">
                <DashboardIcon name="plus" size="4" />
                Добавить группу
              </button>
            </div>
            <p class="text-xs text-muted-foreground-1 mt-1">Добавьте группы с условиями поступления</p>
          </div>

          <div class="p-6">
            <div v-if="form.contests.length === 0" class="text-center py-8 bg-surface border border-layer-line rounded-lg">
              <DashboardIcon name="trophy" size="10" class="text-muted-foreground-2 mx-auto mb-3" />
              <p class="text-foreground font-medium">Нет условий поступления</p>
              <button type="button" @click="addContest" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all">
                <DashboardIcon name="plus" size="4" />
                Добавить первую группу
              </button>
            </div>

            <div v-else class="space-y-4">
              <div v-for="(contest, cIdx) in form.contests" :key="cIdx" class="group bg-layer border border-layer-line rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 bg-surface/50 border-b border-line-2">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-surface-muted text-muted-foreground-1 text-xs font-medium rounded">#{{ cIdx + 1 }}</span>
                    <span class="text-sm font-medium text-foreground">{{ getFormEducationLabel(contest.form_education) || 'Условия поступления' }}</span>
                  </div>
                  <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button type="button" @click="duplicateContest(cIdx)" class="p-1.5 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded transition-all" title="Дублировать">
                      <DashboardIcon name="square-2-stack" size="4" />
                    </button>
                    <button type="button" @click="removeContest(cIdx)" class="p-1.5 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded transition-all" title="Удалить">
                      <DashboardIcon name="trash" size="4" />
                    </button>
                  </div>
                </div>

                <div class="p-4 space-y-4">
                  <div>
                    <label class="block text-xs font-medium text-muted-foreground-1 mb-1.5">Форма обучения <span class="text-rose-500">*</span></label>
                    <select v-model="contest.form_education" class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                      <option value="">Выберите форму</option>
                      <option :value="1">Очная форма обучения</option>
                      <option :value="2">Очно-заочная форма обучения</option>
                      <option :value="3">Заочная форма обучения</option>
                    </select>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs font-medium text-muted-foreground-1 mb-1.5">Форма финансирования <span class="text-rose-500">*</span></label>
                      <select v-model="contest.places.form_budget" class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">Выберите тип</option>
                        <option :value="1">Основные места</option>
                        <option :value="2">Целевая квота</option>
                        <option :value="3">Особая квота</option>
                        <option :value="4">С оплатой обучения</option>
                        <option :value="5">За счёт иных средств</option>
                        <option :value="6">Отдельная квота</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-muted-foreground-1 mb-1.5">Количество мест <span class="text-rose-500">*</span></label>
                      <input v-model.number="contest.places.count" type="number" min="0" class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3">
          <a :href="route('dashboard.admission-plans.index')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all">Отмена</a>
          <button type="submit" :disabled="processing" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed">
            <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" /></svg>
            <DashboardIcon v-else name="check" size="4" />
            {{ processing ? 'Сохранение...' : (isEdit ? 'Сохранить изменения' : 'Создать план') }}
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
  name: 'AdmissionPlanForm',
  components: { DashboardIcon, FlashMessages },
  props: {
    plan: { type: Object, default: null },
    admissionCampaigns: { type: Array, required: true },
    educationalPrograms: { type: Array, required: true }
  },
  data() {
    return {
      form: {
        educational_programs_id: this.plan?.educational_programs_id || '',
        admission_campaigns_id: this.plan?.admission_campaigns_id || '',
        exams: this.plan?.exams ? JSON.parse(JSON.stringify(this.plan.exams)) : [],
        contests: this.plan?.contests ? JSON.parse(JSON.stringify(this.plan.contests)) : []
      },
      errors: {},
      processing: false
    }
  },
  computed: {
    isEdit() { return !!this.plan; }
  },
  mounted() {
    this.SET_DOCUMENT_TITLE(this.isEdit ? `Редактирование плана приема` : 'Создание плана приема');
  },
  methods: {
    getFormEducationLabel(value) {
      const labels = { 1: 'Очная', 2: 'Очно-заочная', 3: 'Заочная' };
      return labels[value] || null;
    },

    // Exams
    addExam() { this.form.exams.push({ title: '', priority: 0, types: [] }); },
    removeExam(idx) { this.form.exams.splice(idx, 1); },
    duplicateExam(idx) {
      const item = JSON.parse(JSON.stringify(this.form.exams[idx]));
      this.form.exams.splice(idx + 1, 0, item);
    },
    addExamType(examIdx) {
      if (!this.form.exams[examIdx].types) this.form.exams[examIdx].types = [];
      if (this.form.exams[examIdx].types.length < 2) {
        this.form.exams[examIdx].types.push({ type: '', min_ball: 0 });
      }
    },
    removeExamType(examIdx, typeIdx) {
      this.form.exams[examIdx].types.splice(typeIdx, 1);
    },

    // Contests
    addContest() { this.form.contests.push({ form_education: '', places: { form_budget: '', count: 0 } }); },
    removeContest(idx) { this.form.contests.splice(idx, 1); },
    duplicateContest(idx) {
      const item = JSON.parse(JSON.stringify(this.form.contests[idx]));
      this.form.contests.splice(idx + 1, 0, item);
    },

    submit() {
      this.processing = true;
      this.errors = {};
      const url = this.isEdit
        ? route('dashboard.admission-plans.update', this.plan.id)
        : route('dashboard.admission-plans.store');
      const method = this.isEdit ? 'put' : 'post';

      this.$inertia[method](url, this.form, {
        onFinish: () => { this.processing = false; },
        onError: (errors) => { this.errors = errors; }
      });
    }
  }
}
</script>
