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
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import BackButton from "@/componentss/ui/buttons/BackButton.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import BasicTitle from "@/componentss/ui/titles/BasicTitle.vue";
import EventItemBreadcrumbs from "@/componentss/features/events/components/EventItemBreadcrumbs.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";


export default {
	name: "Index",
	components: {
    BaseBreadcrumbs, BreadcrumbsItem,
    EventItemBreadcrumbs, BasicTitle, Builder, BackButton, BasicPageWrapper, BasicPageContainer,
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
			type: Object,
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
      return this.filters.category_filter.value || this.filters.is_online_filter.value
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
        <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
          <BreadcrumbsItem title="Мероприятия" :url="route('client.event.index')" />
        </BaseBreadcrumbs>
        <div class="space-y-5 md:space-y-4">
          <div class="flex items-center w-full justify-center">
            <h1 class="block text-brand-primary text-center mb-3 mt-2 mr-4 text-3xl font-semibold tracking-tight dark:text-white lg:text-[40px] lg:leading-tight">
              Мероприятия НТГСПИ
            </h1>
            <BasicListFilter>
              <IsOnlineFilter :is_online_filter="filters.is_online_filter" />
              <CategoryFilter :categories="categories" :category_filter="filters.category_filter" />
            </BasicListFilter>

          </div>

          <div class="my-10 justify-center flex gap-x-3 items-center">
            <h3 class="font-light text-xl">Мероприятия на </h3>
            <div class="shadow-sm w-[35px]">
              <div class="block w-[35px] h-[8px] bg-red-400 rounded-t"></div>
              <div class="block w-[35px] h-[27px] bg-white text-center font-medium">{{ currentDate.day }}</div>
            </div>
            <h3 class="font-light text-xl">{{ currentDate.month }}</h3>
          </div>
          <div class="space-y-5 md:space-y-4">
            <div>
              <EventListSelectDate :current-date="currentDate.fullDate" :dates="eventDates"/>
            </div>

          </div>
          <div v-if="hasActiveFilters">
            <h3 class="text-sm text-gray-500 mb-4">Найдено мероприятий: {{ events.data.length }}</h3>
            <div class="flex-wrap flex gap-3 md:items-center">
              <BasicListBadge :filters="filters" />
            </div>
          </div>
        </div>
        <div class="grid gap-y-10 mt-10">
          <template v-for="event in events.data">
            <div class="sm:flex rounded-xl">
              <div class="grow">
                <div class="flex flex-col h-full">
                  <div class="mb-3">
                    <p class="inline-flex mr-2 items-center gap-1.5 py-1.5 rounded-md text-[12px] text-gray-600">
                      {{ event.event_time_start }}
                    </p>

                    <a :href="route('client.event.index', { 'category[]': event.category.slug })" v-if="event.category" class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] text-primary hover:underline">
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
      </div>
    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>

</template>

<style scoped>


</style>
