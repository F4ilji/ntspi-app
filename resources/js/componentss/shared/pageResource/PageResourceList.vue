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

	<div v-else-if="!loading & resource">

		<!-- Card Blog -->
		<div class="px-0 py-10 sm:px-2 lg:py-14 mx-auto">
			<!-- Title -->
			<div class="max-w-2xl text-center mx-auto mb-10 lg:mb-14">
				<h2 class="text-2xl font-bold md:text-4xl md:leading-tight">Полезные ресурсы</h2>
<!--				<p class="mt-1 text-gray-600">We've helped some great companies brand, design and get to market.</p>-->
			</div>
			<!-- End Title -->

			<!-- Grid -->
			<div class="flex overflow-x-auto space-x-6 mb-10 lg:mb-14 p-4">
				<!-- Card -->

				<a v-for="item in resource.data.content" class="group flex-shrink-0 w-64 flex flex-col bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition" :href="item.link">
					<div class="aspect-w-16 aspect-h-9">
						<img v-if="item.image" class="w-full backdrop-blur-xl object-cover rounded-t-xl h-[150px]" :src="'/storage/' + item.image" alt="Blog Image">
						<div v-else :class="randomBgClass()" class="w-full object-cover rounded-t-xl h-[150px] bg-gradient-to-tr" />
					</div>
					<div class="p-4 md:p-5">
						<p class="mt-2 text-xs uppercase text-gray-600">{{ item.model_select }}</p>
						<h3 class="mt-2 text-lg font-medium text-gray-800 group-hover:text-primary-hover">{{ item.title }}</h3>
						<p class="mt-2 text-xs text-gray-600">{{ item.link_text }}</p>
					</div>
				</a>



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
export default {
	name: "PageResourceList",
	components: {axios, Link },
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