<!-- NavLink.vue -->
<template>
	<div>
		<template v-for="el in list">
			<h2 :id="'anchor-link-' + generateSlug(el.category_publication)" class="my-4 font-medium text-gray-800">{{ el.category_publication }}</h2>
			<ul class="list-disc ms-6 mt-3 space-y-1.5">
				<template v-for="el in el.publication">
					<li class="ps-1 text-sm text-gray-600 dark:text-neutral-400">
						{{ el.item }}
					</li>
				</template>
			</ul>
		</template>


	</div>
</template>

<script>
import slugify from "slugify";

export default {
	name: 'PublicationBlock',
	props: {
		list: {
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