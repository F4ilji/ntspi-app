<template>
	<AppHead :seo="seo" />

	<div class="flex flex-col h-screen justify-between">
		<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>


		<div class="relative mx-auto mb-auto mt-[67px] max-w-screen-xl w-full px-4 py-10 md:flex md:flex-row md:py-10">
			<PageSubSectionLinks v-if="!settings.hide_page_sub_section_links" :sub-section-pages="subSectionPages" :current-section="page.data.section"/>
			<PageNavigateLinks v-if="!settings.hide_page_navigate_links"  :header-navs="headerNavs"/>
			<div class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
				<div class="space-y-5 md:space-y-5">
					<PageBreadcrumbs v-if="!settings.hide_breadcrumbs" :breadcrumbs="breadcrumbs" :page-title="page.data.title"/>
					<PageTitle :header="page.data.title"/>
					<div id="page-area">
						<PageBuilder :blocks="page.data.content"/>
					</div>
				</div>
			</div>
		</div>

		<ClientFooterDown/>
	</div>


</template>

<script>


import {Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue/v3";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import {Head} from '@inertiajs/vue3'
import PageBuilder from "@/Components/BuilderUi/Pages/PageBuilder.vue";
import PageBreadcrumbs from "@/Components/BuilderUi/Pages/PageBreadcrumbs.vue";
import PageTitle from "@/Components/BuilderUi/Pages/PageTitle.vue";
import PageNavigateLinks from "@/Components/BuilderUi/Pages/PageNavigateLinks.vue";
import PageSubSectionLinks from "@/Components/BuilderUi/Pages/PageSubSectionLinks.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import AppHead from "@/Components/AppHead.vue";


export default {
	name: "Page",
	data() {
		return {
			headerNavs: this.page.data.content.filter(block => block.type === 'heading').map(block => ({
				id: block.data.id,
				text: block.data.content
			})),
			settings: this.page.data.settings
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
		AppHead,
		MainPageNavBar,
		PageSubSectionLinks,
		PageNavigateLinks,
		PageTitle,
		PageBreadcrumbs,
		PageBuilder,
		ClientFooterDown,
		Link,
		FsLightbox,
		Head
	},
	methods: {},


	computed: {}
}
</script>

<style>


@keyframes fade {
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}

.fade-enter-active,
.fade-leave-active {
	transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
	opacity: 0;
}

@keyframes grow-progress {
	from {
		transform: scaleX(0);
	}
	to {
		transform: scaleX(1);
	}
}

#progress {
	height: 2px;
	background: #26ACB8;
	z-index: 10000;

	transform-origin: 0 50%;
	animation: grow-progress auto linear;
	animation-timeline: scroll();
}


.example-initial-animation {
	animation: initial-animation 2s ease;
}

@keyframes initial-animation {
	0% {
		transform: rotate(0deg);
	}

	50% {
		transform: rotate(360deg);
	}

	100% {
		transform: rotate(0deg);
	}
}

</style>
