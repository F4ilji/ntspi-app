<script>
import {Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import EducationForm from "../../../Enum/EducationForm.js";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import BackButton from "@/componentss/ui/buttons/BackButton.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import BasicTitle from "@/componentss/ui/titles/BasicTitle.vue";
import ProgramItemBreadcrumbs from "@/componentss/features/educationalPrograms/components/ProgramItemBreadcrumbs.vue";
import ProgramTitle from "@/componentss/features/educationalPrograms/components/ProgramTitle.vue";
import BudgetEducation from "@/Enum/BudgetEducation.js";
import TypeExam from "@/Enum/TypeExam.js";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";


export default {
  name: "Show",
  computed: {
    EducationForm() {
      return EducationForm
    },
    BudgetForm() {
      return BudgetEducation
    },
    TypeExam() {
      return TypeExam
    },
  },
  methods: {
    groupExamsByPriority(exams) {
      return exams.reduce((groups, exam) => {
        if (!groups[exam.priority]) {
          groups[exam.priority] = [];
        }
        groups[exam.priority].push(exam);
        return groups;
      }, {});
    },
    groupContestsByFormEducation(contests) {
      return contests.reduce((groups, contest) => {
        if (!groups[contest.form_education]) {
          groups[contest.form_education] = [];
        }
        groups[contest.form_education].push(contest);
        return groups;
      }, {});
    }
  },
  components: {
    BaseBreadcrumbs, BreadcrumbsItem,
    BasicPageContainer,
    BasicPageWrapper,
    ProgramTitle,
    BasicTitle,
    MetaTags,
    BackButton,
    BasicFooter,
    Builder,
		ProgramItemBreadcrumbs,
		MainPageNavBar,
		Link
  },
  props: {
    program: {
      type: Object,
    },
		formsEdu: {
			type: Object
		},
		seo: {
			type: Object,
		},
    breadcrumbs: {
      type: Object
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
        <BackButton link="client.program.index" title="Все программы" />
        <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
          <BreadcrumbsItem title="Образовательные программы" :url="route('client.program.index')" />
          <BreadcrumbsItem :title="program.data.name" :url="$page.url" />
        </BaseBreadcrumbs>
        <ProgramTitle class="text-center" :header="program.data.name" />
      </div>
      <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 items-start gap-12">
          <!-- Icon Block -->
          <div class="text-center">
            <div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
              </svg>
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
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
              </svg>
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
          <div class="text-center">
            <div class="flex justify-center items-center size-12 bg-gray-50 border border-gray-200 rounded-full mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
              </svg>
            </div>
            <div class="mt-3">
              <h3 class="text-lg font-semibold text-gray-800">Количество мест на прием</h3>
              <template v-if="Object.keys(program.data.admissionPlans).length !== 0" v-for="admissionPlan in program.data.admissionPlans">
                <template v-for="(contests, form) in groupContestsByFormEducation(admissionPlan.contests)">
                  <div class="border rounded my-2 py-1">
                    <span class="text-gray-500 text-[14px]">{{ EducationForm.fromValue(form).label }}</span>
                    <template v-for="contest in contests">
                      <p class="mt-1 text-gray-600"> {{ contest.places.count }} мест <span class="text-gray-400 text-[12px]">{{ BudgetForm.fromValue(contest.places.form_budget).label  }}</span></p>
                    </template>

                  </div>
                </template>
              </template>
              <template v-else>
                <span class="font-light text-sm mt-2">Прием по данной образовательной программе на текущий образовательный год не проводится</span>
              </template>
            </div>
          </div>
          <!-- End Icon Block -->
        </div>
      </div>
      <div>
        <div class="hs-accordion-group">
          <div v-if="Object.keys(program.data.admissionPlans).length !== 0" class="hs-accordion bg-white border -mt-px first:rounded-t-lg last:rounded-b-lg" id="hs-bordered-heading-one">
            <button class="hs-accordion-toggle hs-accordion-active:text-primary inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none" aria-controls="hs-basic-bordered-collapse-two">
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
                    <template v-for="(exams, priority) in groupExamsByPriority(admissionPlan.exams)">
                      <li>
                        <template v-for="(exam, index) in exams">
                          {{ exam.title }}
                          <span v-for="ex in exam.types" class="text-gray-400 text-[14px]">
            ({{ TypeExam.fromValue(ex.type).label }}, минимальный балл: {{ ex.min_ball }})
          </span>
                          <span class="text-gray-400 text-[14px] uppercase" v-if="index !== exams.length - 1"><br> или </span>
                        </template>
                      </li>
                    </template>
                  </template>
                </ol>
              </div>
            </div>
          </div>

          <div v-if="program.data.about_program" class="hs-accordion bg-white border -mt-px first:rounded-t-lg last:rounded-b-lg" id="hs-bordered-heading-two">
            <button class="hs-accordion-toggle hs-accordion-active:text-primary inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none" aria-controls="hs-basic-bordered-collapse-two">
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
                <Builder :blocks="program.data.about_program" />
              </div>
            </div>
          </div>

          <div v-if="program.data.program_features" class="hs-accordion bg-white border -mt-px first:rounded-t-lg last:rounded-b-lg" id="hs-bordered-heading-three">
            <button class="hs-accordion-toggle hs-accordion-active:text-primary inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none" aria-controls="hs-basic-bordered-collapse-three">
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
                <Builder :blocks="program.data.program_features" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>


</template>

<style>

</style>