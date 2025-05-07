<template>
  <header v-bind="$attrs" :class="[
      { 'header-filter': headerFilter },
      { 'under-slider-bg': underSliderHeader },
      isBviActive ? 'static' : 'fixed top-0 left-0 right-0'
    ]"
          class="flex duration-500 flex-wrap md:justify-start md:flex-nowrap z-50 w-full text-sm py-3 md:py-0">
    <nav class="max-w-screen-xl w-full mx-auto px-4 py-3" aria-label="Global">
      <div class="relative lg:flex md:items-center md:justify-between">
        <div class="flex items-center justify-between">
          <Link class="flex-none text-xl font-semibold"
                :href="route('index')" aria-label="NTSPI">
            <img :key="currentLogo" class="max-w-[300px] transition-all duration-300" :src="currentLogo" alt="Логотип">
          </Link>
          <div class="lg:hidden">
            <button :class="{ 'text-black border-gray-500': underSliderHeader, 'text-white border-gray-200': !underSliderHeader }"
                    type="button"
                    id="open-mobile-btn"
                    class="flex justify-center items-center w-9 h-9 text-sm font-semibold rounded-lg border hover:bg-gray-100 hover:text-gray-800 disabled:opacity-50 disabled:pointer-events-none"
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
          <DesktopNavBar v-if="sections" :sections="sections" :under-slider-header="underSliderHeader" />
        </div>
      </div>
    </nav>
  </header>

  <MobileNavbar v-if="sections" :sections="sections" />
  <SearchModal open_id="open-search-modal">
    <ClientGlobalSearch />
  </SearchModal>
  <SearchModal open_id="open-search-sveden-modal">
    <ClientStaticSearch />
  </SearchModal>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import MobileNavbar from "@/Navbars/MobileNavbar.vue";
import DesktopNavBar from "@/Navbars/DesktopNavBar.vue";
import SearchModal from "@/componentss/shared/modals/SearchModal.vue";
import { helpers } from "@/mixins/Helpers.js";
import ClientGlobalSearch from "@/componentss/features/search/components/GlobalSearch.vue";
import ClientStaticSearch from "@/componentss/features/search/components/StaticSearch.vue";

export default {
  inheritAttrs: false,
  mixins: [helpers],
  name: 'MainPageNavBar',
  components: {
    DesktopNavBar,
    SearchModal,
    MobileNavbar,
    BasicIcon,
    Link,
    ClientGlobalSearch,
    ClientStaticSearch,
  },
  props: {
    sections: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      scrollPosition: 0,
      headerFilter: false,
      underSliderHeader: true,
      logos: {
        default: '/logos/ntspi_white.svg',
        alternate: '/logos/ntspi.svg',
      },
    }
  },
  methods: {
    handleScroll() {
      this.scrollPosition = window.pageYOffset;

      // Используем Vuex только если $store доступен
      if (this.$store && this.lastSlider) {
        const slider = this.lastSlider;
        if (this.IS_SAME_ROUTE(slider.url)) {
          const isSliderVisible = slider.top <= this.scrollPosition && slider.bottom >= this.scrollPosition;
          this.underSliderHeader = !isSliderVisible;
        }
      }

      this.headerFilter = this.scrollPosition > 5;
    }
  },
  watch: {
    lastSlider(newVal) {
      // Если Vuex не используется, просто игнорируем
      if (!this.$store) return;
      if (this.IS_SAME_ROUTE(newVal?.url)) {
        this.underSliderHeader = newVal.bottom < 0;
      }
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);

    // Вызываем action, только если на клиенте и $store доступен
    if (typeof window !== 'undefined' && this.$store && this.$store.dispatch('initializeBvi')) {
      this.$store.dispatch('initializeBvi');
    }
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  computed: {

    lastSlider() {
      if (this.$store && this.$store.getters && this.$store.getters.lastSlider) {
        return this.$store.getters.lastSlider;
      }
      return null;
    },

    isBviActive() {
      // Для SSR возвращаем false по умолчанию
      if (typeof window === 'undefined') return false;
      return this.$store?.getters?.isBviActive ?? false;
    },

    currentLogo() {
      if (this.isBviActive) {
        return this.logos.default;
      }
      return this.underSliderHeader ? this.logos.alternate : this.logos.default;
    }
  },
  created() {
    // Инициализацию Vuex экшенов проводим, только если $store есть
    if (this.$store && this.$store.dispatch) {
      this.initializeBvi = () => this.$store.dispatch('initializeBvi');
    }
  }
}
</script>

<style scoped>
.header-filter {
  transition: all 0.3s;
  backdrop-filter: saturate(180%) blur(7px);
}

.under-slider-bg {
  background: hsla(0,0%,100%,.6);
}
</style>
