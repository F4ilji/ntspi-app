<script>
import MainNavbar from "@/Navbars/MainNavbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue/v3";
import ClientScrollTimeline from "@/Components/ClientScrollTimeline.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import AdminIndexSearch from "@/Components/AdminIndexSearch.vue";
import AdminIndexFilter from "@/Components/AdminIndexFilter.vue";
import AdminIndexHeader from "@/Components/AdminIndexHeader.vue";
import AdminIndexHeaderTitle from "@/Components/AdminIndexHeaderTitle.vue";
import ClientPostFilter from '@/Components/ClientPostFilter.vue';
import ClientPost from '@/Components/ClientPost.vue';
import ClientPostSearch from '@/Components/ClientPostSearch.vue';
import ClientEventSelectDate from "@/Components/ClientEventSelectDate.vue";
import ClientEventFilter from "@/Components/ClientEventFilter.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import EventListBreadcrumbs from "@/Components/BuilderUi/Events/EventListBreadcrumbs.vue";
import AppHead from "@/Components/AppHead.vue";

export default {
	name: "Index",
	components: {
		AppHead,
		EventListBreadcrumbs,
		MainPageNavBar,
		ClientEventFilter,
		ClientEventSelectDate,
		AdminIndexHeaderTitle, AdminIndexHeader,
		AdminIndexFilter, AdminIndexSearch,
		ClientFooterDown,
		ClientScrollTimeline,
		ClientPostFilter,
		Link,
		MainNavbar,
		FsLightbox,
		Head,
		ClientPost,
		ClientPostSearch
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
	methods: {},
};
</script>

<template>
	<AppHead :seo="seo" />

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>
	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto">
					<div>
						<!--        <AdminIndexHeader>-->
						<!--          <ClientPostSearch />-->
						<!--          <ClientPostFilter :items="categories"/>-->
						<!--        </AdminIndexHeader>-->
						<EventListBreadcrumbs :breadcrumbs="breadcrumbs" />
						<div class="space-y-5 md:space-y-4">
							<div class="flex items-center w-full justify-center">
								<h1 class="block text-brand-primary text-center mb-3 mt-2 mr-4 text-3xl font-semibold tracking-tight dark:text-white lg:text-[40px] lg:leading-tight">
									Мероприятия НТГСПИ
								</h1>
								<ClientEventFilter :is_online_filter="filters.is_online_filter" :categories="categories" :category_filter="filters.category_filter" :sorting-by_filter="filters.sortingBy_filter" />

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
									<ClientEventSelectDate :current-date="currentDate.fullDate" :dates="eventDates"/>
								</div>

							</div>
						</div>
						<div class="grid gap-y-10 mt-10">
							<template v-for="event in events.data">
								<Link class="group sm:flex rounded-xl" :href="route('client.event.show', event.slug)">
									<div class="grow">
										<div class="flex flex-col h-full">
											<div class="mb-3">
												<p class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-[12px] text-gray-600">
													{{ event.event_time_start }}
												</p>
												<p class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] text-blue-600">
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
					</div>
				</div>
			</div>
		</main>
		<ClientFooterDown/>
	</div>

</template>

<style scoped>


</style>
