<template>
	<div v-if="loading" class="flex flex-col space-y-4">
		<div class="flex animate-pulse">
			<div class="shrink-0 relative rounded-xl overflow-hidden w-full sm:w-56 h-44">
				<div class="bg-gray-200 group-focus:scale-105 transition-transform duration-500 ease-in-out size-full absolute top-0 start-0 object-cover rounded-xl" />
			</div>
			<div class="ms-4 mt-2 w-full">
				<p class="h-4 bg-gray-200 rounded-full" style="width: 40%;"></p>
				<ul class="mt-5 space-y-3 flex flex-col">
					<li class="w-full h-4 bg-gray-200 rounded-full"></li>
					<li class="w-full h-4 bg-gray-200 rounded-full"></li>
					<li class="w-full h-4 bg-gray-200 rounded-full"></li>
					<li class="w-full h-4 bg-gray-200 rounded-full"></li>
					<li class="w-20 h-4 bg-gray-200 rounded-full"></li>
				</ul>
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
		this.getForm(this.block.data.form);
	},
	props: {
		block: {
			type: Object,
		},
	},
}
</script>

<style>

.fslightbox-container {
	margin: 0 !important;
}

</style>