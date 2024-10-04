<script>
import MainNavbar from "@/Navbars/MainNavbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue/v3";
import ClientScrollTimeline from "@/Components/ClientScrollTimeline.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import PostTitle from "@/Components/BuilderUi/Posts/PostTitle.vue";
import PostBackButton from "@/Components/BuilderUi/Posts/PostBackButton.vue";
import PostAuthorsList from "@/Components/BuilderUi/Posts/PostAuthorsList.vue";
import PostTimeRead from "@/Components/BuilderUi/Posts/PostTimeRead.vue";
import PostBuilder from "@/Components/BuilderUi/Posts/PostBuilder.vue";
import PostGallery from "@/Components/BuilderUi/Posts/PostGallery.vue";
import PostBadge from "@/Components/BuilderUi/Events/EventBadgeBuilder.vue";
import ClientPostFilter from "@/Components/ClientPostFilter.vue";
import ClientPostSearch from "@/Components/ClientPostSearch.vue";
import ClientPost from "@/Components/ClientPost.vue";
import AdminIndexHeader from "@/Components/AdminIndexHeader.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";

export default {
	name: "Show",
	components: {
		MainPageNavBar,
		AdminIndexHeader, ClientPost, ClientPostSearch, ClientPostFilter, PostBadge,
		PostGallery,
		PostBuilder,
		PostTimeRead,
		PostAuthorsList,
		PostBackButton,
		PostTitle,
		ClientImageSlider,
		ClientFooterDown,
		ClientScrollTimeline,
		Link,
		MainNavbar,
		FsLightbox,
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
		}
	},
	mounted() {
	},
	methods: {
	}}
</script>
<template>
	<Head>
		<title>{{ post.data.title }}</title>
		<meta name="description" content="Your page description">
	</Head>
	<ClientScrollTimeline/>

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>
	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="max-w-3xl lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto">
					<div>

						<div class="space-y-5 md:space-y-10">
							<div class="space-y-3">
								<PostBackButton :title="'Назад'"/>
								<PostTitle :header="post.data.title"/>
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

							<div class="space-y-3 md:space-y-4">
								<PostBuilder :blocks="this.blocks"/>
							</div>


							<div>
								<PostGallery :title="post.data.title" :images="post.data.gallery"/>

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
		<ClientFooterDown/>
	</div>
</template>

<style>


</style>