<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import AcademicJournalsListBreadcrumbs
  from "@/componentss/features/academicJournals/components/AcademicJournalsListBreadcrumbs.vue.vue";
import LevelEduFilter from "@/componentss/features/additionalEducationPrograms/components/LevelEduFilter.vue";
import CategoryFilter from "@/componentss/shared/filter/filters/CategoryFilter.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import BasicListBadge from "@/componentss/shared/badge/BasicListBadge.vue";
import AdditionalProgramListBreadcrumbs
  from "@/componentss/features/additionalEducationPrograms/components/AdditionalProgramListBreadcrumbs.vue";
import BasicListFilter from "@/componentss/shared/filter/BasicListFilter.vue";
import AdditionalProgramTitle
  from "@/componentss/features/additionalEducationPrograms/components/AdditionalProgramTitle.vue";
import FormEducationalFilter from "@/componentss/shared/filter/filters/FormEducationalFilter.vue";

export default {
  name: "Index",
  components: {
    FormEducationalFilter,
    AdditionalProgramTitle,
    BasicListFilter,
    AdditionalProgramListBreadcrumbs,
    BasicListBadge,
    BasicPageWrapper,
    BasicPageContainer,
    CategoryFilter,
    LevelEduFilter,
    AcademicJournalsListBreadcrumbs,
    BasicFooter,
    MetaTags,
		MainPageNavBar,
    Link,
    Head,
  },
  data() {
    return {

    };
  },
  props: {
		journals: {
        type: Object,
    },
		seo: {
			type: Object,
		}
  },
  methods: {

  },

	mounted() {
  }
};
</script>

<template>
	<MetaTags :seo="seo" />


	<MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <BasicPageContainer>
      <div class="max-w-[85rem] px-4 py-5 sm:px-6 md:px-8 md:py-5 mx-auto">
        <div class="max-w-2xl text-center mx-auto mb-10 lg:mb-14">
          <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Научные периодические издания НТГСПИ</h2>
        </div>
        <!-- Grid -->
        <AcademicJournalsListBreadcrumbs />
        <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-6">
          <template v-for="journal in journals.data">
            <Link :href="route('client.academicJournals.show', journal.slug)" class="group flex items-center bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition" >
              <div class="p-4 md:p-5 w-full">
                <div class="flex justify-between items-center gap-x-3">
                  <div class="grow">
                    <h3 class="group-hover:text-primary-hover font-semibold text-gray-800">
                      {{ journal.title }}
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
    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>
</template>

<style scoped></style>
