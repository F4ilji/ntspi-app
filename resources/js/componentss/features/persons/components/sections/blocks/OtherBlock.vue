<!-- NavLink.vue -->
<template>
	<div>
		<h2 :id="generateSlug(nameList)" class="mb-3 font-medium text-gray-800 dark:text-neutral-200">
			{{ nameList }}
		</h2>
		<Builder :blocks="blocks" />
	</div>
</template>

<script>
import slugify from "slugify";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";

export default {
	name: 'OtherBlock',
	components: {Builder},
	props: {
		blocks: {
			type: String,
			required: true
		},
		nameList: {
			type: String,
			required: true,
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