<template>
	<ul v-if="loading" class="mt-5 space-y-3 flex flex-col animate-pulse">
		<li class="w-full h-4 bg-gray-200 rounded-full"></li>
		<li class="w-full h-8 bg-gray-200 rounded-full"></li>
	</ul>

	<div v-else class="mb-4 sm:mb-8">
		<label :for="block.data.name_field + '-id'" class="block mb-2 text-sm font-medium">{{ block.data.title_field }}</label>
		<div class="relative">
			<select :name="block.data.name_field" :disabled="isActiveProgramPage" v-model="activeProgramPage" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
				<option :value="null" selected="">Без выбора</option>
				<option :value="additionalProgram.name" v-for="additionalProgram in additionalEducationalPrograms.data">{{ additionalProgram.name }}</option>
			</select>
			<div v-if="error" class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
				<svg class="shrink-0 size-4 text-red-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="10"></circle>
					<line x1="12" x2="12" y1="8" y2="12"></line>
					<line x1="12" x2="12.01" y1="16" y2="16"></line>
				</svg>
			</div>
		</div>
		<div class="flex items-center mt-2 justify-between flex-wrap">
			<p v-if="!error" class="text-sm text-gray-500" id="hs-input-helper-text">
				{{ block.data.description }}
			</p>
<!--			<p class="text-sm text-primary">{{ text.length }} / {{ block.data.rules.max }}</p>-->
		</div>
		<p v-for="item in error" class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">{{ item }}</p>

	</div>


</template>

<script>
import axios from "axios";

export default {
	name: "EducationalChoiceBlock",
	data() {
		return {
			additionalEducationalPrograms: null,
			loading: true, // Состояние загрузки
			activeProgramPage: null,
			isActiveProgramPage: false,
		}
	},
	methods: {
		getPrograms() {
			return axios.get(route('client.widget.educational.program.index'))
					.then(response => {
						this.additionalEducationalPrograms = response.data;
						this.loading = false; // Установить состояние загрузки в false
					})
					.catch(error => {
						console.error('Ошибка:', error);
						this.loading = false; // Установить состояние загрузки в false даже при ошибке
					});
		},
		isAdditionalEducationalRoute() {
			const slug = this.getSlugFromUrl(this.$page.props.ziggy.location);
			return this.$page.props.ziggy.location === route('client.program.show', slug);
		},
		getSlugFromUrl(url) {
			const segments = url.split('/');
			return segments[segments.length - 1];
		},
		findItemBySlug() {
			const slug = this.getSlugFromUrl(this.$page.props.ziggy.location)
			return this.additionalEducationalPrograms.data.find(item => item.slug === slug) || null;
		}
	},
	mounted() {
		this.getPrograms().then(() => {
			if (this.isAdditionalEducationalRoute()) {
				this.isActiveProgramPage = true;
				this.activeProgramPage = this.findItemBySlug().name;
			}
		});
	},
	props: {
		block: {
			type: Object,
		},
		error: {
			type: Object,
		}

	},
}
</script>

