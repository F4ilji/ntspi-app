<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import FormEducationalFilter from "@/componentss/shared/filter/filters/FormEducationalFilter.vue";
import DirectionFilter from "@/componentss/shared/filter/filters/DirectionFilter.vue";
import BudgetFilter from "@/componentss/shared/filter/filters/BudgetFilter.vue";
import BasicListFilter from "@/componentss/shared/filter/BasicListFilter.vue";
import ProgramTitle from "@/componentss/features/educationalPrograms/components/ProgramTitle.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import ProgramListBreadcrumbs from "@/componentss/features/educationalPrograms/components/ProgramListBreadcrumbs.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import ProgramLevelEduFilter from "@/componentss/features/educationalPrograms/components/ProgramLevelEduFilter.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import AcademicJournalsListBreadcrumbs
  from "@/componentss/features/academicJournals/components/AcademicJournalsListBreadcrumbs.vue.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import BasicListBadge from "@/componentss/shared/badge/BasicListBadge.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";

export default {
  name: "Index",
  components: {
    BaseBreadcrumbs, BreadcrumbsItem,
    BasicListBadge,
    BasicPageWrapper, AcademicJournalsListBreadcrumbs, BasicPageContainer,
    ProgramLevelEduFilter,
    FormEducationalFilter,
    DirectionFilter,
    BudgetFilter,
    BasicListFilter,
    ProgramTitle,
    MetaTags,
    BasicFooter,
		ProgramListBreadcrumbs,
		MainPageNavBar,
    Link,
    Head,
  },
  computed: {
    hasActiveFilters() {
      return this.filters.budget_filter.value || this.filters.direction_filter.value || this.filters.formEdu_filter.value;
    }
  },
  props: {
		campaignName: {
			type: String,
    },
		levelsEducational: {
			type: Object,
		},
		naprs: {
			type: Object,
		},
		filters: {
			type: Object,
		},
		formsEdu: {
			type: Object,
		},
		budgetEdu: {
			type: Object,
		},
		direction_studies: {
			type: Object,
		},
		seo: {
			type: Object,
		},
    breadcrumbs: {
      type: Object
    }
  },
  methods: {
		transformToColumns(originalArray) {
			const chunkArray = (arr, size) => {
				return arr.reduce((acc, _, i) => (i % size ? acc : [...acc, arr.slice(i, i + size)]), []);
			};

			const dividedArrays = chunkArray(originalArray, Math.ceil(originalArray.length / 2));

			return dividedArrays.reverse();
		},
		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText
				LimitedText = text.substring(0, symbols)
				return LimitedText + "..."
			}
			return text
		},

  },
};
</script>

<template>
	<MetaTags :seo="seo" />

	<MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <BasicPageContainer>
      <div class="space-y-5 md:space-y-4">
        <ProgramTitle class="text-center" :header="this.campaignName" />
        <div class="space-y-5 md:space-y-4 mx-auto max-w-3xl w-full">
          <div>
            <div class="">
              <div class="mt-10 mb-5 w-full">
                <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
                  <BreadcrumbsItem title="Образовательные программы" :url="route('client.program.index')" />
                </BaseBreadcrumbs>
                <div class="flex items-center gap-x-2">
                  <ProgramLevelEduFilter :levels="levelsEducational" :level_filter="filters.level_filter" />
                  <BasicListFilter>
                    <BudgetFilter :budget_filter="filters.budget_filter" :budgets="budgetEdu" />
                    <DirectionFilter :direction_studies="direction_studies" :direction_filter="filters.direction_filter" />
                    <FormEducationalFilter :formEdu_filter="filters.formEdu_filter" :forms="formsEdu" />
                  </BasicListFilter>
                </div>
              </div>
            </div>

            <div class="mb-5">
<!--              <h3 class="text-sm text-gray-500 mb-4">Найдено программ: {{ naprs.meta.total }}</h3>-->
              <div v-if="hasActiveFilters" class="flex-wrap flex gap-3 md:items-center">
                <BasicListBadge :filters="filters" />
              </div>
            </div>

            <div class="container">
              <div class="w-full mx-auto flex flex-wrap lg:justify-between">
                <template v-for="naprs in transformToColumns(this.naprs.data)">
                  <div class="flex flex-col">
                    <template v-for="napr in naprs">
                      <div style="height: max-content" class="px-4">
                        <h1 class="text-brand-primary mb-2 mt-2 text-lg font-semibold upper tracking-tight dark:text-white lg:text-md lg:leading-tight">
                          {{ napr.name }}
                        </h1>
                        <template v-for="program in napr.programs" :key="program.id">
                          <Link
                              class="block text-primary hover:text-primary-hover duration-200 text-sm underline underline-offset-2 py-1"
                              :href="route('client.program.show', program.slug)">{{ program.name }}
                          </Link>
                        </template>
                      </div>
                    </template>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>


</template>

<style scoped></style>
