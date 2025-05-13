<template>
	<MetaTags :seo="seo" />

  <MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <div class="relative mx-auto mb-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">

      <NavigateVisor />

      <div class="sticky top-[110px] hidden h-[calc(100vh-110px)] max-w-[20%] md:flex md:shrink-0 md:flex-col md:justify-between">
        <nav v-if="faculties"
             class="flex h-[calc(100vh-200px)] flex-col overflow-hidden pr-2 pb-4">
          <div class="text-gray-1000 mb-2 text-md font-medium">Факультеты</div>
          <div class="flex gap-x-1">
            <ul class="px-0.5 last-of-type:mb-0 mb-8">
              <li v-for="faculty in faculties.data" :key="faculty.id" class="my-1.5 flex">
                <a :class="{'text-white hover:text-gray-200 font-semibold bg-[#135aae]': isSameRoute(route('client.faculty.show', faculty.slug)), 'text-gray-600 hover:text-[#2C6288]': !isSameRoute(route('client.faculty.show', faculty.id)) }"
                   :href="route('client.faculty.show', faculty.slug) + '/'"
                   class="relative duration-300 flex w-full rounded-md cursor-pointer items-center px-2 py-1 text-left text-sm">{{
                    faculty.title
                  }}</a>
              </li>
            </ul>
          </div>

        </nav>
      </div>

      <NavigateLinks :header-navs="headerNavs" />

      <article class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
        <div class="space-y-5 md:space-y-5">
          <div class="flex justify-between items-center mb-6">
            <div class="flex w-full sm:items-center gap-x-5 sm:gap-x-3 truncate">
              <div class="grow">
                <div class="grid sm:flex sm:justify-between sm:items-center gap-2">
                  <FacultyItemBreadcrumbs :faculty-title="faculty.data.shortTitle" />
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-3">
            <h1 class="text-2xl mb-10 font-bold md:text-3xl">{{ faculty.data.title }}</h1>
          </div>

          <div id="page-area" class="space-y-5 md:space-y-5">

            <h2 :id="'anchor-link-' + generateSlug('Состав')" v-if="faculty.data.workers.length > 0" class="font-bold text-xl">Состав</h2>
            <template v-for="worker in faculty.data.workers">
              <FacultyWorkerCard :worker="worker" />
            </template>


            <h2 :id="'anchor-link-' + generateSlug('Кафедры факультета')" v-if="faculty.data.departments.length > 0" class="font-bold text-xl">Кафедры факультета</h2>
            <div class="grid sm:grid-cols-2 md:grid-cols-2 xl:grid-cols-2 gap-3 sm:gap-6">
              <template v-for="department in faculty.data.departments">
                <Link :href="route('client.department.show', { facultySlug: faculty.data.slug, departmentSlug: department.slug })" class="flex justify-center items-center bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition">
                  <div class="p-4 md:p-5">
                    <div class="flex justify-between items-center gap-x-3">
                      <div class="grow">
                        <h3 class="group-hover:text-primary-hover font-semibold text-gray-800">
                          {{ department.title }}
                        </h3>
                      </div>
                      <div>
                        <svg class="shrink-0 size-5 text-gray-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                      </div>
                    </div>
                  </div>
                </Link>
              </template>
            </div>

            <h2 v-if="faculty.data.content.length > 0" :id="'anchor-link-' + generateSlug('Описание')" class="font-bold text-xl">Описание</h2>

            <div class="space-y-3 md:space-y-4">
              <Builder :blocks="faculty.data.content "/>
            </div>

          </div>


        </div>
      </article>
    </div>
    <BasicFooter />
  </BasicPageWrapper>




</template>

<script>


import {Link} from "@inertiajs/vue3";
import MainNavbar from "@/Navbars/MainNavbar.vue";
import { Head } from '@inertiajs/vue3'
import slugify from "slugify";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import FacultyWorkerCard from "@/componentss/features/faculties/components/FacultyWorkerCard.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import FacultyItemBreadcrumbs from "@/componentss/features/faculties/components/FacultyItemBreadcrumbs.vue";
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
import NavigateVisor from "@/componentss/shared/visor/NavigateVisor.vue";
import NavigateLinks from "@/componentss/shared/navigate/NavigateLinks.vue";


export default {
	name: "Page",
	data() {
		return {
			scrollTop: false,
			currentNavSection: null,
      headerNavs: []
    }
	},
	
	props: {
		faculty: {
			type: Object,
		},
		faculties: {
			type: Object
		},
		seo: {
			type: Object,
		}
	},

	components: {
    NavigateLinks,
    NavigateVisor,
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
    MetaTags,
    Builder,
    BasicFooter,
    FacultyWorkerCard,
		FacultyItemBreadcrumbs,
		MainPageNavBar,
		MainNavbar,
		Link,
		Head
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
			this.scrollTop = windowTop > 100;
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

		window.addEventListener("scroll", () => {
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
		});


	},
	beforeDestroy() {
		window.removeEventListener('scroll', this.handleScroll);
		window.removeEventListener("scroll", this.onScroll)
	},

	computed: {
	}
}
</script>

<style>

</style>
