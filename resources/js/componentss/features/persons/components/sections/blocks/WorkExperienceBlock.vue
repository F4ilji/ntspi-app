<!-- NavLink.vue -->
<template>
	<h2 :id="generateSlug(title)" class="mb-3 font-medium text-gray-800 dark:text-neutral-200">
		{{ title }}
	</h2>
	<div class="grid grid-cols-1 sm:grid-cols-1 gap-3">
		<p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
			Стаж работы: {{ workExpTotal }}
		</p>
		<p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
			Стаж по специальности: {{ workExpByProf }}
		</p>
	</div>
</template>

<script>
import slugify from "slugify";

export default {
	name: 'WorkExperienceBlock',
	props: {
		workExpTotal: {
			type: String,
			required: true
		},
		workExpByProf: {
			type: String,
			required: true
		},
		title: {
			type: String,
			required: true
		},
	},
	methods: {
		generateSlug(text) {
			return slugify(text, {
				lower: true,
				strict: true,
				locale: "ru",
			});
		},
	}
}
</script>

<style scoped>
/* Добавьте стили, если это необходимо */
</style>