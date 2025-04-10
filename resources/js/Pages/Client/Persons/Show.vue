<template>
	<MetaTags :seo="seo" />

  <MainPageNavBar class="border-b" :sections="$page.props.navigation" />


  <BasicPageWrapper>
    <main class="flex-grow">
      <div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">

        <NavigateLinks :header-navs="headerNavs" />
        <section class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
          <div class="w-full mx-auto sm:px-6 lg:px-8">

            <PersonBreadcrumbs class="mb-4" :person-name="person.data.name" />

            <InfoSection :person="person" />

          </div>
        </section>
      </div>
    </main>

    <BasicFooter />
  </BasicPageWrapper>





</template>

<script>



import {Link} from "@inertiajs/vue3";
import slugify from "slugify";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import NavigateLinks from "@/componentss/shared/navigate/NavigateLinks.vue";
import PersonBreadcrumbs from "@/componentss/features/persons/components/PersonBreadcrumbs.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import InfoSection from "@/componentss/features/persons/components/sections/InfoSection.vue";
import CategoryFilter from "@/componentss/shared/filter/filters/CategoryFilter.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import BasicListBadge from "@/componentss/shared/badge/BasicListBadge.vue";
import EventArchiveListBreadcrumbs from "@/componentss/features/events/components/EventArchiveListBreadcrumbs.vue";
import BasicPagination from "@/componentss/shared/paginate/BasicPagination.vue";
import IsOnlineFilter from "@/componentss/shared/filter/filters/IsOnlineFilter.vue";
import BasicListFilter from "@/componentss/shared/filter/BasicListFilter.vue";
import EventListSearch from "@/componentss/features/events/components/EventListSearch.vue";
import SortingByFilter from "@/componentss/shared/filter/filters/SortingByFilter.vue";


export default {
	name: "Show",
	data() {
		return {
			scrollTop: false,
			currentNavSection: null,
			headerNavs: []
		}
	},
	
	props: {
		person: {
			type: Object,
		},
		seo: {
			type: Object,
		}
	},

	components: {
    SortingByFilter,
    EventListSearch,
    BasicListFilter,
    IsOnlineFilter,
    BasicPagination,
    EventArchiveListBreadcrumbs,
    BasicListBadge,
    BasicPageWrapper,
    BasicPageContainer,
    CategoryFilter,
    InfoSection,
    BasicFooter,
    NavigateLinks,
    MetaTags,
		PersonBreadcrumbs,
		MainPageNavBar,
		Link,
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
		isSameRoute(route) {
			if (this.$page.props.ziggy.location === route) {
				return true
			}
		},
		generateSlug: function (text) {
			return slugify(text, {
				lower: true,
				strict: true,
				locale: 'ru'
			});
		},
		onScroll(e) {
			const windowTop = window.top.scrollY
			if (windowTop > 100) {
				this.scrollTop = true
			} else {
				this.scrollTop = false
			}
		},
		handleScroll(e) {
			const headings = document.querySelectorAll('h2');
			const visor = document.querySelector('#visor');
			let lastVisibleHeading = null;

			const visorRect = visor.getBoundingClientRect();

			// Проверяем, находится ли визор в пределах видимости
			if (visorRect.top > window.scrollY) {
				this.currentNavSection = null;
				lastVisibleHeading = null; // Сбрасываем заголовок, если визор не виден
				return; // Выходим из функции, если визор не виден
			}

			for (let i = 0; i < headings.length; i++) {
				const heading = headings[i];
				const rect = heading.getBoundingClientRect();

				// Проверяем, находится ли заголовок в видимой области и касается ли он элемента visor
				if (rect.top >= 0 && rect.bottom <= window.innerHeight && rect.bottom >= visorRect.top && rect.top <= visorRect.bottom) {
					// Проверяем, изменился ли заголовок
					if (heading !== lastVisibleHeading) {
						this.currentNavSection = heading.id;
						lastVisibleHeading = heading;
					}
					break; // Выходим из цикла, если нашли видимый заголовок
				}
			}
		},
		scrollToTop() {
			window.scrollTo(0, 0)
		},
		extractH2Headers() {
			const h2Elements = document.querySelectorAll('h2'); // выбираем все h2 на странице
			this.headerNavs = Array.from(h2Elements).map(h2 => ({
				id: h2.id,           // id заголовка
				text: h2.textContent // содержимое заголовка
			}));
		}



	},
	mounted() {
		this.extractH2Headers();

		window.addEventListener("scroll", this.onScroll)


		window.addEventListener("scroll", this.handleScroll);


	},
	beforeDestroy() {
		window.removeEventListener("scroll", this.handleScroll);
		window.removeEventListener("scroll", this.onScroll)
	},

	computed: {
	}
}
</script>

<style>


.paragraph-container a {
	@apply text-primary;
	@apply underline;
}

.paragraph-container p {
	@apply mb-2
}



@keyframes fade {
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}

.fade-enter-active,
.fade-leave-active {
	transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
	opacity: 0;
}

@keyframes grow-progress {
	from { transform: scaleX(0); }
	to { transform: scaleX(1); }
}

#progress {
	height: 2px;
	background: #26ACB8;
	z-index: 10000;

	transform-origin: 0 50%;
	animation: grow-progress auto linear;
	animation-timeline: scroll();
}


.active {
	color: blue !important;
}

.example-initial-animation {
	animation: initial-animation 2s ease;
}

@keyframes initial-animation {
	0% {
		transform: rotate(0deg);
	}

	50% {
		transform: rotate(360deg);
	}

	100% {
		transform: rotate(0deg);
	}
}

</style>
