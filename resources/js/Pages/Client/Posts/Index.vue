<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import PostListBreadcrumbs from "@/componentss/features/posts/components/PostListBreadcrumbs.vue.vue";
import HeadItemsWrapper from "@/componentss/ui/wrappers/HeadItemsWrapper.vue";
import PostListSearch from "@/componentss/features/posts/components/PostListSearch.vue";
import PostListItem from "@/componentss/features/posts/components/PostListItem.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import PostListBadge from "@/componentss/features/posts/components/PostListBadge.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import BasicListFilter from "@/componentss/shared/filter/BasicListFilter.vue";
import SortingByFilter from "@/componentss/shared/filter/filters/SortingByFilter.vue";
import CategoryFilter from "@/componentss/shared/filter/filters/CategoryFilter.vue";
import TagFilter from "@/componentss/shared/filter/filters/TagFilter.vue";
import BasicListBadge from "@/componentss/shared/badge/BasicListBadge.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import EventArchiveListBreadcrumbs from "@/componentss/features/events/components/EventArchiveListBreadcrumbs.vue";
import BasicPagination from "@/componentss/shared/paginate/BasicPagination.vue";
import IsOnlineFilter from "@/componentss/shared/filter/filters/IsOnlineFilter.vue";
import EventListSearch from "@/componentss/features/events/components/EventListSearch.vue";
export default {
  name: "Index",
  components: {
    EventListSearch, IsOnlineFilter, BasicPagination, EventArchiveListBreadcrumbs, BasicPageWrapper, BasicPageContainer,
    BasicListBadge,
    TagFilter,
    CategoryFilter,
    SortingByFilter,
    BasicListFilter,
    MetaTags,
    PostListBadge,
    BasicFooter,
    PostListItem,
    PostListSearch,
    HeadItemsWrapper,
    PostListBreadcrumbs,
		MainPageNavBar,
    Link,
    Head,
  },
  data() {
    return {

    };
  },
  props: {
    posts: {
        type: Object,
    },
    filters: {
        type: Array,
    },
    categories: {
        type: Object,
    },
		tags: {
			type: Object,
		},
		navigation: {
			type: Object,
		},
		breadcrumbs: {
			type: Object,
		},
		seo: {
			type: Object,
		}
  },
  computed: {
    hasActiveFilters() {
      return this.filters.category_filter.value || this.filters.tag_filter.value || this.filters.search_filter.value;
    }
  },

};
</script>

<template>
	<MetaTags :seo="seo" />

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

  <BasicPageWrapper>
    <BasicPageContainer>
      <div class="max-w-2xl text-center mx-auto mb-10 lg:mb-14">
        <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Новости НТГСПИ</h2>
        <p class="mt-1 text-gray-600 dark:text-neutral-400">Узнайте последние новости любимого вуза</p>
      </div>
      <PostListBreadcrumbs :breadcrumbs="breadcrumbs" />
      <div>
        <HeadItemsWrapper>
          <PostListSearch :search_filter="filters.search_filter" />
          <BasicListFilter>
            <SortingByFilter :sortingBy_filter="filters.sortingBy_filter" />
            <CategoryFilter :categories="categories" :category_filter="filters.category_filter" />
            <TagFilter :tags="tags" :tag_filter="filters.tag_filter" />
          </BasicListFilter>
        </HeadItemsWrapper>


        <div>
          <h3 class="text-sm text-gray-500 mb-4">Найдено новостей: {{ posts.meta.total }}</h3>
          <div v-if="hasActiveFilters" class="flex-wrap flex gap-3 md:items-center">
            <BasicListBadge :filters="filters" />
          </div>
        </div>

        <div class="space-y-5 md:space-y-4">
          <div class="space-y-5 md:space-y-4">
            <div>
              <div class="container mx-auto max-w-screen-lg py-5 lg:py-8">
                <div class="grid gap-10 md:grid-cols-2 lg:gap-10 xl:grid-cols-3">
                  <template v-for="post in posts.data" :key="post.id">
                    <PostListItem :post="post" />
                  </template>
                </div>
                <BasicPagination :links="posts.meta" />
              </div>
            </div>
          </div>
        </div>
        <!-- End Content -->
      </div>
    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>
</template>

<style scoped></style>
