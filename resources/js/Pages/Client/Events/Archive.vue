<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import EventListSelectDate from "@/componentss/features/events/components/EventListSelectDate.vue";
import EventListFilter from "@/componentss/features/events/components/EventListFilter.vue";
import EventListBreadcrumbs from "@/componentss/features/events/components/EventListBreadcrumbs.vue";
import BasicListFilter from "@/componentss/shared/filter/BasicListFilter.vue";
import CategoryFilter from "@/componentss/shared/filter/filters/CategoryFilter.vue";
import IsOnlineFilter from "@/componentss/shared/filter/filters/IsOnlineFilter.vue";
import BasicListBadge from "@/componentss/shared/badge/BasicListBadge.vue";
import EventArchiveListBreadcrumbs from "@/componentss/features/events/components/EventArchiveListBreadcrumbs.vue";
import EventListSearch from "@/componentss/features/events/components/EventListSearch.vue";
import SortingByFilter from "@/componentss/shared/filter/filters/SortingByFilter.vue";
import BasicPagination from "@/componentss/shared/paginate/BasicPagination.vue";


export default {
	name: "Archive",
	components: {
    BasicPagination,
    SortingByFilter,
    EventListSearch,
    EventArchiveListBreadcrumbs,
    BasicListBadge,
    CategoryFilter,
    IsOnlineFilter,
    BasicListFilter,
    BasicFooter,
    EventListSelectDate,
    EventListFilter,
    EventListBreadcrumbs,
    MetaTags,
		MainPageNavBar,
		Link,
		Head,
	},
	props: {
		events: {
			type: Object,
		},
		currentDate: {
			type: String,
		},
		eventDates: {
			type: Array,
		},
		navigation: {
			type: Object,
		},
		filters: {
			type: Array,
		},
		categories: {
			type: Array,
		},
		breadcrumbs: {
			type: Object
		},
		seo: {
			type: Object,
		}
	},
  computed: {
    hasActiveFilters() {
      const filters = Object.values(this.filters);
      return filters.some(filter => filter.value !== null && filter.value !== '');
    }
  },
};
</script>

<template>
	<MetaTags :seo="seo" />

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>
  <div class="flex flex-col h-screen">
    <main class="flex-grow">
      <div class="relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
        <div class="pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto w-full max-w-screen-md"> <!-- Добавлен w-full -->
          <div>
            <EventArchiveListBreadcrumbs :breadcrumbs="breadcrumbs" />
            <div class="space-y-5 md:space-y-4">
              <div class="flex items-center w-full justify-center">
                <h1 class="block text-brand-primary text-center mb-3 mt-2 mr-4 text-3xl font-semibold tracking-tight lg:text-[35px] lg:leading-tight">
                  Архив мероприятий НТГСПИ
                </h1>
                <BasicListFilter>
                  <SortingByFilter :sortingBy_filter="filters.sortingBy_filter" />
                  <IsOnlineFilter :is_online_filter="filters.is_online_filter" />
                  <CategoryFilter :categories="categories" :category_filter="filters.category_filter" />
                </BasicListFilter>
              </div>
              <div class="space-y-5 md:space-y-4">
                <EventListSearch :search_filter="filters.search_filter" />
              </div>
              <div>
                <h3 class="text-sm text-gray-500 mb-4">Найдено мероприятий: {{ events.meta.total }}</h3>
                <div v-if="hasActiveFilters" class="flex-wrap flex gap-3 md:items-center">
                  <BasicListBadge :filters="filters" />
                </div>
              </div>
            </div>
            <div class="grid gap-y-10 mt-5 w-full"> <!-- Добавлен w-full -->
              <template v-for="event in events.data">
                <Link class="group sm:flex rounded-xl w-full" :href="route('client.event.show', event.slug)"> <!-- Добавлен w-full -->
                  <div class="grow">
                    <div class="flex flex-col h-full">
                      <div class="mb-3">
                        <p class="inline-flex mr-2 items-center gap-1.5 py-1.5 rounded-md text-[12px] text-gray-600">
                          {{ event.event_date_start }}
                        </p>
                        <p v-if="event.category" class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] text-blue-600">
                          {{ event.category }}
                        </p>
                      </div>
                      <h3 class="text-lg sm:text-2xl font-semibold text-gray-800 group-hover:text-blue-600">
                        {{ event.title }}
                      </h3>
                      <p class="mt-2 text-gray-600">
                        Great news we're eager to share.
                      </p>
                    </div>
                  </div>
                </Link>
              </template>
            </div>
            <BasicPagination :links="events.links" />
          </div>
        </div>
      </div>
    </main>
    <BasicFooter />
  </div>
</template>

<style scoped>


</style>
