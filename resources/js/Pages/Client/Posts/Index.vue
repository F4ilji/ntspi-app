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
export default {
  name: "Index",
  components: {
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
	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl py-10 md:flex md:flex-row md:py-10">
				<div class="pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto w-full max-w-screen-lg">
					<div class="max-w-2xl text-center mx-auto mb-10 lg:mb-14">
						<h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Новости НТГСПИ</h2>
						<p class="mt-1 text-gray-600 dark:text-neutral-400">Узнайте последние новости любимого вуза</p>
					</div>
					<PostListBreadcrumbs :breadcrumbs="breadcrumbs" class="px-5" />
					<div>
						<HeadItemsWrapper>
							<PostListSearch :search_filter="filters.search_filter" />
              <BasicListFilter>
                <SortingByFilter :sortingBy_filter="filters.sortingBy_filter" />
                <CategoryFilter :categories="categories" :category_filter="filters.category_filter" />
                <TagFilter :tags="tags" :tag_filter="filters.tag_filter" />
              </BasicListFilter>
						</HeadItemsWrapper>


						<div v-if="hasActiveFilters" class="px-6">
							<h3 class="text-sm text-gray-500 mb-4">Найдено новостей: {{ posts.meta.total }}</h3>
							<div class="flex-wrap flex gap-3 md:items-center">
								<BasicListBadge :filters="filters" />
							</div>
						</div>

						<div class="space-y-5 md:space-y-4">
							<div class="space-y-5 md:space-y-4">
								<div>
									<div class="container px-4 mx-auto xl:px-5 max-w-screen-lg py-5 lg:py-8">
										<div class="mt-10 grid gap-10 md:grid-cols-2 lg:gap-10 xl:grid-cols-3">
											<template v-for="post in posts.data" :key="post.id">
												<PostListItem :post="post" />
											</template>
										</div>
										<div class="mt-10 flex items-center justify-center">
											<nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
												<Link as="button" :href="posts.links.prev" :disabled="$props.posts.links.prev === null"
															class="relative inline-flex items-center gap-1 rounded-l-md border border-gray-300 bg-white px-3 py-2 pr-4 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 disabled:pointer-events-none disabled:opacity-40 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300">
													<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
															 stroke="currentColor" aria-hidden="true" data-slot="icon" class="h-3 w-3">
														<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
													</svg>
													<span>Предыдущая</span></Link>
												<Link as="button" :href="posts.links.next" :disabled="$props.posts.links.next === null"
															class="relative inline-flex items-center gap-1 rounded-r-md border border-gray-300 bg-white px-3 py-2 pl-4 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 disabled:pointer-events-none disabled:opacity-40 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300">
													<span>Следующая</span>
													<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
															 stroke="currentColor" aria-hidden="true" data-slot="icon" class="h-3 w-3">
														<path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
													</svg>
												</Link>
											</nav>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- End Content -->
					</div>
				</div>
			</div>
		</main>
		<BasicFooter />
	</div>
</template>

<style scoped></style>
