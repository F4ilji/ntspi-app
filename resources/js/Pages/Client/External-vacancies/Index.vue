<script>
import MainNavbar from "@/Navbars/MainNavbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue/v3";
import ClientScrollTimeline from "@/Components/ClientScrollTimeline.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import AdminIndexSearch from "@/Components/AdminIndexSearch.vue";
import AdminIndexFilter from "@/Components/AdminIndexFilter.vue";
import AdminIndexHeader from "@/Components/AdminIndexHeader.vue";
import AdminIndexHeaderTitle from "@/Components/AdminIndexHeaderTitle.vue";
import ClientPostFilter from '@/Components/ClientPostFilter.vue';
import ClientPost from '@/Components/ClientPost.vue';
import ClientPostSearch from '@/Components/ClientPostSearch.vue';
import TitleEvent from "@/Components/BuilderUi/Events/TitleEvent.vue";
import EventBackButton from "@/Components/BuilderUi/Events/EventBackButton.vue";
import EventBuilder from "@/Components/BuilderUi/Events/EventBuilder.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";

export default {
  name: "Index",
  components: {
		MainPageNavBar,
		EventBuilder, EventBackButton, TitleEvent,
    AdminIndexHeaderTitle, AdminIndexHeader,
    AdminIndexFilter, AdminIndexSearch,
    ClientFooterDown,
    ClientScrollTimeline,
    ClientPostFilter,
    Link,
    MainNavbar,
    FsLightbox,
    Head,
    ClientPost,
    ClientPostSearch
  },
  data() {
    return {

    };
  },
  props: {
    posts: {
        type: Array,
    },
    filters: {
        type: Array,
    },
    categories: {
        type: Array,
    },
		navigation: {
			type: Array,
		},
  },
  methods: {

  },

	mounted() {
  }
};
</script>

<template>
  <Head>
    <title>Новости</title>
    <meta name="description" content="Your page description"/>
  </Head>
	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>
	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto">
					<div>
						<AdminIndexHeader>
							<ClientPostSearch />
							<ClientPostFilter :items="categories"/>
						</AdminIndexHeader>
						<!-- Avatar Media -->
						<!-- End Avatar Media -->
						<!-- Content -->
						<div class="space-y-5 md:space-y-4">
							<div class="space-y-5 md:space-y-4">
								<div>
									<div class="container px-8 mx-auto xl:px-5 max-w-screen-lg py-5 lg:py-8">
										<div class="mt-10 grid gap-10 md:grid-cols-2 lg:gap-10 xl:grid-cols-3">
											<template v-for="post in posts.data" :key="post.id">
												<ClientPost :post="post" />
											</template>
										</div>
										<div class="mt-10 flex items-center justify-center">
											<nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
												<Link as="button" :href="$props.posts.links.prev" :disabled="$props.posts.links.prev === null"
															class="relative inline-flex items-center gap-1 rounded-l-md border border-gray-300 bg-white px-3 py-2 pr-4 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 disabled:pointer-events-none disabled:opacity-40 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300">
													<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
															 stroke="currentColor" aria-hidden="true" data-slot="icon" class="h-3 w-3">
														<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
													</svg>
													<span>Предыдущая</span></Link>
												<Link as="button" :href="$props.posts.links.next" :disabled="$props.posts.links.next === null"
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
		<ClientFooterDown/>
	</div>
</template>

<style scoped></style>
