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

				<div>
					<DesktopNavBar v-if="sections" :sections="sections" />
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
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import MobileNavbar from "@/Navbars/MobileNavbar.vue";
import SearchModal from "@/Components/Modals/SearchModal.vue";
import {defineAsyncComponent} from "vue";
import DesktopNavBar from "@/Navbars/DesktopNavBar.vue";


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
		DesktopNavBar,
		SearchModal,
		MobileNavbar,
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
