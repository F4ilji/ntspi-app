<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import DivisionListTitle from "@/componentss/features/divisions/components/DivisionListTitle.vue";
import DivisionListBreadcrumbs from "@/componentss/features/divisions/components/DivisionListBreadcrumbs.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";

export default {
  name: "Index",
  components: {
    BaseBreadcrumbs, BreadcrumbsItem,
    BasicFooter,
    DivisionListTitle,
    MetaTags,
		DivisionListBreadcrumbs,
		MainPageNavBar,
    Link,
    Head,
  },
  data() {
    return {

    };
  },
  props: {
		divisions: {
        type: Object,
    },
		seo: {
			type: Object,
		},
    breadcrumbs: {
      type: Object
    }
  },
};
</script>

<template>
	<MetaTags :seo="seo" />

	<MainPageNavBar :sections="$page.props.navigation" />

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

                  <DivisionListTitle
                      bottom-text="Список структурных и административных отделов НТГСПИ"
                      header="Подразделения института" />

                  <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
                    <BreadcrumbsItem title="Подразделения института" :url="route('client.division.index')" />
                  </BaseBreadcrumbs>

									<!-- Grid -->
									<div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-6">
										<template v-for="division in divisions.data">
											<Link :href="route('client.division.show', division.slug)" class="group flex items-center bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition" >
												<div class="p-4 md:p-5 w-full">
													<div class="flex justify-between items-center gap-x-3">
														<div class="grow">
															<h3 class="group-hover:text-primary-hover font-semibold text-gray-800">
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

		<BasicFooter/>
	</div>
</template>

<style scoped></style>
