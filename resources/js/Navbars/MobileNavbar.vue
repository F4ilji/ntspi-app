<template>
	<div id="open-mobile-nav" class="hs-overlay hs-overlay-open hs-overlay-open:translate-x-0 hidden [--overlay-backdrop:false] -translate-x-full fixed top-0 start-0 transition-all duration-300 transform h-full w-full z-[80] bg-white border-e" role="dialog" tabindex="-1" aria-labelledby="open-mobile-nav-label">
		<div class="flex justify-between items-center py-3 px-4 border-b">
			<img class="max-w-[75px] p-2"
					 src="/logos/only_logo.svg" alt="">
			<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#open-mobile-nav">
				<span class="sr-only">Close</span>
				<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M18 6 6 18"></path>
					<path d="m6 6 12 12"></path>
				</svg>
			</button>
		</div>
		<div class="overflow-y-auto h-full">
			<nav class="hs-accordion-group p-4 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
				<ul class="space-y-1.5">
					<li>
						<Link
								:href="route('index')"
								:class="(isSameRoute(route('index')) ? 'text-blue-600 bg-gray-100' : 'text-gray-700')"
								class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg hover:bg-gray-100" href="#">
							<svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
							Главная
						</Link>
					</li>


					<li v-for="section in sections.data" class="hs-accordion" :id="'nav-section-accordion-' + section.slug">
						<button
								:id="'nav-section-accordion-btn-' + section.slug"
								:class="(hasActivePage(section) ? 'text-blue-600 bg-gray-100' : 'text-gray-700')"
								type="button"
								class="hs-accordion-toggle hs-accordion-active:text-blue-600 w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg focus:outline-none" aria-expanded="true" aria-controls="users-accordion">
							<BaseIcon class="w-4" name="line" />
							{{ section.title }}
							<svg class="flex-shrink-0 hs-accordion-active:block ms-auto hidden size-4 text-gray-600 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
							<svg class="flex-shrink-0 hs-accordion-active:hidden ms-auto block size-4 text-gray-600 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
						</button>
						<div id="users-accordion" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden" role="region" aria-labelledby="users-accordion">
							<ul class="hs-accordion-group ps-3 pt-2" data-hs-accordion-always-open>
								<li v-for="subSection in section.subSections" class="hs-accordion" :id="'nav-sub-section-accordion-' + subSection.slug">
									<button
											:id="'nav-sub-section-accordion-btn-' + subSection.slug"
											:class="(hasActivePage(subSection) ? 'text-blue-600 bg-gray-100' : 'text-gray-700')"
											type="button"
											class="hs-accordion-toggle hs-accordion-active:text-blue-600 w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg focus:outline-none " aria-expanded="true" :aria-controls="'nav-accordion-' + subSection.slug">
										{{ subSection.title }}

										<svg class="flex-shrink-0 hs-accordion-active:block ms-auto hidden size-4 text-gray-600 group-hover:text-gray-500 dark:text-neutral-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>

										<svg class="flex-shrink-0 hs-accordion-active:hidden ms-auto block size-4 text-gray-600 group-hover:text-gray-500 dark:text-neutral-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
									</button>

									<div class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden" role="region" :aria-labelledby="'nav-accordion-' + subSection.slug">
										<ul class="pt-2 ps-2">
											<li v-for="page in subSection.pages">
												<a
														:href="(page.is_url) ? page.path : route('page.view', page.path) + '/'"
														class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
														:class="(isSameRoute(page.path) ? 'text-blue-600 bg-gray-100' : 'text-gray-700')"
												>
													{{ page.title }}
												</a>
											</li>
										</ul>
									</div>
								</li>
							</ul>
						</div>
					</li>


					<li class="">
						<Link :href="route('client.schedule')"
									:class="(isSameRoute(route('client.schedule')) ? 'text-blue-600 bg-gray-100' : 'text-gray-700')"
									class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300" href="#">
							<BaseIcon class="w-4" name="schedule" />
							Расписание
						</Link>
					</li>
					<li>
						<a class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300" href="#">
							<BaseIcon class="w-4" name="eye" />
							Режим для слабовидящих
						</a>
					</li>
					<li>
						<a aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-full-screen-modal-below-md" data-hs-overlay="#open-search-modal"
							 class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300" href="#">
							<BaseIcon class="w-4" name="search" />
							Поиск
						</a>
					</li>
				</ul>
			</nav>
		</div>
	</div>








</template>

<script>

import {Link} from "@inertiajs/vue3";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import jsonIcons from "@/Components/other/icons.json";
import {defineAsyncComponent} from "vue";


export default {
	name: 'MobileNavbar',
	props: {
		sections: {
			type: Object,
		}

	},
	data() {
		return {
			icons: jsonIcons,
		}
	},
	components: {
		BaseIcon,
		ClientGlobalSearch: defineAsyncComponent(() =>
				import('@/Components/ClientGlobalSearch.vue')
		),
		Link,
	},
	methods: {
		isSameRoute(route) {
			if (route === this.$page.props.ziggy.location) {
				return true;
			}

			const currentLocation = this.$page.props.ziggy.location;
			const currentUrl = this.$page.props.ziggy.url + '/' + route;

			if (currentLocation === currentUrl) {
				return true;
			}

			return false;
		},
		hasActivePage(section) {
			// Проверяем, есть ли активная страница в секции или подсекции
			// if (!section || !section.pages) return false; // Проверка на наличие section и pages

			if (section.pages) {
				return section.pages.some(page => this.isSameRoute(page.path));
			}

			if (section.subSections) {
				return section.subSections.some(subSection => this.hasActivePage(subSection));
			}



			// // Проверяем наличие активной страницы в подсекциях
			// const hasActiveInSubSections = section.subSections && section.subSections.some(subSection => this.hasActivePage(subSection));
			//
			// console.log(hasActiveInSubSections)
			//
			//
			// return hasActiveInPages || hasActiveInSubSections;
		},
	},


}

</script>

<style scoped>

.header-filter {
	transition: all 0.3s;
	backdrop-filter: saturate(180%) blur(7px);
	background: hsla(0, 0%, 100%, .6);
}




</style>
