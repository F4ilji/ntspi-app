<template>


	<div v-if="loading" class="flex flex-col space-y-4">
		<div class="flex animate-pulse">
			<div class="ms-4 mt-2 w-full border px-4 py-4 rounded-xl shadow-sm">
				<p class="h-4 bg-gray-200 rounded-full" style="width: 40%;"></p>
				<ul class="mt-5 space-y-3 flex flex-col">
					<li class="w-full h-4 bg-gray-200 rounded-full"></li>
					<li class="w-full h-4 bg-gray-200 rounded-full"></li>
				</ul>
			</div>

		</div>
	</div>

	<div v-else class="w-full px-2 py-5 sm:px-3 lg:px-4 lg:py-7 mx-auto">
		<!-- Grid -->
		<a class="group flex flex-col bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition"
					:href="(page.is_url) ? page.path : route('page.view', page.path) + '/'">
			<div class="p-4 md:p-5">
				<div class="flex items-center gap-x-5">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 shrink-0 size-7 text-gray-600">
						<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
					</svg>
					<div class="grow">
						<ol class="flex items-center whitespace-nowrap">
							<li class="inline-flex items-center">
								<span class="flex items-center text-sm text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600" href="#">
									{{ breadcrumbs.mainSection }}
								</span>
								<svg class="shrink-0 mx-2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="m9 18 6-6-6-6"></path>
								</svg>
							</li>
							<li class="inline-flex items-center">
								<span class="flex items-center text-sm text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600" href="#">
									{{ breadcrumbs.mainSection }}
								</span>
								<svg class="shrink-0 mx-2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="m9 18 6-6-6-6"></path>
								</svg>
							</li>
							<li class="inline-flex items-center">
								<span class="flex items-center text-sm text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600" href="#">
									{{ breadcrumbs.page }}
								</span>
							</li>
						</ol>
						<h3 class="mt-1 group-hover:text-blue-600 font-semibold text-gray-700">
							{{ page.title }}
						</h3>
					</div>
				</div>
			</div>
		</a>
		<!-- End Grid -->
	</div>


</template>

<script>
import slugify from "slugify";
import axios from "axios";
import {Link} from "@inertiajs/vue3";

export default {
	name: "PageItemBlock",
	components: { axios, Link },
	data() {
		return {
			page: null,
			breadcrumbs: null,
			loading: true, // Состояние загрузки
		}
	},
	methods: {
		getPage(id) {
			axios.get(route('client.widget.page.single', id))
					.then(response => {
						this.page = response.data.data.page;
						this.breadcrumbs = response.data.data.breadcrumbs;
						this.loading = false; // Установить состояние загрузки в false
					})
					.catch(error => {
						console.error('Ошибка:', error);
						this.loading = false; // Установить состояние загрузки в false даже при ошибке
					});
		}
	},
	mounted() {
		this.getPage(this.block.data.page)
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
