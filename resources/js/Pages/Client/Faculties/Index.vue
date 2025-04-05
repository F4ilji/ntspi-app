<script>
import {Head, Link} from "@inertiajs/vue3";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import FacultyListBreadcrumbs from "@/componentss/features/faculties/components/FacultyListBreadcrumbs.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import BasicTitle from "@/componentss/ui/titles/BasicTitle.vue";
import FacultyListTitle from "@/componentss/features/faculties/components/FacultyListTitle.vue";

export default {
  name: "Index",
  components: {
    FacultyListTitle,
    BasicTitle,
    BasicFooter,
    FacultyListBreadcrumbs,
    MainPageNavBar,
    MetaTags,
    Link
  },
  data() {
    return {

    };
  },
  props: {
		faculties: {
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
	<MetaTags :seo="seo" />

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl py-10 md:flex md:flex-row md:py-10">
				<div class="px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto w-full max-w-screen-xl">
					<div>
						<!-- Avatar Media -->
						<!-- End Avatar Media -->
						<!-- Content -->
						<div class="space-y-5 md:space-y-4">
							<div class="space-y-5 md:space-y-4">
								<!-- Card Section -->
								<div class="max-w-[85rem] px-0 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
                  <FacultyListTitle
                      bottom-text="Информация о наших факультетах и кафедрах"
                      header="Факультеты и кафедры" />
									<FacultyListBreadcrumbs />

									<!-- Grid -->
									<div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-6">
										<template v-for="faculty in faculties.data">
											<Link :href="route('client.faculty.show', faculty.slug)" class="group flex flex-col bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition" >
												<div class="p-4 md:p-5">
													<div class="flex justify-between items-center gap-x-3">
														<div class="grow">
															<h3 class="group-hover:text-primary-hover font-semibold text-gray-800">
																{{ faculty.shortTitle }}
															</h3>
															<p class="text-sm text-gray-500">
																{{ faculty.title }}
															</p>
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
		<BasicFooter />
	</div>
</template>

<style scoped></style>
