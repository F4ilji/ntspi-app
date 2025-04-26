<template>

	<div v-if="images.length" class="grid gap-4 border-t border-gray-300 py-4">
		<div class="relative">
			<img loading="lazy"
					class="filter brightness-[0.8] w-full max-h-[500px] object-cover rounded-lg hover:opacity-95 hover:duration-200 transition"
					:src="'/storage/' + images[0]" data-hs-overlay="#modal-post-gallery" alt="">
			<div class="absolute inset-x-0 bottom-5 flex flex-col justify-center items-center">
				<p class="text-white text-sm lg:text-xl text-center">{{ title }}</p>
				<span class="text-gray-300 text-sm">{{ images.length }} фотографий</span>
			</div>
		</div>
	</div>

	<div id="modal-post-gallery" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal-post-gallery-label">
		<div class="hs-overlay-open:mt-0 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-10 opacity-0 transition-all max-w-full max-h-full h-full">
			<div class="flex flex-col bg-white pointer-events-auto max-w-full max-h-full h-full dark:bg-neutral-800">
				<div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
					<h3 id="modal-post-gallery-label" class="font-bold text-gray-800 dark:text-white">
						Галлерея: {{ title }}
					</h3>
					<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#modal-post-gallery">
						<span class="sr-only">Close</span>
						<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M18 6 6 18"></path>
							<path d="m6 6 12 12"></path>
						</svg>
					</button>
				</div>
				<div class="p-4 overflow-y-auto">
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 px-10 py-5">
            <div v-for="(image, index) in images" class="group block relative overflow-hidden rounded-lg aspect-square">
              <img
                  loading="lazy"
                  @click="openLightboxOnSlide(index + 1)"
                  class="w-full h-full object-cover bg-gray-100 rounded-lg"
                  :src="'/storage/' + image"
              >
              <div class="absolute bottom-1 end-1 opacity-0 group-hover:opacity-100 transition">
                <div class="flex items-center gap-x-1 py-1 px-2 bg-white border border-gray-200 text-gray-800 rounded-lg">
                  <svg class="shrink-0 size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                  </svg>
                  <span class="text-xs">Открыть</span>
                </div>
              </div>
            </div>
          </div>
				</div>
			</div>
		</div>
	</div>




	<FsLightbox class="" :slide="slide" :toggler="toggler" :sources="images.map(image => domainPath + '/storage/' + image)"/>



</template>

<script>
import {Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue";
export default {
	name: "PostGallery",
	components: {Link, FsLightbox},
	data() {
		return {
			toggler: false,
			slide: 1,
			domainPath: null,
		}
	},
	methods: {
		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText
				LimitedText = text.substring(0, symbols)
				return LimitedText + "..."
			}
			return text
		},
		openLightboxOnSlide: function(number) {
			this.slide = number;
			this.toggler = !this.toggler;
		}
	},
	mounted() {
		this.domainPath = window.location.origin;
	},

	props: {
		images: {
			type: Object,
		},
		title: {
			type: String,
		},
	},
}

</script>


<style scoped>

</style>