<template>

	<header :style=" underSliderHeader ? 'background: hsla(0,0%,100%,.6)' : '' "
					:class="{ 'header-filter': headerFilter }"
					class="flex duration-500 fixed top-0 left-0 right-0 flex-wrap md:justify-start md:flex-nowrap z-50 w-full text-sm py-3 md:py-0">
		<nav class="max-w-screen-xl w-full mx-auto px-4 py-3" aria-label="Global">
			<div class="relative lg:flex md:items-center md:justify-between">
				<div class="flex items-center justify-between">
					<a class="flex-none text-xl font-semibold dark:text-white dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
						 href="/" aria-label="Brand">
						<img class="max-w-[300px] duration-300" :src="currentLogo" alt="Логотип">
					</a>
					<div class="lg:hidden">
						<button :class="underSliderHeader ? 'text-black' : 'text-white'"
								type="button"
								id="open-mobile-btn"
								class="flex justify-center items-center w-9 h-9 text-sm font-semibold rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-gray-800 disabled:opacity-50 disabled:pointer-events-none"
								aria-haspopup="dialog"
								aria-expanded="false"
								aria-controls="open-mobile-nav"
								data-hs-overlay="#open-mobile-nav"
						>
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
					<DesktopNavBar :sections="sections" :under-slider-header="underSliderHeader" />

				</div>
			</div>
		</nav>
	</header>

	<MobileNavbar :sections="sections" />


	<SearchModal open_id="open-search-modal" />

</template>

<script>

import {Link} from "@inertiajs/vue3";
import axios from "axios";
import ClientGlobalSearch from "@/Components/ClientGlobalSearch.vue";
import * as isvek from "bvi"
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import MobileNavbar from "@/Navbars/MobileNavbar.vue";
import SearchModal from "@/Components/Modals/SearchModal.vue";
import DesktopNavBar from "@/Navbars/DesktopNavBar.vue";


export default {
	name: 'MainPageNavBar',
	components: {
		DesktopNavBar,
		SearchModal,
		MobileNavbar,
		BaseIcon,
		ClientGlobalSearch,
		Link,
	},
	props: {
		sections: {
			type: Object,
		},
		sliderRef: {
			type: HTMLDivElement,
			default: true
		},
	},
	data() {
		return {
			scrollPosition: 0,
			headerFilter: false,
			underSliderHeader: this.sliderRef,
			bvi: null,
			logos: {
				default: '/logos/white_ntspi_logo.svg',
				alternate: '/logos/ntspi-logo.svg',
			},
		}
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

		handleScroll() {
			if (typeof this.sliderRef === 'object') {
				const mainSlider = this.sliderRef;
				this.underSliderHeader = mainSlider.getBoundingClientRect().bottom < 50
				this.scrollPosition = window.pageYOffset
				this.headerFilter = this.scrollPosition > 90
			} else {
				this.headerFilter = true
			}
		},
		getCookie(name) {
			let cookies = document.cookie.split(';');
			for (let i = 0; i < cookies.length; i++) {
				let cookie = cookies[i].trim();
				if (cookie.startsWith(name + '=')) {
					return cookie.substring(name.length + 1);
				}
			}
			return null; // Если cookie не найден
		}


	},
	mounted() {
		window.addEventListener('scroll', this.handleScroll)
		window.addEventListener('scroll', this.checkSlider)
		if (this.getCookie('bvi_panelActive') === null) {
			this.bvi = new isvek.Bvi({
				target: '.className',
				fontSize: 24,
				theme: 'black',
				speech: false,
				reload: true,
				//...etc
			});
		}
	},
	beforeDestroy() {
		window.removeEventListener('scroll', this.handleScroll)
		window.removeEventListener('scroll', this.checkSlider)
	},
	computed: {
		currentLogo() {
			return this.underSliderHeader ? this.logos.alternate : this.logos.default;
		},
	},


}

</script>

<style scoped>


.header-filter {
	transition: all 0.3s;
	backdrop-filter: saturate(180%) blur(7px);
}


</style>
