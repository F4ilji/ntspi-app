<script>
import {Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import AdditionalProgramTitle
  from "@/componentss/features/additionalEducationPrograms/components/AdditionalProgramTitle.vue";
import AdditionalProgramListBreadcrumbs
  from "@/componentss/features/additionalEducationPrograms/components/AdditionalProgramListBreadcrumbs.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import BasicListFilter from "@/componentss/shared/filter/BasicListFilter.vue";
import AdditionalProgramBadge
  from "@/componentss/features/additionalEducationPrograms/components/AdditionalProgramBadge.vue";
import LevelEduFilter from "@/componentss/features/additionalEducationPrograms/components/LevelEduFilter.vue";
import CategoryFilter from "@/componentss/shared/filter/filters/CategoryFilter.vue";
import BasicListBadge from "@/componentss/shared/badge/BasicListBadge.vue";
import FormEducationalFilter from "@/componentss/shared/filter/filters/FormEducationalFilter.vue";
import AdditionalProgramItemBreadcrumbs
  from "@/componentss/features/additionalEducationPrograms/components/AdditionalProgramItemBreadcrumbs.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import BackButton from "@/componentss/ui/buttons/BackButton.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import ProgramTitle from "@/componentss/features/educationalPrograms/components/ProgramTitle.vue";

export default {
  name: "Index",
  components: {
    ProgramTitle, Builder, BackButton, BasicPageWrapper, BasicPageContainer, AdditionalProgramItemBreadcrumbs,
    BasicListBadge,
    AdditionalProgramBadge,
    FormEducationalFilter, CategoryFilter,
    BasicListFilter,
    BasicFooter,
    AdditionalProgramListBreadcrumbs,
    AdditionalProgramTitle,
    MetaTags,
		LevelEduFilter,
		MainPageNavBar,
    Link,
  },
  data() {
    return {
			direction_id: this.filters.dir_id,
    };
  },
  props: {
		directionAdditionalEducations: {
        type: Object,
    },
		additionalEducations: {
			type: Object
		},
		filters: {
			type: Object
		},
		forms_education: {
			type: Object
		},
		categories: {
			type: Object
		},
		breadcrumbs: {
			type: Object
		},
		seo: {
			type: Object,
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
  },
  computed: {
    hasActiveFilters() {
      return this.filters.category_filter.value || this.filters.form_education_filter.value
    },
    totalPrograms() {
      return this.additionalEducations.data.reduce((total, item) => {
        return total + (item.additionalEducations?.length || 0);
      }, 0);
    },
  },
};
</script>

<template>

	<MetaTags :seo="seo" />

	<MainPageNavBar :sections="$page.props.navigation" />


  <BasicPageWrapper>
    <BasicPageContainer>
      <div class="space-y-3">
        <AdditionalProgramTitle class="text-center" header="Дополнительное образование" />
        <div class="space-y-3 mx-auto max-w-3xl w-full">
          <div class="my-10 w-full">
            <AdditionalProgramListBreadcrumbs :breadcrumbs="breadcrumbs" />
            <div class="flex items-center gap-x-2">
              <LevelEduFilter :directions="directionAdditionalEducations" :direction_filter="filters.direction_filter" />
              <BasicListFilter>
                <FormEducationalFilter :forms="forms_education" :formEdu_filter="filters.form_education_filter" />
                <CategoryFilter :categories="categories" :category_filter="filters.category_filter" />
              </BasicListFilter>
            </div>
          </div>
          <div class="px-1">
            <h3 class="text-sm text-gray-500 mb-4">Найдено программ: {{ totalPrograms }}</h3>
            <div v-if="hasActiveFilters" class="flex-wrap flex gap-3 md:items-center">
              <BasicListBadge :filters="filters" />
            </div>
          </div>
          <div class="">
            <div class="w-full mx-auto flex flex-wrap md:justify-between">
              <template v-for="educations in transformToColumns(additionalEducations.data)">
                <div class="flex flex-col">
                  <template v-for="education in educations">
                    <div style="height: max-content" class="px-1">
                      <h1 class="text-brand-primary mb-2 mt-2 text-lg font-semibold upper tracking-tight dark:text-white lg:text-md lg:leading-tight">
                        {{ education.title }}
                      </h1>
                      <template v-for="program in education.additionalEducations" :key="program.id">
                        <Link class="block text-primary hover:text-primary-hover duration-200 text-sm underline underline-offset-2 py-1" :href="route('client.additionalEducation.show', program.slug)">{{ program.title }}</Link>
                      </template>
                    </div>
                  </template>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>


</template>

<style scoped></style>
