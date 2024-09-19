<template>

	<header
			class="flex duration-500 fixed top-0 left-0 right-0 flex-wrap lg:justify-start lg:flex-nowrap z-50 w-full text-sm py-3 lg:py-0 header-filter">
		<nav class="max-w-screen-xl w-full mx-auto px-4 py-3" aria-label="Global">
			<div class="relative lg:flex lg:items-center lg:justify-between">
				<div class="flex items-center justify-between">
					<Link
							class="flex-none text-xl font-semibold"
							href="/" aria-label="Brand">
						<img class="max-w-[300px]" src="/logos/ntspi-logo.svg" alt="">
					</Link>

					<div class="lg:hidden">
						<button
										type="button"
										class="flex justify-center items-center w-9 h-9 text-sm font-semibold rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-gray-800 disabled:opacity-50 disabled:pointer-events-none"
										aria-haspopup="dialog"
										aria-expanded="false"
										aria-controls="open-mobile-nav"
										data-hs-overlay="#open-mobile-nav">
							<svg class="hs-collapse-open:hidden flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24"
									 height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									 stroke-linecap="round" stroke-linejoin="round">
								<line x1="3" x2="21" y1="6" y2="6"/>
								<line x1="3" x2="21" y1="12" y2="12"/>
								<line x1="3" x2="21" y1="18" y2="18"/>
							</svg>
							<svg class="hs-collapse-open:block hidden flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
									 width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									 stroke-linecap="round" stroke-linejoin="round">
								<path d="M18 6 6 18"/>
								<path d="m6 6 12 12"/>
							</svg>
						</button>
					</div>
				</div>

				<div id="navbar-collapse-with-animation"
						 class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow lg:block">
					<div
							class="overflow-hidden overflow-y-auto max-h-[75vh] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
						<div
								class="flex flex-col gap-x-0 mt-5 md:flex-row md:items-center md:justify-end md:gap-x-7 md:mt-0 md:ps-7 md:divide-y-0 md:divide-solid">
							<template v-for="section in this.sections.data" :key="section.id">
								<div
										class="hs-dropdown [--strategy:static] md:[--strategy:absolute] [--adaptive:none] md:[--trigger:hover] py-3 md:py-6">
									<button type="button"
													class="active:text-blue-600 flex items-center w-full text-black hover:text-gray-300 font-medium">
										{{ section.title }}

									</button>
									<div
											class="hs-dropdown-menu transition-opacity duration-150 md:duration-500 hs-dropdown-open:opacity-100 opacity-0 w-full hidden z-10 top-full start-0 min-w-[15rem] bg-white md:shadow-2xl rounded-lg py-2 md:p-4 before:absolute before:-top-5 before:start-0 before:w-full before:h-5">
										<div class="grid px-5 grid-cols-1 md:grid-cols-10">

											<template v-for="subsection in section.subSections" :key="subsection.id">
												<div class="md:col-span-3">
													<div class="flex flex-col py-6 px-3 md:px-6">
														<div class="space-y-4">
															<div class="flex items-center mb-2 gap-x-2">
																<span class="text-xs font-bold uppercase text-gray-800">{{
																		subsection.title
																	}}</span>
															</div>

															<template v-for="page in subsection.pages" :key="page.id">
																<a :class="{'text-secondDarkBlue hover:text-gray-800 font-semibold ' : isSameRoute(page.path), 'text-gray-800 hover:text-gray-500' : !isSameRoute(page.path) }"
																	 class="flex items-center gap-x-2"
																	 :href="(page.is_url) ? page.path : route('page.view', page.path) + '/'">
																	<div class="grow">
																		<p>{{ page.title }}</p>
																	</div>
																</a>
															</template>
														</div>
													</div>
												</div>
											</template>
										</div>
									</div>

								</div>
							</template>

							<a href="#" class="hover:opacity-70 py-3 className">
								<BaseIcon name="eye" class="w-6 h-6 duration-300 md:block hidden" />
								<span class="md:hidden">Режим для слабовидящих</span>
							</a>


							<Link :href="route('client.schedule')" class="hover:opacity-70 py-3">
								<BaseIcon name="schedule" class="w-6 h-6 text-black md:block hidden" />
								<span class="md:hidden text-black">Расписание</span>
							</Link>

							<a class="hover:opacity-70 py-3 cursor-pointer" data-hs-overlay="#hs-full-screen-modal-below-md">
								<BaseIcon name="search" class="w-6 h-6 text-black md:block hidden" />
								<span class="md:hidden text-black">Поиск</span>
							</a>


						</div>
					</div>
				</div>
			</div>
		</nav>
	</header>

	<MobileNavbar :sections="sections" />

	<SearchModal open_id="hs-full-screen-modal-below-md" />







</template>

<script>

import {Link} from "@inertiajs/vue3";
import axios from "axios";
import ClientGlobalSearch from "@/Components/ClientGlobalSearch.vue";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import MobileNavbar from "@/Navbars/MobileNavbar.vue";
import SearchModal from "@/Components/Modals/SearchModal.vue";


export default {
	name: 'MainNavBar',
	props: {
		sections: {
			type: Object,
		}

	},
	data() {
		return {}
	},
	components: {
		SearchModal,
		MobileNavbar,
		BaseIcon,
		ClientGlobalSearch,
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
