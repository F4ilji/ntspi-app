<template>

	<MetaTags :seo="seo" />

	<MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">

      <NavigateVisor />

      <div class="sticky top-[110px] hidden h-[calc(100vh-110px)] max-w-[20%] lg:flex lg:shrink-0 lg:flex-col lg:justify-between">
        <nav v-if="departments"
             class="flex h-[calc(100vh-200px)] flex-col overflow-hidden pr-2 pb-4">
          <div class="text-gray-1000 mb-2 text-md font-medium">Кафедры факультета - {{ department.data.faculty.abbreviation }}</div>
          <div class="flex gap-x-1">
            <ul class="px-0.5 last-of-type:mb-0 mb-8">
              <li v-for="depart in departments.data" :key="department.id" class="my-1.5 flex">
                <a :class="{'text-white hover:text-gray-200 font-semibold bg-[#135aae]': this.IS_SAME_ROUTE(route('client.department.show', { facultySlug: department.data.faculty.slug, departmentSlug: depart.slug })), 'text-gray-600 hover:text-[#2C6288]': !this.IS_SAME_ROUTE(route('client.department.show', { facultySlug: department.data.faculty.slug, departmentSlug: depart.slug })) }"
                   :href="route('client.department.show', { facultySlug: department.data.faculty.slug, departmentSlug: depart.slug }) + '/'"
                   class="relative duration-300 flex w-full rounded-md cursor-pointer items-center px-2 py-1 text-left text-sm">{{
                    depart.title
                  }}</a>
              </li>
            </ul>
          </div>

        </nav>
      </div>

      <NavigateLinks :header-navs="headerNavs" />

      <article class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
        <div class="space-y-5 md:space-y-5">

          <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
            <BreadcrumbsItem title="Факультеты" :url="route('client.faculty.index')" />
            <BreadcrumbsItem :title="department.data.faculty.abbreviation" :url="route('client.faculty.show', department.data.faculty.slug)" />
            <BreadcrumbsItem :title="department.data.title" :url="$page.url" />
          </BaseBreadcrumbs>

          <BasicTitle :header="department.data.title" />

          <div id="page-area" class="space-y-5 md:space-y-5">
            <h2 v-if="department.data.workers.length > 0" :id="'anchor-link-' + generateSlug('Профессорско-преподавательский состав')" class="font-bold text-xl">Профессорско-преподавательский состав</h2>
            <template v-for="worker in department.data.workers">
              <DepartmentWorkerCard :worker="worker" />
            </template>

            <h2 v-if="department.data.teachers.length > 0" :id="'anchor-link-' + generateSlug('Преподаватели кафедры')" class="font-bold text-xl">Преподаватели кафедры</h2>
            <template v-for="teacher in department.data.teachers">
              <DepartmentTeacherCard :teacher="teacher" />
            </template>

<!--            <h2 id="external-teachers" class="font-bold text-xl">Внешние совместители</h2>-->

            <h2 v-if="directions" :id="'anchor-link-' + generateSlug('Программы')" class="font-bold text-xl">Программы</h2>

            <div class="hs-accordion-group">
              <div v-for="(direction, index) in directions" class="hs-accordion hs-accordion-active:bg-gray-100 rounded-xl px-4 py-6" id="hs-basic-with-title-and-arrow-stretched-heading-three">
                <button class="hs-accordion-toggle group pb-3 inline-flex items-center justify-between gap-x-3 w-full md:text-lg font-semibold text-start text-gray-800 rounded-lg transition hover:text-gray-500 focus:outline-none" aria-expanded="false" aria-controls="hs-basic-with-title-and-arrow-stretched-collapse-three">
                  {{ index }}
                  <svg class="hs-accordion-active:hidden block shrink-0 size-5 text-gray-600 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                  <svg class="hs-accordion-active:block hidden shrink-0 size-5 text-gray-600 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
                </button>
                <div id="hs-basic-with-title-and-arrow-stretched-collapse-three" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="hs-basic-with-title-and-arrow-stretched-heading-three">
                  <ul class="list-disc list-outside space-y-3 ps-5 text-lg text-gray-800 dark:text-neutral-200">
                    <li v-for="program in direction" class="ps-2">
                      <Link :href="route('client.program.show', program.slug)" class="text-base text-primary decoration-2 hover:underline focus:outline-none focus:underline font-medium dark:text-blue-500">{{ program.name }}</Link>
                    </li>
                  </ul>
                </div>
              </div>


            </div>

            <h2 v-if="department.data.content.length > 0" :id="'anchor-link-' + generateSlug('Описание')" class="font-bold text-xl">Описание</h2>


            <div class="space-y-3 md:space-y-4">
              <Builder :blocks="department.data.content "/>
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
import slugify from "slugify";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import DepartmentItemBreadcrumbs from "@/componentss/features/departments/components/DepartmentItemBreadcrumbs.vue";
import BasicTitle from "@/componentss/ui/titles/BasicTitle.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import DepartmentWorkerCard from "@/componentss/features/departments/components/DepartmentWorkerCard.vue";
import DepartmentTeacherCard from "@/componentss/features/departments/components/DepartmentTeacherCard.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import {helpers} from "@/mixins/Helpers.js";
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
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";
import NavigateLinks from "@/componentss/shared/navigate/NavigateLinks.vue";


export default {
  mixins: [helpers],
  name: "Page",
	data() {
		return {
			scrollTop: false,
			currentNavSection: null,
      headerNavs: []
    }
	},
	
	props: {
		department: {
			type: Object,
		},
		departments: {
			type: Object
		},
		directions: {
			type: Object
		},
		seo: {
			type: Object
		},
    breadcrumbs: {
      type: Object
    }
	},

	components: {
    NavigateLinks,
    BaseBreadcrumbs, BreadcrumbsItem,
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
    BasicFooter,
    DepartmentTeacherCard,
    DepartmentWorkerCard,
    Builder,
    BasicTitle,
    MetaTags,
		DepartmentItemBreadcrumbs,
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
    this.extractH2Headers()
		window.addEventListener("scroll", this.onScroll)

		// this.editorImages = this.blocksWithSlideNumber.filter(block => block.type === 'image').map(block => block.data.file.url);

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
