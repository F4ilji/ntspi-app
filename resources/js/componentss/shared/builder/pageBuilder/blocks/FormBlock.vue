<template>
	<div v-if="loading" class="flex flex-col space-y-4">
		<div class="flex-col animate-pulse mt-10">
			<div class="w-[25rem] mx-auto h-8 bg-gray-200 rounded-full"></div>
			<div class="mt-5 mx-auto w-[40rem] h-60 relative z-1000 border rounded-xl sm:mt-10 md:p-10 bg-gray-200">
			</div>
		</div>
	</div>
	<div v-else>

    <div v-if="block.data.settings.in_modal">
      <button type="button" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-vertically-centered-scrollable-modal" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
        {{ form.data.title }}
      </button>
      <div id="hs-vertically-centered-scrollable-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[1000000] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="hs-vertically-centered-scrollable-modal-label">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-3xl sm:w-full m-3 sm:mx-auto h-[calc(100%-56px)] min-h-[calc(100%-56px)] flex items-center">
          <div class="w-full max-h-full overflow-hidden flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
              <h3 id="hs-vertically-centered-scrollable-modal-label" class="font-bold text-gray-800 dark:text-white">
              </h3>
              <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
                <span class="sr-only">Close</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M18 6 6 18"></path>
                  <path d="m6 6 12 12"></path>
                </svg>
              </button>
            </div>
            <div class="p-4 overflow-y-auto">
              <FormBuilder :blocks="form" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <FormBuilder :blocks="form" />
    </div>

  </div>
</template>

<script>

import axios from "axios";
import {Link} from "@inertiajs/vue3";
import FormBuilder from "@/componentss/shared/builder/formBuilder/FormBuilder.vue";
import SubmitBlock from "@/componentss/shared/builder/formBuilder/blocks/SubmitBlock.vue";
export default {
	name: "FormBlock",
	components: {SubmitBlock, FormBuilder, axios, Link },
	data() {
		return {
			form: null,
			loading: true, // Состояние загрузки
		}
	},
	methods: {
		getForm(id) {
			axios.get(route('client.widget.form.single', id))
					.then(response => {
						this.form = response.data;
						this.loading = false; // Установить состояние загрузки в false
					})
					.catch(error => {
						console.error('Ошибка:', error);
					});
		}
	},
	mounted() {
		const id = this.block?.data.form || this.formId
		this.getForm(id);

	},
	props: {
		block: {
			type: Object,
		},
		formId: {
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