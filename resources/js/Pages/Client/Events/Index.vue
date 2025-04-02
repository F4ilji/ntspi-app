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


export default {
	name: "Index",
	components: {
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
      return this.filters.category_filter.value || this.filters.is_online_filter.value
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
				<div class="pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto w-full max-w-screen-md">
					<div>
						<EventListBreadcrumbs :breadcrumbs="breadcrumbs" />
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

												<a :href="route('client.event.index', { 'category[]': event.category.slug })" v-if="event.category" class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] text-blue-600 hover:underline">
													{{ event.category.title }}
												</a>
											</div>
											<Link :href="route('client.event.show', event.slug)" class="text-lg sm:text-2xl font-semibold text-gray-800 hover:text-blue-600">
												{{ event.title }}
											</Link>
										</div>
									</div>
								</div>
							</template>
						</div>
					</div>
				</div>
			</div>
		</main>
		<BasicFooter />
	</div>

</template>

<style scoped>


</style>
