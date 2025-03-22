<template>
	<div>
			<img @click="toggler = !toggler" loading="lazy" class="mx-auto object-cover rounded-md hover:opacity-95 hover:duration-200 transition" :src="'/storage/' + block.data.url" alt="">
			<div v-if="block.data.alt" class="mt-3 text-sm text-center text-gray-500 dark:text-neutral-500">
			{{ block.data.alt }}
			</div>
	</div>

	<FsLightbox class="" :toggler="toggler" :sources="[domainPath + '/storage/' + block.data.url]"/>

</template>

<script>
import slugify from "slugify";
import FsLightbox from "fslightbox-vue";


export default {
	name: "ImageBlock",
	components: { FsLightbox },
	data() {
		return {
			toggler: false,
			domainPath: null,
		}
	},
	methods: {
		generateSlug: function (text) {
			return slugify(text, {
				lower: true,
				strict: true,
				locale: 'ru'
			});
		},
	},
	mounted() {
		this.domainPath = window.location.origin;
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