<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="pencil-square" size="5" class="text-primary" />
    </template>
    <template #header-title>Редактирование детальной информации</template>
    <template #header-subtitle>{{ user.name }}</template>
    <template #header-actions>
      <a
        :href="route('dashboard.users.edit', user.id)"
        class="inline-flex items-center gap-2 px-4 py-2 bg-surface text-foreground text-sm font-medium rounded-lg border border-layer-line hover:bg-muted-hover transition-all duration-200"
      >
        <DashboardIcon name="arrow-left" size="4" />
        Назад к пользователю
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Form Card -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <form @submit.prevent="submit">
        <div class="p-6 space-y-6">
          <!-- Только сотрудник -->
          <div class="flex items-center gap-4">
            <label class="flex items-center gap-2">
              <input
                v-model="form.is_only_worker"
                type="checkbox"
                class="h-4 w-4 text-primary focus:ring-primary border-layer-line rounded"
              />
              <span class="text-sm font-medium text-foreground">Только сотрудник</span>
            </label>
          </div>

          <!-- Tabs -->
          <div>
            <!-- Tab Navigation -->
            <div class="border-b border-layer-line mb-6">
              <nav class="-mb-px flex space-x-8">
                <button
                  v-for="tab in tabs"
                  :key="tab.key"
                  type="button"
                  @click="activeTab = tab.key"
                  :class="[
                    activeTab === tab.key
                      ? 'border-primary text-primary'
                      : 'border-transparent text-muted-foreground-1 hover:text-foreground hover:border-line-2',
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                  ]"
                >
                  {{ tab.label }}
                </button>
              </nav>
            </div>

            <!-- Tab Content: Основная информация -->
            <div v-show="activeTab === 'main'">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-foreground mb-1">
                    Фотография
                  </label>

                  <!-- Текущее фото -->
                  <div v-if="form.photo && typeof form.photo === 'string'" class="mb-2">
                    <img :src="RESOLVE_ASSET_URL(form.photo)" alt="Фото" class="w-24 h-24 rounded-full object-cover" />
                    <p class="text-xs text-muted-foreground-1 mt-1">Текущее фото</p>
                  </div>

                  <input
                    ref="photoInput"
                    type="file"
                    accept="image/*"
                    @change="handlePhotoChange"
                    class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  />
                  <p class="mt-1 text-xs text-muted-foreground-1">Оставьте пустым, чтобы сохранить текущее фото</p>
                  <p v-if="fileError" class="mt-1 text-sm text-danger">{{ fileError }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-foreground mb-1">
                      Контактный Email
                    </label>
                    <input
                      v-model="form.contactEmail"
                      type="email"
                      maxlength="255"
                      placeholder="contact@example.com"
                      class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                    <p v-if="errors.contactEmail" class="mt-1 text-sm text-danger">{{ errors.contactEmail }}</p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-foreground mb-1">
                      Контактный телефон
                    </label>
                    <input
                      v-model="form.contactPhone"
                      type="text"
                      maxlength="255"
                      placeholder="+7 (999) 123-45-67"
                      class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab Content: Образование -->
            <div v-show="activeTab === 'education'" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-foreground mb-1">
                    Ученая степень
                  </label>
                  <input
                    v-model="form.academicTitle"
                    type="text"
                    maxlength="255"
                    placeholder="к.т.н., доцент"
                    class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-foreground mb-1">
                    Ученое звание
                  </label>
                  <input
                    v-model="form.AcademicDegree"
                    type="text"
                    maxlength="255"
                    placeholder="Доцент кафедры"
                    class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  />
                </div>
              </div>

              <!-- Образование (repeater) -->
              <div class="border border-layer-line rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-sm font-medium text-foreground">Образование</h4>
                  <button type="button" @click="addEducation" class="text-xs text-primary hover:underline">
                    + Добавить
                  </button>
                </div>
                <div v-for="(edu, index) in form.education" :key="index" class="mb-3 p-3 bg-muted/30 rounded-lg">
                  <div class="flex justify-between mb-2">
                    <span class="text-xs font-medium text-foreground">#{{ index + 1 }}</span>
                    <button type="button" @click="removeEducation(index)" class="text-danger text-xs hover:underline">
                      Удалить
                    </button>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <input v-model="edu.year" type="text" placeholder="Года обучения" class="px-3 py-2 border border-layer-line rounded-lg text-sm" />
                    <input v-model="edu.content" type="text" placeholder="Общая информация" class="px-3 py-2 border border-layer-line rounded-lg text-sm" />
                    <input v-model="edu.institution" type="text" placeholder="Учебное заведение" class="px-3 py-2 border border-layer-line rounded-lg text-sm" />
                  </div>
                </div>
                <p v-if="!form.education || form.education.length === 0" class="text-xs text-muted-foreground-1 text-center py-2">
                  Нет записей об образовании
                </p>
              </div>

              <!-- Профессиональная переподготовка -->
              <div class="border border-layer-line rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-sm font-medium text-foreground">Профессиональная переподготовка</h4>
                  <button type="button" @click="addProfessionalRetraining" class="text-xs text-primary hover:underline">
                    + Добавить
                  </button>
                </div>
                <div v-for="(item, index) in form.professionalRetraining" :key="index" class="mb-2 flex gap-2">
                  <input v-model="item.item" type="text" placeholder="" class="flex-1 px-3 py-2 border border-layer-line rounded-lg text-sm" />
                  <button type="button" @click="removeProfessionalRetraining(index)" class="text-danger text-xs hover:underline px-2">
                    ✕
                  </button>
                </div>
                <p v-if="!form.professionalRetraining || form.professionalRetraining.length === 0" class="text-xs text-muted-foreground-1 text-center py-2">
                  Нет записей
                </p>
              </div>

              <!-- Повышение квалификации -->
              <div class="border border-layer-line rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-sm font-medium text-foreground">Повышение квалификации</h4>
                  <button type="button" @click="addProfessionalDevelopment" class="text-xs text-primary hover:underline">
                    + Добавить
                  </button>
                </div>
                <div v-for="(item, index) in form.professionalDevelopment" :key="index" class="mb-2 flex gap-2">
                  <input v-model="item.item" type="text" placeholder="" class="flex-1 px-3 py-2 border border-layer-line rounded-lg text-sm" />
                  <button type="button" @click="removeProfessionalDevelopment(index)" class="text-danger text-xs hover:underline px-2">
                    ✕
                  </button>
                </div>
                <p v-if="!form.professionalDevelopment || form.professionalDevelopment.length === 0" class="text-xs text-muted-foreground-1 text-center py-2">
                  Нет записей
                </p>
              </div>

              <!-- Награды -->
              <div class="border border-layer-line rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-sm font-medium text-foreground">Награды</h4>
                  <button type="button" @click="addAwards" class="text-xs text-primary hover:underline">
                    + Добавить
                  </button>
                </div>
                <div v-for="(item, index) in form.awards" :key="index" class="mb-2 flex gap-2">
                  <input v-model="item.item" type="text" placeholder="" class="flex-1 px-3 py-2 border border-layer-line rounded-lg text-sm" />
                  <button type="button" @click="removeAwards(index)" class="text-danger text-xs hover:underline px-2">
                    ✕
                  </button>
                </div>
                <p v-if="!form.awards || form.awards.length === 0" class="text-xs text-muted-foreground-1 text-center py-2">
                  Нет записей
                </p>
              </div>
            </div>

            <!-- Tab Content: Преподавание -->
            <div v-show="activeTab === 'teaching'" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-foreground mb-1">
                    Стаж работы в общем
                  </label>
                  <input
                    v-model.number="form.workExperience.total"
                    type="number"
                    placeholder="0"
                    class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-foreground mb-1">
                    Стаж работы по специальности
                  </label>
                  <input
                    v-model.number="form.workExperience.byProf"
                    type="number"
                    placeholder="0"
                    class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  />
                </div>
              </div>

              <!-- Преподаваемые дисциплины -->
              <div class="border border-layer-line rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-sm font-medium text-foreground">Преподаваемые дисциплины</h4>
                  <button type="button" @click="addProfessDisciplines" class="text-xs text-primary hover:underline">
                    + Добавить
                  </button>
                </div>
                <div v-for="(item, index) in form.professDisciplines" :key="index" class="mb-2 flex gap-2">
                  <input v-model="item.item" type="text" placeholder="" class="flex-1 px-3 py-2 border border-layer-line rounded-lg text-sm" />
                  <button type="button" @click="removeProfessDisciplines(index)" class="text-danger text-xs hover:underline px-2">
                    ✕
                  </button>
                </div>
                <p v-if="!form.professDisciplines || form.professDisciplines.length === 0" class="text-xs text-muted-foreground-1 text-center py-2">
                  Нет записей
                </p>
              </div>
            </div>

            <!-- Tab Content: Научная деятельность -->
            <div v-show="activeTab === 'science'" class="space-y-6">
              <!-- Участие в конференциях -->
              <div class="border border-layer-line rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-sm font-medium text-foreground">Участие в выставках, конференциях, проектах</h4>
                  <button type="button" @click="addAttendedConferences" class="text-xs text-primary hover:underline">
                    + Добавить
                  </button>
                </div>
                <div v-for="(item, index) in form.attendedConferences" :key="index" class="mb-2 flex gap-2">
                  <input v-model="item.item" type="text" placeholder="" class="flex-1 px-3 py-2 border border-layer-line rounded-lg text-sm" />
                  <button type="button" @click="removeAttendedConferences(index)" class="text-danger text-xs hover:underline px-2">
                    ✕
                  </button>
                </div>
                <p v-if="!form.attendedConferences || form.attendedConferences.length === 0" class="text-xs text-muted-foreground-1 text-center py-2">
                  Нет записей
                </p>
              </div>

              <!-- Публикации -->
              <div class="border border-layer-line rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-sm font-medium text-foreground">Публикации</h4>
                  <button type="button" @click="addPublication" class="text-xs text-primary hover:underline">
                    + Добавить категорию
                  </button>
                </div>
                <div v-for="(pub, index) in form.publications" :key="index" class="mb-4 p-3 bg-muted/30 rounded-lg">
                  <div class="flex justify-between mb-2">
                    <span class="text-xs font-medium text-foreground">Категория #{{ index + 1 }}</span>
                    <button type="button" @click="removePublication(index)" class="text-danger text-xs hover:underline">
                      Удалить
                    </button>
                  </div>
                  <input v-model="pub.category_publication" type="text" placeholder="Категория публикаций" class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm mb-2" />
                  <div v-for="(item, itemIndex) in pub.publication" :key="itemIndex" class="mb-2 flex gap-2 ml-4">
                    <input v-model="item.item" type="text" placeholder="Публикация" class="flex-1 px-3 py-2 border border-layer-line rounded-lg text-sm" />
                    <button type="button" @click="removePublicationItem(index, itemIndex)" class="text-danger text-xs hover:underline px-2">
                      ✕
                    </button>
                  </div>
                  <button type="button" @click="addPublicationItem(index)" class="text-xs text-primary hover:underline ml-4">
                    + Добавить публикацию
                  </button>
                </div>
                <p v-if="!form.publications || form.publications.length === 0" class="text-xs text-muted-foreground-1 text-center py-2">
                  Нет публикаций
                </p>
              </div>
            </div>

            <!-- Tab Content: Другое (ContentBuilder) -->
            <div v-show="activeTab === 'other'">
              <ContentBuilder
                v-model="form.other"
                label="Другое"
              />
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-surface/50 border-t border-layer-line flex items-center justify-end gap-3">
          <a
            :href="route('dashboard.users.edit', user.id)"
            class="px-4 py-2 text-sm font-medium text-foreground bg-surface border border-layer-line rounded-lg hover:bg-muted-hover transition-all"
          >
            Отмена
          </a>
          <button
            type="submit"
            :disabled="processing"
            class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ processing ? 'Сохранение...' : 'Сохранить изменения' }}
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../Components/DashboardLayout.vue';
import DashboardIcon from '../../Components/DashboardIcon.vue';
import FlashMessages from '../../Components/shared/FlashMessages.vue';
import ContentBuilder from '../../Components/ContentBuilder/ContentBuilder.vue';

