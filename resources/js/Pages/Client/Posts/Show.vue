<script>
import {Head, Link} from "@inertiajs/vue3";
import BasicTitle from "@/componentss/ui/titles/BasicTitle.vue";
import BackButton from "@/componentss/ui/buttons/BackButton.vue";
import PostItemBreadcrumbs from "@/componentss/features/posts/components/PostItemBreadcrumbs.vue";
import PostGallery from "@/componentss/features/posts/components/PostGallery.vue";
import PostTimeRead from "@/componentss/features/posts/components/PostTimeRead.vue";
import PostAuthorsList from "@/componentss/features/posts/components/PostAuthorsList.vue";
import ScrollTimeline from "@/componentss/shared/timeline/ScrollTimeline.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
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


export default {
	name: "Show",
	components: {
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
    MetaTags,
    MainPageNavBar,
    BasicFooter,
    Builder,
    ScrollTimeline,
    BasicTitle,
    BackButton,
    PostItemBreadcrumbs,
		PostGallery,
		PostTimeRead,
		PostAuthorsList,
		Link,
		Head
	},
	data() {
		return {
			blocks: this.post.data.content,
		}
	},
	props: {
		post: {
			type: Object,
		},
		navigation: {
			type: Object,
		},
		searchMatch: {
			type: String,
			default: ''
		},
		breadcrumbs: {
			type: Object,
		},
		seo: {
			type: Object,
		}
	},
	mounted() {
	},
	methods: {
	}}
</script>
<template>

	<MetaTags :seo="seo" />

	<ScrollTimeline />

	<MainPageNavBar class="border-b" :sections="$page.props.navigation" />
  <BasicPageWrapper>
    <BasicPageContainer breakpoint="md">
      <div class="space-y-5 md:space-y-10">
        <div class="space-y-3">
          <BackButton link="client.post.index" title="Все новости"/>
          <PostItemBreadcrumbs :breadcrumbs="breadcrumbs" :post-title="post.data.title" />
          <BasicTitle :header="post.data.title"/>
        </div>

        <div class="flex space-x-3 text-gray-500 ">
          <div class="flex items-center gap-3">
            <div class="md:flex md:items-center space-y-2 md:space-y-0 md:space-x-4 text-sm">
              <PostAuthorsList :authors="post.data.authors"/>
              <time class="block text-gray-500">Опубликовано {{ post.data.created_post }}
              </time>
              <PostTimeRead :time="post.data.reading_time"/>
            </div>
          </div>
        </div>
        <Builder :blocks="blocks"/>


        <div>
          <PostGallery v-if="post.data.gallery.length > 1" :title="post.data.title" :images="post.data.gallery"/>
        </div>
        <div>
          <a :href="route('client.post.index', { 'tag[]': tag.slug })" v-for="tag in post.data.tags" class="m-1 inline-flex items-center gap-1.5 py-2 px-3 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" href="#">
            {{ tag.name }}
          </a>
        </div>
      </div>
    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>
</template>

<style>


</style>