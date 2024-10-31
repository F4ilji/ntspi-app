<script>
import {Head, Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue/v3";
import ClientScrollTimeline from "@/Components/ClientScrollTimeline.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import ClientPostFilter from "@/Components/ClientPostFilter.vue";
import ClientPost from "@/Components/ClientPost.vue";
import AdminIndexHeader from "@/Components/AdminIndexHeader.vue";
import ClientPostSearch from "@/Components/ClientPostSearch.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import FormBuilder from "@/Components/BuilderUi/Pages/FormBuilder.vue";
import FormBlock from "@/Components/BuilderUi/Pages/Blocks/FormBlock.vue";
import ProgramBackButton from "@/Components/BuilderUi/Programs/ProgramBackButton.vue";
import PostTitle from "@/Components/BuilderUi/Posts/PostTitle.vue";
import PostBackButton from "@/Components/BuilderUi/Posts/PostBackButton.vue";
import ProgramTitle from "@/Components/BuilderUi/AdditionalEducationPrograms/ProgramTitle.vue";
import ProgramBuilder from "@/Components/BuilderUi/AdditionalEducationPrograms/ProgramBuilder.vue";
import AdditionalEducationProgramItemBreadcrumbs
	from "@/Components/BuilderUi/AdditionalEducationPrograms/AdditionalEducationProgramItemBreadcrumbs.vue";


export default {
  name: "Show",
  components: {
		AdditionalEducationProgramItemBreadcrumbs,
		ProgramBuilder,
		ProgramTitle,
		PostBackButton, PostTitle,
		ProgramBackButton,
		FormBlock,
		FormBuilder,
		MainPageNavBar,
		ClientPostSearch,
		AdminIndexHeader,
		ClientPost,
		ClientPostFilter,
		ClientFooterDown, ClientScrollTimeline, Link, FsLightbox, Head},
  data() {
    return {

    }
  },
  props: {
		additionalEducation: {
      type: Object,
    },
		breadcrumbs: {
			type: Object
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
  <Head>
    <title>Образовательная программа</title>
    <meta name="description" content="Your page description">
  </Head>
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
											<ProgramBackButton title="Назад" />
											<AdditionalEducationProgramItemBreadcrumbs :breadcrumbs="breadcrumbs" :additional-education-title="additionalEducation.data.title" />
											<ProgramTitle :header="additionalEducation.data.title" class="text-center" />
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
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.directionAdditionalEducation.title }} {{ additionalEducation.data.category }}</p>
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
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.learning_time }} часов</p>
													</div>
												</div>
												<!-- End Icon Block -->

												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Присваиваемая квалификация</h3>
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.qualification }}</p>
													</div>
												</div>

												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Форма обучения</h3>
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.form_education }}</p>
													</div>
												</div>

												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Цена обучения</h3>
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.price }} &#8381;</p>
													</div>
												</div>

												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Целевая аудитория</h3>
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.target_group }}</p>
													</div>
												</div>



												<!-- End Icon Block -->
											</div>
										</div>

										<div>
											<h2 class="text-xl font-bold md:text-2xl">О программе</h2>
											<div class="py-4 px-5">
												<ProgramBuilder :blocks="additionalEducation.data.content" />
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
	@apply mb-6
}


</style>