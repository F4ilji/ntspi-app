<template>
	<div v-if="success" class="bg-teal-50 border-t-2 border-teal-500 rounded-lg p-4 dark:bg-teal-800/30" role="alert" tabindex="-1" aria-labelledby="hs-bordered-success-style-label">
		<div class="flex">
			<div class="shrink-0">
				<!-- Icon -->
				<span class="inline-flex justify-center items-center size-8 rounded-full border-4 border-teal-100 bg-teal-200 text-teal-800 dark:border-teal-900 dark:bg-teal-800 dark:text-teal-400">
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
            <path d="m9 12 2 2 4-4"></path>
          </svg>
        </span>
				<!-- End Icon -->
			</div>
			<div class="ms-3">
				<h3 id="hs-bordered-success-style-label" class="text-gray-800 font-semibold dark:text-white">
					Успешно отправлено!
				</h3>
				<p class="text-sm text-gray-700 dark:text-neutral-400">
					{{ message }}
				</p>
			</div>
		</div>
	</div>


	<div v-else class="max-w-[85rem] py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">

		<div class="mx-auto max-w-2xl">
			<div class="text-center">
				<h2 class="text-xl text-gray-800 font-bold sm:text-3xl">
					{{ blocks.data.title }}
				</h2>
			</div>
			<!-- Card -->
			<div class="mt-5 p-4 relative z-1000 bg-white border rounded-xl sm:mt-10 md:p-10">
				<form @submit="submitForm">
					<component
							v-for="(block, index) in blocks.data.columns"
							:key="index"
							:is="getComponent(block.type)"
							:block="block"
							:error="errors && errors[block.data.name_field] ? errors[block.data.name_field] : null"
					/>
					<SubmitBlock :block="blocks.data.button" />
				</form>
			</div>
			<!-- End Card -->
		</div>
	</div>

	<transition name="fade">
		<SuccessNotification v-if="success" :text="message" />
	</transition>

</template>

<script>
import SubmitBlock from "@/componentss/shared/builder/formBuilder/blocks/SubmitBlock.vue";
import axios from "axios";
import SuccessNotification from "@/Components/Notifications/SuccessNotification.vue";
import {defineAsyncComponent} from "vue";

export default {
	name: "FormBuilder",
	components: {
		SuccessNotification,
		SubmitBlock,
	},
	data() {
		return {
			formData: {},
			errors: null,
			success: false,
			message: null,
		};
	},

	methods: {
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
				this.errors = null; // Сбрасываем ошибки при успешной отправке
			}
		},

		handleError(error) {
			this.errors = error.response.data || ['Неизвестная ошибка']; // Обработка ошибок
			this.success = false; // Сбрасываем успешное состояние
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