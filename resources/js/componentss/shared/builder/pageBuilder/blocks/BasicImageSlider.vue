<template>
	<div class="relative">
		<div class="flex">
			<div
					v-for="(item, index) in items"
					:key="index"
					class="w-full h-[500px] overflow-hidden relative"
					:class="{ 'block': currentIndex === index, 'hidden': currentIndex !== index }"
			>
				<div class="absolute inset-0 bg-cover bg-center blur-lg" :style="{ backgroundImage: 'url(/storage/' + item + ')' }"></div>
				<img
						@click="openLightboxOnSlide(index + 1)"
						:src="'/storage/' + item"
						class="absolute inset-0 w-full h-full object-cover rounded-md hover:opacity-95 hover:duration-200 transition"
				/>
			</div>
		</div>
		<button
				class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
				@click="prevSlide"
		>
			&#10094;
		</button>
		<button
				class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
				@click="nextSlide"
		>
			&#10095;
		</button>
	</div>

	<figcaption class="mt-3 text-sm text-center text-gray-500 dark:text-neutral-500">
		{{ block.data.alt }}
	</figcaption>

	<FsLightbox class="" :slide="slide" :toggler="toggler" :sources="items.map(item => domainPath + '/storage/' + item)"/>
</template>

<script>
import FsLightbox from "fslightbox-vue";

export default {
	name: "ClientImageSlider",
  components: { FsLightbox },
	data() {
		return {
			currentIndex: 0,
			items: this.block.data.url,
			toggler: false,
			domainPath: null,
			slide: null,
		};
	},
	props: {
		block: {
			type: Object,
		},
	},
	methods: {
		prevSlide() {
			if (this.currentIndex === 0) {
				this.currentIndex = this.items.length - 1;
			} else {
				this.currentIndex--;
			}
		},
		nextSlide() {
			if (this.currentIndex === this.items.length - 1) {
				this.currentIndex = 0;
			} else {
				this.currentIndex++;
			}
		},
		openLightboxOnSlide: function (number) {
			this.slide = number;
			this.toggler = !this.toggler;
		}
	},
	mounted() {
		this.domainPath = window.location.origin;
	},
};
</script>

<style scoped>
.blur-lg {
	filter: blur(20px);
}
</style>