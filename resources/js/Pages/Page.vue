<template>
	<MetaTags :seo="seo" />


  <MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <div class="relative mx-auto mb-auto mt-[67px] max-w-screen-xl w-full px-4 py-10 md:flex md:flex-row md:py-10">
      <PageSubSectionLinks v-if="!settings?.hide_page_sub_section_links" :sub-section-pages="subSectionPages" :current-section="page.data.section"/>
      <NavigateLinks v-if="!settings?.hide_page_navigate_links"  :header-navs="headerNavs"/>
      <div class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
        <div class="space-y-2 md:space-y-5">
          <BaseBreadcrumbs v-if="!settings?.hide_breadcrumbs" :breadcrumbs="breadcrumbs">
            <BreadcrumbsItem :title="breadcrumbs.page.data.title" :url="route('page.view', breadcrumbs.page.data.path)" />
          </BaseBreadcrumbs>
          <BasicTitle :header="page.data.title"/>
          <div id="page-area">
            <Builder :blocks="page.data.content"/>
          </div>
        </div>
      </div>
    </div>

    <BasicFooter />
  </BasicPageWrapper>



</template>

<script>


import {Link, Head} from "@inertiajs/vue3";
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


export default {
	name: "Page",
  data() {
    return {
      headerNavs: (this.page?.data?.content || []).filter(block => block?.type === 'heading').map(block => ({
        id: block?.data?.id,
        text: block?.data?.content
      })),
      settings: this.page?.data?.settings || {}
    }
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
		}
	},
	components: {
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
		Head
	},
	methods: {},


	computed: {}
}
</script>

<style>


</style>
