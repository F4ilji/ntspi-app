<template>
	<div class="w-full rounded-xl mb-4 p-4 md:p-6 bg-white border border-gray-200 ">
		<div class="flex items-center gap-y-4 gap-x-4 flex-wrap md:flex-nowrap">
			<img v-if="block.data.photo" @click="toggler = !toggler" loading="lazy" class="rounded-xl md:w-[150px]" :src="'/storage/' + block.data.photo" alt="Image Description">
			<div class="grow overflow-x-auto">
				<p class="font-medium text-gray-800 hover:text-gray-500">
					{{ block.data.name }}
				</p>
				<template v-for="item in block.data.info">
					<p class="text-xs text-gray-500 mt-2">
						{{ item.column }}: {{ item.content }}
					</p>
				</template>

			</div>
		</div>

		<!-- Social Brands -->
		<!-- End Social Brands -->
	</div>

	<FsLightbox class="" :toggler="toggler" :sources="[domainPath + '/storage/' + block.data.photo]"/>



</template>

<script>
import slugify from "slugify";
import FsLightbox from "fslightbox-vue";

export default {
	name: "PersonBlock",
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

<style scoped>

.fslightbox-container {
	margin: 0 !important;
}



</style>