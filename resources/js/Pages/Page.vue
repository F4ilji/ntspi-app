<template>
  <MetaTags :seo="seo" />

  <MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <div
      class="relative mx-auto mb-auto mt-[67px] max-w-screen-xl w-full px-4 py-10 md:flex md:flex-row md:py-10"
    >
      <PageSubSectionLinks
        v-if="!settings?.hide_page_sub_section_links"
        :sub-section-pages="subSectionPages"
        :current-section="page.data.section"
      />
      <NavigateLinks
        v-if="!settings?.hide_page_navigate_links"
        :header-navs="headerNavs"
      />
      <div class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
        <div class="space-y-2 md:space-y-5">
          <BaseBreadcrumbs
            v-if="!settings?.hide_breadcrumbs"
            :breadcrumbs="breadcrumbs"
          >
            <BreadcrumbsItem
              :title="breadcrumbs.page.data.title"
              :url="route('page.view', breadcrumbs.page.data.path)"
            />
          </BaseBreadcrumbs>
          <BasicTitle :header="page.data.title" />
          <div id="page-area">
            <Builder :blocks="page.data.content" />
          </div>
        </div>
      </div>
    </div>

    <BasicFooter />
  </BasicPageWrapper>

  <div v-if="settings?.form.id" class="fixed bottom-0 end-0 z-60 sm:max-w-xl w-full mx-auto p-6">
    <div class="hs-accordion-group">
      <div class="hs-accordion border-gray-200 active bg-white border rounded-xl" id="hs-active-bordered-heading-two">
        <button class="hs-accordion-toggle hs-accordion-active:text-blue-600 inline-flex justify-between items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-8 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none rounded-xl" aria-expanded="true" aria-controls="hs-basic-active-bordered-collapse-two">
          {{ settings?.form.title }}
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
                  {{ settings?.form.description }}
                </p>
                <div class="mt-5 inline-flex gap-x-2">
                  <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-vertically-centered-scrollable-modal" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
                    {{ settings?.form.button }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div v-if="settings?.form.id" id="hs-vertically-centered-scrollable-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[1000000] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="hs-vertically-centered-scrollable-modal-label">
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
          <Form :form-id="settings?.form.id" />
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import { Link, Head } from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import BasicTitle from "@/componentss/ui/titles/BasicTitle.vue";
import PageSubSectionLinks from "@/componentss/features/pages/components/PageSubSectionLinks.vue";
import PageBreadcrumbs from "@/componentss/features/pages/components/PageBreadcrumbs.vue";
import NavigateLinks from "@/componentss/shared/navigate/NavigateLinks.vue";
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
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import FormBlock from "@/componentss/shared/builder/pageBuilder/blocks/FormBlock.vue";
import Form from "@/componentss/features/forms/Form.vue";

export default {
  name: "Page",
  data() {
    return {
      headerNavs: (this.page?.data?.content || [])
        .filter((block) => block?.type === "heading")
        .map((block) => ({
          id: block?.data?.id,
          text: block?.data?.content,
        })),
      settings: this.page?.data?.settings || {},
    };
  },
  props: {
    navigation: {
      type: Object,
    },
    page: {
      type: Object,
    },
    subSectionPages: {
      type: Object,
    },
    breadcrumbs: {
      type: Object,
    },
    seo: {
      type: Object,
    },
  },
  components: {
    Form,
    BreadcrumbsItem,
    BasicIcon,
    BaseBreadcrumbs,
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
    NavigateLinks,
    BasicTitle,
    Builder,
    BasicFooter,
    MetaTags,
    MainPageNavBar,
    PageSubSectionLinks,
    PageBreadcrumbs,
    Link,
    Head,
    FormBlock,
  },
  methods: {

  },

  computed: {},
};
</script>

<style></style>
