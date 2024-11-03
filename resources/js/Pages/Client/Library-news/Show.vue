<script>
import MainNavbar from "@/Navbars/MainNavbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue/v3";
import ClientScrollTimeline from "@/Components/ClientScrollTimeline.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import BaseBackButton from "@/Components/BaseComponents/BaseBuilderUi/BaseBackButton.vue";
import BaseTitle from "@/Components/BaseComponents/BaseBuilderUi/BaseTitle.vue";
import BaseBuilder from "@/Components/BaseComponents/BaseBuilderUi/BaseBuilder.vue";


export default {
	name: "Show",
	components: {
		BaseBuilder,
		BaseTitle, BaseBackButton,
		MainPageNavBar,
		ClientImageSlider, ClientFooterDown, ClientScrollTimeline, Link, MainNavbar, FsLightbox, Head},
	data() {
		return {
			blocks: this.post.data.content,

		}
	},

	props: {
		post: {
			type: Array,
		},
	},
	methods: {
		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText
				LimitedText = text.substring(0, symbols)
				return LimitedText + "..."
			}
			return text
		},

	},

}
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
				<div class="max-w-4xl px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto">
					<div>
						<div class="space-y-5 md:space-y-10">
							<div class="space-y-3">
								<BaseBackButton title="Назад" link="client.library.exhibition.index" />
								<BaseTitle :header="post.data.title" />
							</div>


							<div class="flex space-x-3 text-gray-500 ">
								<div class="flex items-center gap-3">
									<div>

										<div class="flex items-center space-x-2 text-sm">
											<p class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] text-blue-600">
												{{ post.data.category }}
											</p>
										</div>
									</div>
								</div>
							</div>

							<BaseBuilder :blocks="blocks" />
						</div>
						<!-- End Content -->
					</div>
				</div>

			</div>
		</main>
		<ClientFooterDown/>
	</div>
</template>

<style>


</style>