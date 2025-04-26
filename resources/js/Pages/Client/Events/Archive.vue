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
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import BackButton from "@/componentss/ui/buttons/BackButton.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import BasicTitle from "@/componentss/ui/titles/BasicTitle.vue";
import EventItemBreadcrumbs from "@/componentss/features/events/components/EventItemBreadcrumbs.vue";


export default {
	name: "Archive",
	components: {
    EventItemBreadcrumbs, BasicTitle, Builder, BackButton, BasicPageWrapper, BasicPageContainer,
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
			type: Object,
		},
		navigation: {
			type: Object,
		},
		filters: {
			type: Object,
		},
		categories: {
			type: Object,
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

	<MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <BasicPageContainer breakpoint="md">
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
            <div class="sm:flex rounded-xl w-full"> <!-- Добавлен w-full -->
              <div class="grow">
                <div class="flex flex-col h-full">
                  <div class="mb-3">
                    <p class="inline-flex mr-2 items-center gap-1.5 py-1.5 rounded-md text-[12px] text-gray-600">
                      {{ event.event_date_start }}
                    </p>
                    <a :href="route('client.event.archive', { 'category[]': event.category.slug })" v-if="event.category" class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] text-primary hover:underline">
                      {{ event.category.title }}
                    </a>
                  </div>
                  <Link :href="route('client.event.show', event.slug)" class="text-lg sm:text-2xl font-semibold text-gray-800 hover:text-primary-hover">
                    {{ event.title }}
                  </Link>
                </div>
              </div>
            </div>
          </template>
        </div>
        <BasicPagination v-if="events.links.next" :links="events.links" />
      </div>

    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>


</template>

<style scoped>


</style>
