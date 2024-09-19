<template>
	<template v-for="file in block.data.file">
		<div class="mb-4">
			<a class="" :href="'/storage/'+ file.path" download type="button">
			<div class="flex border rounded-lg px-4 py-2 items-center justify-between duration-300 hover:bg-gray-100">
					<div class="flex items-center justify-between">
						<div class="min-w-[30px] min-h-[30px] bg-[#303030] flex justify-center items-center rounded-md mr-2">
							<BaseIcon :name="file.expansion" class="w-5 h-5 flex-shrink-0" />
						</div>
						<div>{{ textLimit(file.title, 70) }}</div>
					</div>
				<span class="text-sm text-gray-400">{{ file.size }}</span>

			</div>

			</a>
		</div>
	</template>

</template>

<script>
import slugify from "slugify";
import FsLightbox from "fslightbox-vue/v3";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";


export default {
	name: "FileBlock",
	components: {BaseIcon, FsLightbox },
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
		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText;
				LimitedText = text.substring(0, symbols);
				return LimitedText + "...";
			}
			return text;
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