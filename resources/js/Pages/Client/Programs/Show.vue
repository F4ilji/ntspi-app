<script>
import MainNavbar from "@/Navbars/MainNavbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue/v3";
import ClientScrollTimeline from "@/Components/ClientScrollTimeline.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import ClientPostFilter from "@/Components/ClientPostFilter.vue";
import ClientPost from "@/Components/ClientPost.vue";
import AdminIndexHeader from "@/Components/AdminIndexHeader.vue";
import ClientPostSearch from "@/Components/ClientPostSearch.vue";
import ClientProgramFilter from "@/Components/ClientProgramFilter.vue";
import LevelEduFilter from "@/Components/BuilderUi/Programs/Filters/LevelEduFilter.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import FormBlock from "@/Components/BuilderUi/Pages/Blocks/FormBlock.vue";
import ProgramItemBreadcrumbs from "@/Components/BuilderUi/Programs/ProgramItemBreadcrumbs.vue";
import AdditionalEducationProgramItemBreadcrumbs
	from "@/Components/BuilderUi/AdditionalEducationPrograms/AdditionalEducationProgramItemBreadcrumbs.vue";
import ProgramTitle from "@/Components/BuilderUi/AdditionalEducationPrograms/ProgramTitle.vue";
import ProgramBuilder from "@/Components/BuilderUi/AdditionalEducationPrograms/ProgramBuilder.vue";
import ProgramBackButton from "@/Components/BuilderUi/Programs/ProgramBackButton.vue";
import BaseBuilder from "@/Components/BaseComponents/BaseBuilderUi/BaseBuilder.vue";
import AppHead from "@/Components/AppHead.vue";


export default {
  name: "Show",
  components: {
		AppHead,
		BaseBuilder,
		ProgramBackButton, ProgramBuilder, ProgramTitle, AdditionalEducationProgramItemBreadcrumbs,
		ProgramItemBreadcrumbs,
		FormBlock,
		MainPageNavBar,
		LevelEduFilter, ClientProgramFilter,
		ClientPostSearch,
		AdminIndexHeader,
		ClientPost,
		ClientPostFilter,
		ClientFooterDown, ClientScrollTimeline, Link, MainNavbar, FsLightbox, Head},
  data() {
    return {
      blocks: this.blocksWithSlideNumber,
      userData: this.$page.props.auth.user,
      toggler: false,
      togglerGallery: false,
      editorImages: [],
      galleryImages: [],
      slide: 1,
      gallerySlide: 1,
    }
  },
  props: {
    program: {
      type: Array,
    },
		formsEdu: {
			type: Array
		},
		seo: {
			type: Object,
		}

  },
  methods: {
		getFormOfStudy(contests) {
			const formOfStudyArray = Array.from(new Set(contests.map(item => item.form_edu)));

			return formOfStudyArray;
		},
		getYearOfStudy(contests) {
			const YearOfStudyArray = Array.from(new Set(contests.map(item => item.period_data)));

			return YearOfStudyArray;
		}

  },
}
</script>

