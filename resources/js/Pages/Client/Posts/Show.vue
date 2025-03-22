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


export default {
	name: "Show",
	components: {
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

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>
	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="max-w-3xl lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto w-full">
					<div>
						<div class="space-y-5 md:space-y-10">
							<div class="space-y-3">
								<PostItemBreadcrumbs :breadcrumbs="breadcrumbs" :post-title="post.data.title" />
								<BackButton link="client.post.index" title="Все новости"/>
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

					</div>
				</div>


			</div>
		</main>
		<BasicFooter />
	</div>
</template>

<style>


</style>