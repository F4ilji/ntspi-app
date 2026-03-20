<script>
import {Head, Link} from "@inertiajs/vue3";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import FacultyListBreadcrumbs from "@/componentss/features/faculties/components/FacultyListBreadcrumbs.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import BasicTitle from "@/componentss/ui/titles/BasicTitle.vue";
import FacultyListTitle from "@/componentss/features/faculties/components/FacultyListTitle.vue";
import CategoryFilter from "@/componentss/shared/filter/filters/CategoryFilter.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import BasicListBadge from "@/componentss/shared/badge/BasicListBadge.vue";
import EventArchiveListBreadcrumbs from "@/componentss/features/events/components/EventArchiveListBreadcrumbs.vue";
import BasicPagination from "@/componentss/shared/paginate/BasicPagination.vue";
import IsOnlineFilter from "@/componentss/shared/filter/filters/IsOnlineFilter.vue";
import BasicListFilter from "@/componentss/shared/filter/BasicListFilter.vue";
import EventListSearch from "@/componentss/features/events/components/EventListSearch.vue";
import SortingByFilter from "@/componentss/shared/filter/filters/SortingByFilter.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";

export default {
  name: "Index",
  components: {
    BaseBreadcrumbs, BreadcrumbsItem,
    SortingByFilter,
    EventListSearch,
    BasicListFilter,
    IsOnlineFilter,
    BasicPagination,
    EventArchiveListBreadcrumbs,
    BasicListBadge,
    BasicPageWrapper,
    BasicPageContainer,
    CategoryFilter,
    FacultyListTitle,
    BasicTitle,
    BasicFooter,
    FacultyListBreadcrumbs,
    MainPageNavBar,
    MetaTags,
    Link
  },
  data() {
    return {

    };
  },
  props: {
		faculties: {
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
      <FacultyListTitle
          bottom-text="Информация о наших факультетах и кафедрах"
          header="Факультеты и кафедры" />
      <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
        <BreadcrumbsItem title="Факультеты" :url="route('client.faculty.index')" />
      </BaseBreadcrumbs>

      <!-- Grid -->
      <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-6">
        <template v-for="faculty in faculties.data">
          <Link :href="route('client.faculty.show', faculty.slug)" class="group flex flex-col bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition" >
            <div class="p-4 md:p-5">
              <div class="flex justify-between items-center gap-x-3">
                <div class="grow">
                  <h3 class="group-hover:text-primary-hover font-semibold text-gray-800">
                    {{ faculty.shortTitle }}
                  </h3>
                  <p class="text-sm text-gray-500">
                    {{ faculty.title }}
                  </p>
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
