<template>
  <div class="relative rounded-md overflow-hidden">
    <div
        class="absolute inset-0 bg-cover bg-center blur-md brightness-75"
        :style="`background-image: url('${'/storage/' + block.data.url}')`"
    ></div>

    <div class="relative z-10 md:m-4"> <img
        @click="toggler = !toggler"
        loading="lazy"
        class="mx-auto h-96 rounded-md object-cover md:object-contain hover:opacity-95 hover:duration-200 transition"
        :src="'/storage/' + block.data.url"
        alt=""
    />
      <div v-if="block.data.alt" class="mt-3 text-sm text-center text-gray-500 dark:text-neutral-500">
        {{ block.data.alt }}
      </div>
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