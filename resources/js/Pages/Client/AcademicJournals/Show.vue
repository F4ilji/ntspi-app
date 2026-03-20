<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import AcademicJournalsTitle from "@/componentss/features/academicJournals/components/AcademicJournalsTitle.vue";
import BasicPagination from "@/componentss/shared/paginate/BasicPagination.vue";
import AcademicJournalsItemBreadcrumbs
  from "@/componentss/features/academicJournals/components/AcademicJournalsItemBreadcrumbs.vue";
import BudgetFilter from "@/componentss/shared/filter/filters/BudgetFilter.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import BasicListFilter from "@/componentss/shared/filter/BasicListFilter.vue";
import DirectionFilter from "@/componentss/shared/filter/filters/DirectionFilter.vue";
import ProgramLevelEduFilter from "@/componentss/features/educationalPrograms/components/ProgramLevelEduFilter.vue";
import ProgramTitle from "@/componentss/features/educationalPrograms/components/ProgramTitle.vue";
import ProgramListBreadcrumbs from "@/componentss/features/educationalPrograms/components/ProgramListBreadcrumbs.vue";
import FormEducationalFilter from "@/componentss/shared/filter/filters/FormEducationalFilter.vue";
import BackButton from "@/componentss/ui/buttons/BackButton.vue";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";

export default {
  name: "Show",
  components: {
    BaseBreadcrumbs,
    BreadcrumbsItem,
    BasicIcon,
    BackButton,
    FormEducationalFilter,
    ProgramListBreadcrumbs,
    ProgramTitle,
    ProgramLevelEduFilter,
    DirectionFilter,
    BasicListFilter,
    BasicPageWrapper,
    BasicPageContainer,
    BudgetFilter,
    AcademicJournalsItemBreadcrumbs,
    BasicPagination,
    MetaTags,
    BasicFooter,
    Builder,
		AcademicJournalsTitle,
		MainPageNavBar,
    Link,
    Head,
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
		seo: {
			type: Object,
		},
    breadcrumbs: {
      type: Object
    }
  }
};
</script>

<template>
	<MetaTags :seo="seo" />

	<MainPageNavBar :sections="$page.props.navigation" />


  <BasicPageWrapper>
    <BasicPageContainer>
      <div class=" space-y-5 md:space-y-8">
        <div class="space-y-3">
          <BackButton link="client.academicJournals.index" title="К списку журналов" />
          <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
            <BreadcrumbsItem :title="journal.data.title" :url="$page.url" />
          </BaseBreadcrumbs>

          <AcademicJournalsTitle class="text-center" :header="journal.data.title" />
        </div>

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
        <div>
          <div id="horizontal-alignment-1" role="tabpanel" aria-labelledby="horizontal-alignment-item-1">
            <Builder :blocks="journal.data.main_info" />
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
            <Builder :blocks="journal.data.for_authors" />
          </div>
          <div id="horizontal-alignment-4" class="hidden" role="tabpanel" aria-labelledby="horizontal-alignment-item-4">
            <!-- List -->
            <!-- Card Section -->
            <div class="px-0 py-5 sm:px-6 lg:px-8 lg:py-7 mx-auto">
              <!-- Grid -->
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
                            <h3 class="group-hover:text-primary-hover font-semibold text-gray-800">
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
              <!-- End Grid -->
            </div>
            <!-- End Card Section -->									<!-- End List -->
          </div>

        </div>
      </div>

    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>
</template>

<style>

.paragraph-container a {
	@apply text-primary;
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
