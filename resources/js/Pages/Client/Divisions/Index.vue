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
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import DivisionListBreadcrumbs from "@/Components/BuilderUi/Divisions/DivisionListBreadcrumbs.vue";
import AppHead from "@/Components/AppHead.vue";

export default {
  name: "Index",
  components: {
		AppHead,
		DivisionListBreadcrumbs,
		MainPageNavBar,
    AdminIndexHeaderTitle, AdminIndexHeader,
    AdminIndexFilter, AdminIndexSearch,
    ClientFooterDown,
    ClientScrollTimeline,
    ClientPostFilter,
    Link,
    FsLightbox,
    Head,
    ClientPost,
    ClientPostSearch
  },
  data() {
    return {

    };
  },
  props: {
		divisions: {
        type: Array,
    },
		seo: {
			type: Object,
		}
  },
  methods: {

  },

	mounted() {
  }
};
</script>

<template>
	<AppHead :seo="seo" />

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto">
					<div>
						<!-- Avatar Media -->
						<!-- End Avatar Media -->
						<!-- Content -->
						<div class="space-y-5 md:space-y-4">
							<div class="space-y-5 md:space-y-4">
								<!-- Card Section -->
								<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">

									<div class="max-w-2xl text-center mx-auto mb-10 lg:mb-14">

										<h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Подразделения института</h2>
										<p class="mt-1 text-gray-600 dark:text-neutral-400">Список структурных и административных отделов НТГСПИ</p>
									</div>
									<DivisionListBreadcrumbs />

									<!-- Grid -->
									<div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-6">
										<template v-for="division in divisions.data">
											<Link :href="route('client.division.show', division.slug)" class="group flex items-center bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition" >
												<div class="p-4 md:p-5 w-full">
													<div class="flex justify-between items-center gap-x-3">
														<div class="grow">
															<h3 class="group-hover:text-blue-600 font-semibold text-gray-800">
																{{ division.title }}
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
									<!-- End Grid -->
								</div>
								<!-- End Card Section -->
							</div>
						</div>
						<!-- End Content -->
					</div>
				</div>
			</div>
		</main>
		<ClientFooterDown/>
	</div>
</template>

<style scoped></style>
