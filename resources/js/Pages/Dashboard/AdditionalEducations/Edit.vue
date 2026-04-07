<template>
  <div class="min-h-screen bg-background-2">
    <!-- Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.additional-educations.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="pencil-square" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Редактирование программы ДПО</h1>
              <p class="text-xs text-muted-foreground-1">{{ education?.title }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <AdditionalEducationForm
        :education="education"
        :categories="categories"
        :education-forms="educationForms"
        submit-label="Сохранить изменения"
        submit-route="dashboard.additional-educations.update"
        submit-method="put"
      />
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../Components/DashboardIcon.vue';
import AdditionalEducationForm from './AdditionalEducationForm.vue';

export default {
  name: 'AdditionalEducationEdit',
  components: {
    DashboardIcon,
    AdditionalEducationForm
  },

  props: {
    education: {
      type: Object,
      required: true
    },
    categories: {
      type: Array,
      required: true
    },
    educationForms: {
      type: Array,
      required: true
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование программы ДПО - ${this.education?.title}`);
  }
}
</script>
