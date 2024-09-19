<template>
	<div class="flex justify-between pb-4 items-center">
		<div class="flex w-full sm:items-center gap-x-5 sm:gap-x-3">
			<div class="grow">
				<div class="grid sm:flex sm:justify-between sm:items-center gap-2">
					<ol v-if="breadcrumbs" class="flex items-center whitespace-normal min-w-0 flex-wrap gap-y-2"
							aria-label="Breadcrumb">
						<li class="text-sm">
										<span class="flex items-center text-gray-500 hover:text-primaryBlue">
											Главная
											<svg class="flex-shrink-0 mx-3 overflow-visible h-2.5 w-2.5 text-gray-400"
													 width="16" height="16"
													 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
															stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
											</svg>
										</span>
						</li>
						<li class="text-sm">
										<span class="flex items-center text-gray-500 hover:text-primaryBlue" @click.prevent>
											{{ textLimit(this.breadcrumbs.mainSection, 25) }}
											<svg class="flex-shrink-0 mx-3 overflow-visible h-2.5 w-2.5 text-gray-400"
													 width="16" height="16"
													 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
															stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
											</svg>
										</span>
						</li>
						<li class="text-sm">
										<span class="flex items-center text-gray-500 hover:text-primaryBlue" @click.prevent>
											{{ textLimit(this.breadcrumbs.subSection, 25) }}
											<svg class="flex-shrink-0 mx-3 overflow-visible h-2.5 w-2.5 text-gray-400"
													 width="16" height="16"
													 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
															stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
											</svg>
										</span>
						</li>
						<li class="text-sm">
										<span class="flex items-center text-gray-500 hover:text-primaryBlue" @click.prevent>
											{{ textLimit(this.breadcrumbs.page, 25) }}
										</span>
						</li>
					</ol>
					<ol v-if="!breadcrumbs" class="flex items-center whitespace-nowrap min-w-0 flex-wrap"
							aria-label="Breadcrumb">
						<li class="text-sm">
										<span class="flex items-center text-gray-500 hover:text-blue-600" href="/">
											Главная
											<svg class="flex-shrink-0 mx-3 overflow-visible h-2.5 w-2.5 text-gray-400"
													 width="16" height="16"
													 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
															stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
											</svg>
										</span>
						</li>
						<li class="text-sm">
										<span class="flex items-center text-gray-500 hover:text-blue-600" @click.prevent>
											{{ textLimit(pageTitle, 30) }}

										</span>
						</li>
					</ol>
				</div>
			</div>
		</div>
	</div>


</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "PageBreadcrumbs",
	components: {Link},
	data() {
		return {
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
	},

	props: {
		breadcrumbs: {
			type: Object,
		},
		pageTitle: {
			type: String,
		},
	},
}

</script>


<style scoped>

</style>