export default {
  name: 'UserDetailEdit',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
    ContentBuilder,
  },

  props: {
    user: {
      type: Object,
      required: true
    },
    userDetail: {
      type: Object,
      required: true
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      activeTab: 'main',
      tabs: [
        { key: 'main', label: 'Основная информация' },
        { key: 'education', label: 'Образование' },
        { key: 'teaching', label: 'Преподавание' },
        { key: 'science', label: 'Научная деятельность' },
        { key: 'other', label: 'Другое' },
      ],
      form: {
        is_only_worker: this.userDetail?.is_only_worker || false,
        photo: this.userDetail?.photo || null,
        contactEmail: this.userDetail?.contactEmail || '',
        contactPhone: this.userDetail?.contactPhone || '',
        academicTitle: this.userDetail?.academicTitle || '',
        AcademicDegree: this.userDetail?.AcademicDegree || '',
        workExperience: this.userDetail?.workExperience || { total: null, byProf: null },
        education: this.userDetail?.education || [],
        professionalRetraining: this.userDetail?.professionalRetraining || [],
        professionalDevelopment: this.userDetail?.professionalDevelopment || [],
        awards: this.userDetail?.awards || [],
        professDisciplines: this.userDetail?.professDisciplines || [],
        attendedConferences: this.userDetail?.attendedConferences || [],
        publications: this.userDetail?.publications || [],
        other: this.userDetail?.other || []
      },
      fileError: null,
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Редактирование детальной информации');
  },

  methods: {
    handlePhotoChange(event) {
      const file = event.target.files[0];
      this.fileError = null;

      if (!file) return;

      if (!file.type.startsWith('image/')) {
        this.fileError = 'Выберите изображение';
        event.target.value = '';
        return;
      }

      const maxSize = 10 * 1024 * 1024;
      if (file.size > maxSize) {
        this.fileError = 'Размер файла не должен превышать 10MB';
        event.target.value = '';
        return;
      }

      this.form.photo = file;
    },

    // Education repeater
    addEducation() { this.form.education.push({ year: '', content: '', institution: '' }); },
    removeEducation(index) { this.form.education.splice(index, 1); },

    // Professional retraining
    addProfessionalRetraining() { this.form.professionalRetraining.push({ item: '' }); },
    removeProfessionalRetraining(index) { this.form.professionalRetraining.splice(index, 1); },

    // Professional development
    addProfessionalDevelopment() { this.form.professionalDevelopment.push({ item: '' }); },
    removeProfessionalDevelopment(index) { this.form.professionalDevelopment.splice(index, 1); },

    // Awards
    addAwards() { this.form.awards.push({ item: '' }); },
    removeAwards(index) { this.form.awards.splice(index, 1); },

    // Profess disciplines
    addProfessDisciplines() { this.form.professDisciplines.push({ item: '' }); },
    removeProfessDisciplines(index) { this.form.professDisciplines.splice(index, 1); },

    // Attended conferences
    addAttendedConferences() { this.form.attendedConferences.push({ item: '' }); },
    removeAttendedConferences(index) { this.form.attendedConferences.splice(index, 1); },

    // Publications
    addPublication() { this.form.publications.push({ category_publication: '', publication: [] }); },
    removePublication(index) { this.form.publications.splice(index, 1); },
    addPublicationItem(pubIndex) { this.form.publications[pubIndex].publication.push({ item: '' }); },
    removePublicationItem(pubIndex, itemIndex) { this.form.publications[pubIndex].publication.splice(itemIndex, 1); },

    submit() {
      this.processing = true;

      const formData = new FormData();
      formData.append('is_only_worker', this.form.is_only_worker ? 1 : 0);
      formData.append('contactEmail', this.form.contactEmail);
      formData.append('contactPhone', this.form.contactPhone);
      formData.append('academicTitle', this.form.academicTitle);
      formData.append('AcademicDegree', this.form.AcademicDegree);
      formData.append('workExperience', JSON.stringify(this.form.workExperience));
      formData.append('education', JSON.stringify(this.form.education));
      formData.append('professionalRetraining', JSON.stringify(this.form.professionalRetraining));
      formData.append('professionalDevelopment', JSON.stringify(this.form.professionalDevelopment));
      formData.append('awards', JSON.stringify(this.form.awards));
      formData.append('professDisciplines', JSON.stringify(this.form.professDisciplines));
      formData.append('attendedConferences', JSON.stringify(this.form.attendedConferences));
      formData.append('publications', JSON.stringify(this.form.publications));
      formData.append('other', JSON.stringify(this.form.other));
      formData.append('_method', 'PUT');

      if (this.form.photo instanceof File) {
        formData.append('photo', this.form.photo);
      }

      this.$inertia.post(
        route('dashboard.users.detail.update', { user: this.user.id, userDetail: this.userDetail.id }),
        formData,
        {
          forceFormData: true,
          onFinish: () => {
            this.processing = false;
          }
        }
      );
    }
  }
}
</script>
