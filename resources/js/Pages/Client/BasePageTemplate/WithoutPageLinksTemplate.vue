<template>
	<Head>
		<title>{{ page.data.title }}</title>
		<meta name="description" content="Your page description">
	</Head>


	<div class="flex flex-col h-screen">
		<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

		<main class="flex-grow">
			<div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">

				<PageNavigateLinks :header-navs="this.headerNavs"/>
				<article class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
					<div class="space-y-5 md:space-y-5">
						<PageBreadcrumbs :breadcrumbs="breadcrumbs" :page-title="page.data.title"/>
						<PageTitle :header="page.data.title"/>
						<div id="page-area" class="space-y-4">
							<PageBuilder :blocks="this.page.data.content"/>

						</div>
					</div>


				</article>
			</div>
		</main>
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


export default {
	name: "Page",
	data() {
		return {
			headerNavs: this.page.data.content.filter(block => block.type === 'heading').map(block => ({
				id: block.data.id,
				text: block.data.content
			}))
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
	},
	components: {
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
