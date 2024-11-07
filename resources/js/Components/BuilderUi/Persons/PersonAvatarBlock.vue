<template>
	<div class="">
		<img @click="toggler = !toggler" class="shrink-0 w-full md:w-[200px] md:h-[300px] rounded-xl object-cover" :src="'/storage/' + photo" alt="Avatar">
	</div>

	<FsLightbox class="" :toggler="toggler" :sources="[domainPath + '/storage/' + photo]"/>

</template>

<script>
import slugify from "slugify";
import FsLightbox from "fslightbox-vue/v3";


export default {
	name: "PersonAvatarBlock",
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
		photo: {
			type: String,
		},
	},
}
</script>

<style>

.fslightbox-container {
	margin: 0 !important;
}

</style>