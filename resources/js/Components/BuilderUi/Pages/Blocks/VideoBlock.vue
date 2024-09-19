<template>
	<video class="h-full w-full rounded-lg" controls>
		<source
				:src="domainPath + '/storage/' + block.data.path"
				:type="block.data.mime"
		/>
		Your browser does not support the video tag.
	</video>
	<figcaption class="mt-3 text-sm text-center text-gray-500 dark:text-neutral-500">
		{{ block.data.title }}
	</figcaption>
</template>

<script>
import slugify from "slugify";
import FsLightbox from "fslightbox-vue/v3";


export default {
	name: "VideoBlock",

	data() {
		return {
			toggler: false,
			domainPath: null,
		}
	},
	methods: {
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



</style>