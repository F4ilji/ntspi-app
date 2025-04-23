<template>
  <div v-if="success" class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
    <div class="pb-10 px-5 text-center overflow-y-auto">
      <!-- Icon -->
      <span class="mb-4 inline-flex justify-center items-center size-11 rounded-full border-4 border-green-50 bg-green-100 text-green-500 dark:bg-green-700 dark:border-green-600 dark:text-green-100">
          <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
          </svg>
        </span>
      <!-- End Icon -->

      <h3 id="hs-task-created-alert-label" class="mb-8 text-xl font-bold text-gray-800 dark:text-neutral-200">
        Успех!
      </h3>
      <p class="text-gray-500 dark:text-neutral-500">
        {{ message }}
      </p>
    </div>
  </div>


  <div v-else>
    <div class="max-w-[85rem] py-5 sm:px-6 lg:px-8 lg:py-7 mx-auto">
      <div class="mx-auto max-w-2xl">
        <div class="text-center">
          <h2 class="text-xl text-gray-800 font-bold sm:text-3xl">
            {{ blocks.data.title }}
          </h2>
        </div>
        <!-- Card -->
        <div class="mt-5 p-4 relative z-1000 bg-white md:border rounded-xl sm:mt-10 md:p-10">
          <form @submit="submitForm">
            <component
                v-for="(block, index) in blocks.data.columns"
                :key="index"
                :is="getComponent(block.type)"
                :block="block"
                :error="errors && errors[block.data.name_field] ? errors[block.data.name_field] : null"
            />

            <PersonalDataBlock v-if="blocks.data.settings.personal_data" />
            <CaptchaBlock v-if="blocks.data.settings.captcha" />

            <div class="mt-6">
              <button
                  type="submit"
                  class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary text-white hover:bg-primary-light disabled:opacity-50 disabled:pointer-events-none"
                  :disabled="!isFormAvailable"
              >
                {{ blocks.data.button }}
              </button>
              <p v-if="!isFormAvailable" class="mt-2 text-sm text-red-600">
                {{ availabilityMessage }}
              </p>
            </div>
          </form>
        </div>
        <!-- End Card -->
      </div>
    </div>
  </div>

  <transition name="fade">
    <SuccessNotification v-if="success" :text="message" />
  </transition>
</template>

<script>
import SubmitBlock from "@/componentss/shared/builder/formBuilder/blocks/SubmitBlock.vue";
import axios from "axios";
import SuccessNotification from "@/componentss/shared/notifications/SuccessNotification.vue";
import {defineAsyncComponent} from "vue";
import PersonalDataBlock from "@/componentss/shared/builder/formBuilder/blocks/PersonalDataBlock.vue";
import CaptchaBlock from "@/componentss/shared/builder/formBuilder/blocks/CaptchaBlock.vue";

export default {
  name: "FormBuilder",
  components: {
    CaptchaBlock,
    PersonalDataBlock,
    SuccessNotification,
    SubmitBlock,
  },
  data() {
    return {
      formData: {},
      errors: null,
      success: false,
      message: null,
      currentTime: new Date(),
      timeInterval: null
    };
  },

  computed: {
    isFormAvailable() {
      if (!this.blocks.data.settings.period) return true;

      const startTime = new Date(this.blocks.data.settings.period.start_time);
      const endTime = new Date(this.blocks.data.settings.period.end_time);

      return this.currentTime >= startTime && this.currentTime <= endTime;
    },

    availabilityMessage() {
      if (!this.blocks.data.settings.period) return '';

      const startTime = new Date(this.blocks.data.settings.period.start_time);
      const endTime = new Date(this.blocks.data.settings.period.end_time);

      if (this.currentTime < startTime) {
        return `Форма будет доступна с ${this.formatDateTime(startTime)}`;
      } else if (this.currentTime > endTime) {
        return `Форма была доступна до ${this.formatDateTime(endTime)}`;
      }

      return '';
    }
  },

  mounted() {
    this.updateCurrentTime();
    this.timeInterval = setInterval(this.updateCurrentTime, 60000);
  },

  beforeUnmount() {
    if (this.timeInterval) {
      clearInterval(this.timeInterval);
    }
  },

  methods: {
    updateCurrentTime() {
      this.currentTime = new Date();
    },

    formatDateTime(date) {
      return date.toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    getComponent(type) {
      const componentMap = {
        text: () => import('@/componentss/shared/builder/formBuilder/blocks/TextBlock.vue'),
        phone: () => import('@/componentss/shared/builder/formBuilder/blocks/PhoneBlock.vue'),
        email: () => import('@/componentss/shared/builder/formBuilder/blocks/EmailBlock.vue'),
        textarea: () => import('@/componentss/shared/builder/formBuilder/blocks/TextAreaBlock.vue'),
        multiple_choice: () => import('@/componentss/shared/builder/formBuilder/blocks/MultipleChoiceBlock.vue'),
        single_choice: () => import('@/componentss/shared/builder/formBuilder/blocks/SingleChoiceBlock.vue'),
        date: () => import('@/componentss/shared/builder/formBuilder/blocks/DateBlock.vue'),
        additional_education_choice: () => import('@/componentss/shared/builder/formBuilder/blocks/AdditionalEducationalChoiceBlock.vue'),
        educational_program_choice: () => import('@/componentss/shared/builder/formBuilder/blocks/EducationalChoiceBlock.vue'),
        captcha: () => import('@/componentss/shared/builder/formBuilder/blocks/CaptchaBlock.vue'),
        personal_data: () => import('@/componentss/shared/builder/formBuilder/blocks/PersonalDataBlock.vue'),
      };
      return defineAsyncComponent(componentMap[type] || null);
    },

    submitForm(event) {
      if (!this.isFormAvailable) {
        event.preventDefault();
        return;
      }
      event.preventDefault();
      this.formData = this.getFormData(event.target.elements);
      this.sendDataToServer();
    },

    getFormData(formElements) {
      const formData = {};
      for (let i = 0; i < formElements.length; i++) {
        const element = formElements[i];
        if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
          const fieldName = this.normalizeFieldName(element.name);

          if (element.tagName === 'INPUT') {
            if (element.type === 'checkbox') {
              this.handleCheckbox(formData, fieldName, element);
            } else if (fieldName && fieldName !== 'choices') {
              formData[fieldName] = element.value;
            }
          } else if (element.tagName === 'TEXTAREA') {
            formData[fieldName] = element.value;
          } else if (element.tagName === 'SELECT') {
            formData[fieldName] = element.value;
          }
        }
      }
      return formData;
    },

    normalizeFieldName(name) {
      return name.endsWith('[]') ? name.slice(0, -2) : name;
    },

    handleCheckbox(formData, fieldName, element) {
      if (!formData[fieldName]) {
        formData[fieldName] = [];
      }
      if (element.checked) {
        formData[fieldName].push(element.value);
      }
    },

    sendDataToServer() {
      axios.post(route('client.widget.form.submit', this.blocks.data.id), this.formData)
          .then(this.handleResponse)
          .catch(this.handleError);
    },

    handleResponse(response) {
      if (response.data.status === 'ok') {
        this.success = true;
        this.message = response.data.message;
        this.errors = null;
      }
    },

    handleError(error) {
      this.errors = error.response.data || ['Неизвестная ошибка'];
      this.success = false;
    }
  },
  props: {
    blocks: {
      type: Object,
    },
  },
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(30px);
}
</style>