<template>
	<AppHead :seo="seo" />

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="px-0 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto">
					<div>

						<div class="space-y-5 md:space-y-4">
							<div class="space-y-5 md:space-y-4">

								<div>
									<div class="container mx-auto xl:px-5 max-w-screen-lg py-5 lg:py-8">
										<div class="space-y-3">
											<ProgramBackButton link="client.program.index" title="Все программы" />
											<ProgramItemBreadcrumbs :program-title="program.data.name" />
											<ProgramTitle :header="program.data.name" />

										</div>

										<!-- Icon Blocks -->
										<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
											<div class="grid sm:grid-cols-2 lg:grid-cols-3 items-start gap-12">
												<!-- Icon Block -->
												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="10" height="14" x="3" y="8" rx="2"/><path d="M5 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2h-2.4"/><path d="M8 18h.01"/></svg>
													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Направление подготовки</h3>
														<p class="mt-1 text-gray-600">{{ program.data.directionStudy.name }} {{ program.data.directionStudy.code }}</p>
													</div>
												</div>
												<!-- End Icon Block -->

												<!-- Icon Block -->
												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg>
													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Срок обучения</h3>
														<template v-for="data in program.data.learning_forms">
															<p class="mt-1 text-gray-600">{{ data.period_data }} <span class="text-gray-400 text-[12px]">({{ data.form_edu }})</span> </p>
														</template>
													</div>
												</div>
												<!-- End Icon Block -->

												<!-- Icon Block -->
												<!--										<div class="text-center">-->
												<!--											<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">-->
												<!--												<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>-->
												<!--											</div>-->
												<!--											<div class="mt-3">-->
												<!--												<h3 class="text-lg font-semibold text-gray-800">Форма обучения</h3>-->
												<!--												<template v-for="form in getFormOfStudy(program.data.learning_forms)">-->
												<!--													<p class="mt-1 text-gray-600">{{ form }}</p>-->
												<!--												</template>-->
												<!--											</div>-->
												<!--										</div>-->
												<!-- End Icon Block -->



												<!-- Icon Block -->
												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4c0-1.1.9-2 2-2h8a2 2 0 0 1 2 2v5Z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/></svg>
													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Количество мест на прием</h3>
														<template v-for="admissionPlan in program.data.admissionPlans">
															<template v-for="contest in admissionPlan.contests">
																<div class="border rounded my-2 py-1">
																	<span class="text-gray-500 text-[14px]">{{ (contest.form_education === '1') ? 'Очная форма обучения' : 'Заочная форма обучения' }}</span>
																	<p v-for="place in contest.places" class="mt-1 text-gray-600">{{ place.count }} мест <span class="text-gray-400 text-[12px]">{{ formsEdu[place.form_budget] }}</span></p>
																</div>
															</template>
														</template>
													</div>
												</div>
												<!-- End Icon Block -->
											</div>
										</div>

										<div>
											<div class="hs-accordion-group">
												<div class="hs-accordion bg-white border -mt-px first:rounded-t-lg last:rounded-b-lg" id="hs-bordered-heading-one">
													<button class="hs-accordion-toggle hs-accordion-active:text-blue-600 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none" aria-controls="hs-basic-bordered-collapse-two">
														<svg class="hs-accordion-active:hidden block size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M5 12h14"></path>
															<path d="M12 5v14"></path>
														</svg>
														<svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M5 12h14"></path>
														</svg>
														Условия поступления
													</button>
													<div id="hs-basic-bordered-collapse-two" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" aria-labelledby="hs-bordered-heading-two">
														<div class="pb-4 px-5">
															<ol class="list-decimal list-inside">
																<template v-for="admissionPlan in program.data.admissionPlans">
																	<template v-for="exam in admissionPlan.exams">
																		<li>{{ exam.title }} <span class="text-gray-400 text-[14px]">({{ (exam.type_exam === 'ege' ? 'ЕГЭ' : 'ВИ') }}, минимальный балл: {{ exam.min_score }})</span></li>
																	</template>
																</template>
															</ol>
														</div>
													</div>
												</div>

												<div class="hs-accordion bg-white border -mt-px first:rounded-t-lg last:rounded-b-lg" id="hs-bordered-heading-two">
													<button class="hs-accordion-toggle hs-accordion-active:text-blue-600 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none" aria-controls="hs-basic-bordered-collapse-two">
														<svg class="hs-accordion-active:hidden block size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M5 12h14"></path>
															<path d="M12 5v14"></path>
														</svg>
														<svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M5 12h14"></path>
														</svg>
														О программе
													</button>
													<div id="hs-basic-bordered-collapse-two" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" aria-labelledby="hs-bordered-heading-two">
														<div class="pb-4 px-5">
															<BaseBuilder :blocks="program.data.about_program" />
														</div>
													</div>
												</div>

												<div class="hs-accordion bg-white border -mt-px first:rounded-t-lg last:rounded-b-lg" id="hs-bordered-heading-three">
													<button class="hs-accordion-toggle hs-accordion-active:text-blue-600 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none" aria-controls="hs-basic-bordered-collapse-three">
														<svg class="hs-accordion-active:hidden block size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M5 12h14"></path>
															<path d="M12 5v14"></path>
														</svg>
														<svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M5 12h14"></path>
														</svg>
														Особенности программы
													</button>
													<div id="hs-basic-bordered-collapse-three" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" aria-labelledby="hs-bordered-heading-three">
														<div class="pb-4 px-5">
															<BaseBuilder :blocks="program.data.program_features" />
														</div>
													</div>
												</div>
											</div>
										</div>
										<!-- End Icon Blocks -->

									</div>
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


</style>