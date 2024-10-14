<script>
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
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import AcademicJournalsBuilder from "@/Components/BuilderUi/AcademicJournals/AcademicJournalsBuilder.vue";
import AcademicJournalsTitle from "@/Components/BuilderUi/AcademicJournals/AcademicJournalsTitle.vue";

export default {
  name: "Show",
  components: {
		AcademicJournalsTitle,
		AcademicJournalsBuilder,
		MainPageNavBar,
		ClientImageSlider,
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
		journal: {
			type: Object
		},
		journals: {
			type: Object
		},
		years: {
			type: Object
		},
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

  },
  mounted() {
  }
};
</script>

<template>
  <Head>
    <title>Журнал</title>
    <meta name="description" content="Your page description"/>
  </Head>
	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:py-10">
				<div class="w-100">
					<div>
						<!-- Avatar Media -->
						<!-- End Avatar Media -->
						<!-- Content -->
						<div class="space-y-5 md:space-y-8">
							<AcademicJournalsTitle class="text-center" :header="journal.data.title" />
							<div>
								<nav class="flex flex-col md:flex-row justify-center gap-x-6" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
									<button type="button" class="hs-tab-active:bg-gray-100 rounded-md hs-tab-active:text-gray-700 py-1.5 px-3 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 focus:outline-none disabled:opacity-50 disabled:pointer-events-none active"
													id="horizontal-alignment-item-1" aria-selected="true" data-hs-tab="#horizontal-alignment-1" aria-controls="horizontal-alignment-1" role="tab">
										Основная информация журнала
									</button>
									<button type="button" class="hs-tab-active:bg-gray-100 rounded-md hs-tab-active:text-gray-700 py-1.5 px-3 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
													id="horizontal-alignment-item-2" aria-selected="false" data-hs-tab="#horizontal-alignment-2" aria-controls="horizontal-alignment-2" role="tab">
										Редакция
									</button>
									<button type="button" class="hs-tab-active:bg-gray-100 rounded-md hs-tab-active:text-gray-700 py-1.5 px-3 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
													id="horizontal-alignment-item-3" aria-selected="false" data-hs-tab="#horizontal-alignment-3" aria-controls="horizontal-alignment-3" role="tab">
										Информация для авторов
									</button>
									<button type="button" class="hs-tab-active:bg-gray-100 rounded-md hs-tab-active:text-gray-700 py-1.5 px-3 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
													id="horizontal-alignment-item-3" aria-selected="false" data-hs-tab="#horizontal-alignment-4" aria-controls="horizontal-alignment-4" role="tab">
										Архив
									</button>

								</nav>
							</div>
							<div class="container max-w-4xl px-2 xl:px-5 lg:py-4 md:w-4/5 mx-auto">
								<div id="horizontal-alignment-1" role="tabpanel" aria-labelledby="horizontal-alignment-item-1">
									<AcademicJournalsBuilder :blocks="journal.data.main_info" />
								</div>
								<div id="horizontal-alignment-2" class="hidden w-full" role="tabpanel" aria-labelledby="horizontal-alignment-item-2">
									<template v-for="block in journal.data.chief_editor" :key="block.id">
										<div class="w-full rounded-xl mb-4 p-4 md:p-6 bg-white border border-gray-200 dark:bg-slate-900 dark:border-gray-700">
											<div class="flex items-center gap-x-4">
												<!--												<img loading="lazy" class="rounded-xl w-[150px]" :src="'/storage/' + worker.details.photo" alt="Image Description">-->
												<div class="grow">
													<p class="font-medium text-gray-800 hover:text-gray-500 underline">
														Главный редактор: {{ block.name }}
													</p>
													<p class="text-xs text-gray-500 mt-2">
														Ученая степень: {{ block.academicTitle }}
													</p>
													<p class="text-xs text-gray-500 mt-2">
														Должность: {{ block.position }}
													</p>
													<p class="text-xs text-gray-500 mt-2">
														Учереждение: {{ block.institution }}
													</p>
												</div>
											</div>

											<!-- Social Brands -->
											<!-- End Social Brands -->
										</div>

									</template>
									<template v-for="block in journal.data.editors" :key="block.id">
										<div class="w-full rounded-xl mb-4 p-4 md:p-6 bg-white border border-gray-200 dark:bg-slate-900 dark:border-gray-700">
											<div class="flex items-center gap-x-4">
												<!--												<img loading="lazy" class="rounded-xl w-[150px]" :src="'/storage/' + worker.details.photo" alt="Image Description">-->
												<div class="grow">
													<p class="font-medium text-gray-800 hover:text-gray-500">
														{{ block.name }}
													</p>
													<p class="text-xs text-gray-500 mt-2">
														Ученая степень: {{ block.academicTitle }}
													</p>
													<p class="text-xs text-gray-500 mt-2">
														Должность: {{ block.position }}
													</p>
													<p class="text-xs text-gray-500 mt-2">
														Учереждение: {{ block.institution }}
													</p>
												</div>
											</div>

											<!-- Social Brands -->
											<!-- End Social Brands -->
										</div>
									</template>
								</div>
								<div id="horizontal-alignment-3" class="hidden" role="tabpanel" aria-labelledby="horizontal-alignment-item-3">
									<AcademicJournalsBuilder :blocks="journal.data.for_authors" />
								</div>
								<div id="horizontal-alignment-4" class="hidden" role="tabpanel" aria-labelledby="horizontal-alignment-item-4">
									<!-- List -->
									<!-- Card Section -->
									<div class="px-4 py-5 sm:px-6 lg:px-8 lg:py-7 mx-auto">
										<!-- Grid -->
										<div class="">
											<template v-for="journalByYear in journals">
												<h2 class="font-bold text-xl text-center">{{ journalByYear.year_publication }}</h2>
												<hr class="mb-4 mt-2">
												<div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6 mb-4">
													<template v-for="item in journalByYear.journalIssues">
														<a target="_blank" class="group flex flex-col bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition" :href="'/storage/' + item.path_file">
															<div class="p-4 md:p-5">
																<div class="flex justify-between items-center gap-x-3">
																	<div class="grow">
																		<p class="text-sm text-gray-500">
																			{{ item.year_publication }}
																		</p>
																		<h3 class="group-hover:text-blue-600 font-semibold text-gray-800">
																			{{ item.title }}
																		</h3>
																	</div>
																	<div>
																		<svg class="shrink-0 size-5 text-gray-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
																	</div>
																</div>
															</div>
														</a>
													</template>
												</div>
											</template>

										</div>
										<!-- End Grid -->
									</div>
									<!-- End Card Section -->									<!-- End List -->
								</div>

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

<style>

.paragraph-container a {
	@apply text-[#1E57A3];
	@apply underline;
}

.paragraph-container p {
	@apply mb-2
}

.paragraph-container ol li {
	@apply list-decimal list-inside
}

.paragraph-container ul li {
	@apply list-disc list-inside
}

.paragraph-container li ol {
	@apply ml-10
}

.paragraph-container ul {
	@apply mb-2
}

</style>
