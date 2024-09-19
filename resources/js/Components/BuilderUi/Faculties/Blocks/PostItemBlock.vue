<template>
	<div  class="w-full px-4 py-5 sm:px-6 lg:px-8 lg:py-7 mx-auto">
		<!-- Проверка на загрузку данных -->


		<div v-if="loading" class="flex flex-col space-y-4">
			<div class="flex animate-pulse">
					<div class="shrink-0 relative rounded-xl overflow-hidden w-full sm:w-56 h-44">
						<div class="bg-gray-200 group-focus:scale-105 transition-transform duration-500 ease-in-out size-full absolute top-0 start-0 object-cover rounded-xl" />
					</div>


					<div class="ms-4 mt-2 w-full">
						<p class="h-4 bg-gray-200 rounded-full" style="width: 40%;"></p>
						<ul class="mt-5 space-y-3 flex flex-col">
							<li class="w-full h-4 bg-gray-200 rounded-full"></li>
							<li class="w-full h-4 bg-gray-200 rounded-full"></li>
							<li class="w-full h-4 bg-gray-200 rounded-full"></li>
							<li class="w-full h-4 bg-gray-200 rounded-full"></li>
							<li class="w-20 h-4 bg-gray-200 rounded-full"></li>
						</ul>
					</div>

				</div>


		</div>


		<div v-else class="grid lg:grid-cols-1 lg:gap-y-16 gap-10">
				<Link class="group block rounded-xl overflow-hidden focus:outline-none" :href="route('client.post.show', post.data.slug)">
					<div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
						<div class="shrink-0 relative rounded-xl overflow-hidden w-full sm:w-56 h-44">
							<img class="group-hover:scale-105 group-focus:scale-105 transition-transform duration-500 ease-in-out size-full absolute top-0 start-0 object-cover rounded-xl"
									 :src="post.data.preview ? 'storage/images/' + post.data.preview : '/img/thumbnail-1.png'" />
						</div>

						<div class="grow">
							<h3 class="text-xl font-semibold text-gray-800 group-hover:text-gray-600">
								{{ post.data.title }}
							</h3>
							<p class="mt-3 text-gray-600">
								Produce professional, reliable streams easily leveraging Preline's innovative broadcast studio
							</p>
							<p class="mt-4 inline-flex items-center gap-x-1 text-sm text-primaryBlue decoration-2 group-hover:underline group-focus:underline font-medium">
								Читать далее
								<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="m9 18 6-6-6-6"/>
								</svg>
							</p>
						</div>
					</div>
				</Link>
		</div>
	</div>
</template>

<script>
import slugify from "slugify";
import axios from "axios";
import {Link} from "@inertiajs/vue3";

export default {
	name: "PostListBlock",
	components: { axios, Link },
	data() {
		return {
			post: null,
			loading: true, // Состояние загрузки
		}
	},
	methods: {
		getPost(id) {
			axios.get(route('client.widget.post.single', id), {
				params: {
					count: this.block.data.count,
					category: this.block.data.category // Исправлено с count.category на category
				}
			})
					.then(response => {
						this.post = response.data;
						this.loading = false; // Установить состояние загрузки в false
					})
					.catch(error => {
						console.error('Ошибка:', error);
						this.loading = false; // Установить состояние загрузки в false даже при ошибке
					});
		}
	},
	mounted() {
		this.getPost(this.block.data.post);
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
