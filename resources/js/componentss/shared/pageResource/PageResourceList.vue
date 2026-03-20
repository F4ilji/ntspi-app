<template>
	<div v-if="loading" class="flex flex-col space-y-4">
		<div class="group block rounded-xl overflow-hidden animate-pulse">
			<div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
				<div class="shrink-0 relative rounded-xl overflow-hidden w-full sm:w-56 h-44">
					<div class="w-full h-full bg-gray-200"></div>
				</div>
			</div>
		</div>
	</div>

	<div v-else-if="!loading && resource">

		<!-- Card Blog -->
		<div class="px-0 py-10 sm:px-2 lg:py-14 mx-auto">
			<!-- Title -->
			<div class="max-w-2xl text-center mx-auto mb-10 lg:mb-14">
				<h2 class="text-2xl font-bold md:text-4xl md:leading-tight">Полезные ресурсы</h2>
<!--				<p class="mt-1 text-gray-600">We've helped some great companies brand, design and get to market.</p>-->
			</div>
			<!-- End Title -->

			<!-- Grid -->
			<div class="flex overflow-x-auto overflow-y-hidden space-x-6 pb-10 lg:mb-14 p-4">
				<!-- Card -->

        <BasicCarousel
            :items="resource.data.content"
            :items-to-show-desktop="4"
            :items-to-show-mobile="1"
            :mobile-breakpoint="768"
        >
          <template #item="{ item, index }">
            <div class="h-full flex items-center justify-center">
              <a class="group flex-shrink-0 w-[18rem] flex flex-col bg-white border shadow-sm rounded-xl focus:outline-none focus:shadow-md transition" :href="item.link">
                <div class="aspect-w-16 aspect-h-9 p-3 rounded-xl m-1 group-hover:bg-gray-100">
                  <img v-if="!item.image || item.image.length > 0" class="w-full backdrop-blur-xl rounded-xl object-cover h-[150px]" :src="'/storage/' + item.image" alt="Blog Image">
                  <div v-else class="w-full rounded-xl object-cover h-[150px] flex justify-center items-center">
                    <BasicIcon class="w-10 h-10 text-gray-500" :name="item.icon" />
                  </div>
                </div>
                <div class="px-5 pb-4">
                  <h3 class="mt-2 truncate text-base font-medium text-gray-800 group-hover:text-primary-hover">{{ item.title }}</h3>
                  <p class="mt-2 text-xs text-gray-600">{{ item.link_text }}</p>
                </div>
              </a>
            </div>
          </template>
        </BasicCarousel>



      </div>
			<!-- End Grid -->

			<!-- Card -->
			<!-- End Card -->
		</div>
		<!-- End Card Blog -->
	</div>
</template>

<script>

import axios from "axios";
import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import BasicCarousel from "@/componentss/shared/carousel/BasicCarousel.vue";
export default {
	name: "PageResourceList",
	components: {BasicCarousel, BasicIcon, axios, Link },
	data() {
		return {
			resource: null,
			loading: true,
			colors: ['from-primary', 'from-secondary'],
		}
	},
	methods: {
		getResource(id) {
			axios.get(route('client.widget.page.resource.show', id))
					.then(response => {
						this.resource = response.data;
						this.loading = false; // Установить состояние загрузки в false
					})
					.catch(error => {
						console.error('Ошибка:', error);
					});
		},
		randomBgClass() {
			return this.colors[Math.floor(Math.random() * this.colors.length)];
		},
	},
	mounted() {
		const id = this.block?.data.resource || this.resourceId
		this.getResource(id);
	},

	props: {
		block: {
			type: Object,
		},
		resourceId: {
			type: String,
			default: null,
		}
	},
}
</script>

<style>

.fslightbox-container {
	margin: 0 !important;
}

</style>