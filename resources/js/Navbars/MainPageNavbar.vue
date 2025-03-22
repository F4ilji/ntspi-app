<template>

	<header :class="[{ 'header-filter': headerFilter }, { 'under-slider-bg': underSliderHeader }]"
          class="flex duration-500 fixed top-0 left-0 right-0 flex-wrap md:justify-start md:flex-nowrap z-50 w-full text-sm py-3 md:py-0">
		<nav class="max-w-screen-xl w-full mx-auto px-4 py-3" aria-label="Global">
			<div class="relative lg:flex md:items-center md:justify-between">
				<div class="flex items-center justify-between">
					<Link class="flex-none text-xl font-semibold"
						 :href="route('index')" aria-label="NTSPI">
            <transition name="logo" mode="out-in">
              <img :key="currentLogo" class="max-w-[300px] logo-transition" :src="currentLogo" alt="Логотип">
            </transition>
          </Link>
					<div class="lg:hidden">
            <button :class="{ 'text-black': underSliderHeader, 'text-white': !underSliderHeader }"
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
					<DesktopNavBar v-if="sections" :sections="sections" :under-slider-header="underSliderHeader" />
				</div>
			</div>
		</nav>
	</header>


	<MobileNavbar v-if="sections" :sections="sections" />

	<SearchModal open_id="open-search-modal" />

</template>

<script>

import {Link} from "@inertiajs/vue3";
import * as isvek from "bvi"
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import MobileNavbar from "@/Navbars/MobileNavbar.vue";
import DesktopNavBar from "@/Navbars/DesktopNavBar.vue";
import {mapGetters} from "vuex";
import SearchModal from "@/componentss/shared/modals/SearchModal.vue";
import {helpers} from "@/mixins/Helpers.js";


export default {
  mixins: [helpers],
  name: 'MainPageNavBar',
	components: {
		DesktopNavBar,
		SearchModal,
		MobileNavbar,
		BasicIcon,
		Link,
	},
	props: {
		sections: {
			type: Object,
		},
		sliderRef: {
			default: true
		},
	},
	data() {
		return {
			scrollPosition: 0,
			headerFilter: false,
			underSliderHeader: true,
			bvi: null,
			isActiveBvi: null,
			logos: {
				default: '/logos/white_ntspi_logo.svg',
				alternate: '/logos/ntspi-logo.svg',
			},
		}
	},
	methods: {
		iniBvi() {
			if (this.getCookie('bvi_panelActive') === null) {
				this.bvi = new isvek.Bvi({
					target: '.open-bvi',
					fontSize: 24,
					theme: 'black',
					speech: false,
					reload: true,
					panelHide: true
				});
			}
		},
    handleScroll() {
      // Сохраняем текущую позицию прокрутки
      this.scrollPosition = window.pageYOffset;

      // Проверяем, существует ли слайдер
      if (this.lastSlider) {
        const slider = this.lastSlider;

        // Проверяем, совпадает ли текущий маршрут с маршрутом слайдера
        if (this.IS_SAME_ROUTE(slider.url)) {
          // Определяем, находится ли слайдер в области видимости
          const isSliderVisible = slider.top <= this.scrollPosition && slider.bottom >= this.scrollPosition;

          // Если слайдер виден, underSliderHeader будет false, иначе true
          this.underSliderHeader = !isSliderVisible;
        }
      }

      // Активируем фильтр заголовка, если прокрутка превышает 90 пикселей
      this.headerFilter = this.scrollPosition > 50;
    }
    },
	watch: {
		lastSlider(newVal) {
			if (this.IS_SAME_ROUTE(newVal?.url)) {
				this.underSliderHeader = newVal.bottom < 0; // Пример логики
			}
		},
	},
	mounted() {
		window.addEventListener('scroll', this.handleScroll);

		if (this.getCookie('bvi_panelActive') === null) {
			this.iniBvi();
		}
	},
	beforeDestroy() {
		window.removeEventListener('scroll', this.handleScroll);
	},
	computed: {
		...mapGetters(['lastSlider']),
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

.under-slider-bg {
  background: hsla(0,0%,100%,.6)
}

.logo-transition {
  transition: opacity 0.3s ease-out;
}

.logo-enter, .logo-leave-to {
  opacity: 0;
}

.logo-enter-to, .logo-leave {
  opacity: 1;
}


</style>
