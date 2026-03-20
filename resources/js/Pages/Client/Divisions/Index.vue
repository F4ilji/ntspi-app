<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import DivisionListTitle from "@/componentss/features/divisions/components/DivisionListTitle.vue";
import DivisionListBreadcrumbs from "@/componentss/features/divisions/components/DivisionListBreadcrumbs.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import FacultyListTitle from "@/componentss/features/faculties/components/FacultyListTitle.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";

export default {
  name: "Index",
  components: {
    BasicPageContainer, FacultyListTitle, BasicPageWrapper,
    BaseBreadcrumbs, BreadcrumbsItem,
    BasicFooter,
    DivisionListTitle,
    MetaTags,
		DivisionListBreadcrumbs,
		MainPageNavBar,
    Link,
    Head,
  },
  data() {
    return {

    };
  },
  props: {
		divisions: {
        type: Object,
    },
		seo: {
			type: Object,
		},
    breadcrumbs: {
      type: Object
    }
  },
};
</script>

<template>
	<MetaTags :seo="seo" />

	<MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <BasicPageContainer>
      <DivisionListTitle
          bottom-text="Список структурных и административных отделов НТГСПИ"
          header="Подразделения института" />
      <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
        <BreadcrumbsItem title="Подразделения института" :url="route('client.division.index')" />
      </BaseBreadcrumbs>

      <!-- Grid -->
      <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-6">
        <template v-for="division in divisions.data">
          <Link :href="route('client.division.show', division.slug)" class="group flex items-center bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition" >
            <div class="p-4 md:p-5 w-full">
              <div class="flex justify-between items-center gap-x-3">
                <div class="grow">
                  <h3 class="group-hover:text-primary-hover font-semibold text-gray-800">
                    {{ division.title }}
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
    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>


</template>

<style scoped></style>
