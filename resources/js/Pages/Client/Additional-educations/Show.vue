<script>
import {Head, Link} from "@inertiajs/vue3";

import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";

import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import BackButton from "@/componentss/ui/buttons/BackButton.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import ProgramTitle from "@/componentss/features/educationalPrograms/components/ProgramTitle.vue";
import AdditionalProgramItemBreadcrumbs
  from "@/componentss/features/additionalEducationPrograms/components/AdditionalProgramItemBreadcrumbs.vue";


export default {
  name: "Show",
  components: {
    AdditionalProgramItemBreadcrumbs,
    BasicFooter,
    Builder,
    BackButton,
    MetaTags,
		ProgramTitle,
		MainPageNavBar,
		Link, Head
  },
  data() {
    return {

    }
  },
  props: {
		additionalEducation: {
      type: Object,
    },
		breadcrumbs: {
			type: Object,
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
	<MetaTags :seo="seo" />

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="px-0 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto w-full max-w-screen-md">
					<div>

						<div class="space-y-5 md:space-y-4">
							<div class="space-y-5 md:space-y-4">

								<div>
									<div class="container mx-auto xl:px-5 max-w-screen-lg py-5 lg:py-8">
										<div class="space-y-10">
											<div class="space-y-3">
												<BackButton link="client.additionalEducation.index" title="Все программы"/>
												<AdditionalProgramItemBreadcrumbs :breadcrumbs="breadcrumbs" :additional-education-title="additionalEducation.data.title" />
											</div>
											<ProgramTitle :header="additionalEducation.data.title" class="text-center" />
										</div>

										<!-- Icon Blocks -->
										<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
											<div class="grid sm:grid-cols-2 lg:grid-cols-3 items-start gap-12">
												<!-- Icon Block -->
												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 size-6 text-gray-600">
															<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
														</svg>
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
														<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 size-6 text-gray-600">
															<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
														</svg>

													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Срок обучения</h3>
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.learning_time }} часов</p>
													</div>
												</div>
												<!-- End Icon Block -->

												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 size-6 text-gray-600">
															<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
														</svg>

													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Присваиваемая квалификация</h3>
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.qualification }}</p>
													</div>
												</div>

												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 size-6 text-gray-600">
															<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
														</svg>

													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Форма обучения</h3>
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.form_education }}</p>
													</div>
												</div>

												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 size-6 text-gray-600">
															<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
														</svg>
													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Цена обучения</h3>
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.price }} &#8381;</p>
													</div>
												</div>

												<div class="text-center">
													<div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
														<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 size-6 text-gray-600">
															<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
														</svg>

													</div>
													<div class="mt-3">
														<h3 class="text-lg font-semibold text-gray-800">Целевая аудитория</h3>
														<p class="mt-1 text-gray-600">{{ additionalEducation.data.target_group }}</p>
													</div>
												</div>



												<!-- End Icon Block -->
											</div>
										</div>

										<div v-if="additionalEducation.data.content.length > 0">
											<h2 class="text-xl font-bold md:text-2xl">О программе</h2>
											<div class="py-4 px-5">
												<Builder :blocks="additionalEducation.data.content" />
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

		<BasicFooter />
	</div>

</template>

<style>



</style>