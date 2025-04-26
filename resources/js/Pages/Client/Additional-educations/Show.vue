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
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import ProgramItemBreadcrumbs from "@/componentss/features/educationalPrograms/components/ProgramItemBreadcrumbs.vue";
import FormBlock from "@/componentss/shared/builder/pageBuilder/blocks/FormBlock.vue";
import FormBuilder from "@/componentss/shared/builder/formBuilder/FormBuilder.vue";


export default {
  name: "Show",
  components: {
    FormBuilder,
    FormBlock,
    ProgramItemBreadcrumbs, BasicPageWrapper, BasicPageContainer,
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
		},
    form: {
      type: String
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

	<MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <BasicPageContainer>
      <div class="space-y-3">
        <BackButton link="client.additionalEducation.index" title="Все программы"/>
        <AdditionalProgramItemBreadcrumbs class="pb-4" :breadcrumbs="breadcrumbs" :additional-education-title="additionalEducation.data.title" />
        <ProgramTitle :header="additionalEducation.data.title" class="text-center" />
      </div>

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

          <div v-if="additionalEducation.data.qualification" class="text-center">
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
        <div class="py-4">
          <Builder :blocks="additionalEducation.data.content" />
        </div>
      </div>

    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>

  <div class="fixed bottom-0 end-0 z-60 sm:max-w-xl w-full mx-auto p-6">
    <div class="hs-accordion-group">
      <div class="hs-accordion border-gray-200 active bg-white border rounded-xl" id="hs-active-bordered-heading-two">
        <button class="hs-accordion-toggle hs-accordion-active:text-blue-600 inline-flex justify-between items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-8 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none rounded-xl" aria-expanded="true" aria-controls="hs-basic-active-bordered-collapse-two">
          Запишитесь на консультацию по курсу
          <svg class="hs-accordion-active:hidden block size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"></path>
            <path d="M12 5v14"></path>
          </svg>
          <svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"></path>
          </svg>
        </button>
        <div id="hs-basic-active-bordered-collapse-two" class="hs-accordion-content w-full overflow-hidden transition-[height] rounded-xl duration-300" role="region" aria-labelledby="hs-active-bordered-heading-two">
          <div class="px-8 pb-4 pt-0 bg-white rounded-xl shadow-2xs">
            <div class="flex rounded-xl">
              <div class="grow rounded-xl">
                <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                  Заполните форму обратной связи и с вами свяжется специалист центра дополнительного образования
                </p>
                <div class="mt-5 inline-flex gap-x-2">
                  <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-vertically-centered-scrollable-modal" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
                    Записаться
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div v-if="form" id="hs-vertically-centered-scrollable-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[1000000] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="hs-vertically-centered-scrollable-modal-label">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-3xl sm:w-full m-3 sm:mx-auto h-[calc(100%-56px)] min-h-[calc(100%-56px)] flex items-center">
      <div class="w-full max-h-full overflow-hidden flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
        <div class="flex justify-between items-center py-3 px-4 border-gray-200 dark:border-neutral-700">
          <h3 id="hs-vertically-centered-scrollable-modal-label" class="font-bold text-gray-800 dark:text-white">
          </h3>
          <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
            <span class="sr-only">Close</span>
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 6 6 18"></path>
              <path d="m6 6 12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-2 overflow-y-auto">
          <FormBlock :form-id="form" />
        </div>
      </div>
    </div>
  </div>


</template>

<style>



</style>