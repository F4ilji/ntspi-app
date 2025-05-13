<!-- NavLink.vue -->
<template>
	<div>
		<h2 :id="'anchor-link-' + generateSlug(title)" class="mb-3 font-medium text-gray-800 dark:text-neutral-200">
			{{ title }}
		</h2>

		<template v-for="education in educations">
			<div class="grid grid-cols-1 sm:grid-cols-1 my-4">
				<div class="p-4 border border-gray-200 rounded-lg dark:border-neutral-700">
					<h3 class="mb-1 text-xs text-gray-600 dark:text-neutral-400">
						{{ education.year }}
					</h3>

					<p class="font-semibold text-sm text-gray-800 dark:text-neutral-200">
						{{ education.content }}
					</p>

					<p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
						{{ education.institution }}
					</p>
				</div>

			</div>

		</template>

	</div>
</template>

<script>
import slugify from "slugify";

export default {
	name: 'EducationBlock',
	props: {
		educations: {
			type: Object,
			required: true
		},
		title: {
			type: String,
			required: true
		}
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