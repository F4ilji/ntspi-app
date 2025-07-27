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

  <div v-else class="w-full px-2 sm:px-3 lg:px-4 lg:py-4 mx-auto">
    <a class="group flex flex-col bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition"
       :href="(page.is_url) ? page.path : route('page.view', page.path) + '/'">
      <div class="p-4 md:p-5">
        <div class="flex items-center gap-x-5">
          <div class="grow min-w-0">
            <ol class="flex items-center whitespace-nowrap overflow-hidden hidden md:block">
              <li class="inline-flex items-center">
        <span class="flex items-center text-sm text-gray-500 hover:text-primary-hover focus:outline-none focus:text-primary truncate" href="#">
         {{ breadcrumbs.mainSection }}
        </span>
                <svg class="shrink-0 mx-2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m9 18 6-6-6-6"></path>
                </svg>
              </li>
              <li class="inline-flex items-center">
        <span class="flex items-center text-sm text-gray-500 hover:text-primary-hover focus:outline-none focus:text-primary truncate" href="#">
         {{ breadcrumbs.subSection }}
        </span>
                <svg class="shrink-0 mx-2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m9 18 6-6-6-6"></path>
                </svg>
              </li>
              <li class="inline-flex items-center">
        <span class="flex items-center text-sm text-gray-500 hover:text-primary-hover focus:outline-none focus:text-primary truncate" href="#">
         {{ breadcrumbs.page }}
        </span>
              </li>
            </ol>
            <p class="mt-1 group-hover:text-primary-hover font-semibold text-gray-700 break-words">
              {{ page.title }}
            </p>
          </div>
        </div>
      </div>
    </a>
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
