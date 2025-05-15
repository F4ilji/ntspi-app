<script>
import {Head, Link, WhenVisible} from "@inertiajs/vue3";
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
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";
export default {
  name: "Index",
  components: {
    BaseBreadcrumbs, BreadcrumbsItem,
    WhenVisible,
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
  props: {
    posts: {
        type: Object,
    },
    posts_pagination: {
      type: Object,
    },
    filters: {
        type: Object,
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

	<MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <BasicPageContainer>
      <div class="max-w-2xl text-center mx-auto mb-10 lg:mb-14">
        <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Новости НТГСПИ</h2>
        <p class="mt-1 text-gray-600 dark:text-neutral-400">Узнайте последние новости любимого вуза</p>
      </div>
      <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
        <BreadcrumbsItem title="Новости" :url="route('client.post.index')" />
      </BaseBreadcrumbs>
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
          <h3 class="text-sm text-gray-500 mb-4">Найдено новостей: {{ posts_pagination.total }}</h3>
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
                <div class="mt-5" v-if="posts_pagination.total === 0">
                  <div class="h-full flex flex-col items-center justify-center gap-1">
                    <p class="font-light">Извините, ничего не найдено</p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-700">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                  </div>
                </div>

                <div v-if="!(posts_pagination.current_page >= posts_pagination.last_page)">
                  <WhenVisible always :params="{
                  data: {
                    page: posts_pagination.current_page + 1
                  },
                  only: ['posts', 'posts_pagination']
                }"
                  />
                  <div class="text-center mt-10">
                    <div role="status">
                      <svg aria-hidden="true" class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                      </svg>
                      <span class="sr-only">Loading...</span>
                    </div>
                  </div>
                </div>


<!--                <BasicPagination :links="posts_pagination" />-->
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
