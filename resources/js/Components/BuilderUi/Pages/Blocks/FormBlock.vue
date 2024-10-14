<template>
	<div v-if="loading" class="flex flex-col space-y-4">
		<div class="flex-col animate-pulse mt-10">
			<div class="w-[25rem] mx-auto h-8 bg-gray-200 rounded-full"></div>
			<div class="mt-5 mx-auto w-[40rem] h-60 relative z-1000 border rounded-xl sm:mt-10 md:p-10 bg-gray-200">
			</div>
		</div>
	</div>
	<div v-else>
		<FormBuilder :blocks="form" />
	</div>
</template>

<script>
import slugify from "slugify";
import FsLightbox from "fslightbox-vue/v3";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import axios from "axios";
import {Link} from "@inertiajs/vue3";
import FormBuilder from "@/Components/BuilderUi/Pages/FormBuilder.vue";
export default {
	name: "FormBlock",
	components: {FormBuilder, axios, Link },
	data() {
		return {
			form: null,
			loading: true, // Состояние загрузки
		}
	},
	methods: {
		getForm(id) {
			axios.get(route('client.widget.form.single', id))
					.then(response => {
						this.form = response.data;
						this.loading = false; // Установить состояние загрузки в false
					})
					.catch(error => {
						console.error('Ошибка:', error);
					});
		}
	},
	mounted() {
		const id = this.block?.data.form || this.formId
		this.getForm(id);
	},
	props: {
		block: {
			type: Object,
		},
		formId: {
			type: String,
			default: null,
		}
	},
}
</script>

<style>

.fslightbox-container {
	margin: 0 !important;
}

</style>