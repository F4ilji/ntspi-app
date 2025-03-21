<template>
	<AppHead :seo="seo" />


	<div class="flex flex-col h-screen justify-between">
		<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

		<div class="relative mx-auto mb-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">

			<div class="w-full h-[67px] pointer-events-none fixed" id="visor"></div>


			<div class="sticky top-[110px] hidden h-[calc(100vh-110px)] max-w-[20%] md:flex md:shrink-0 md:flex-col md:justify-between">
				<nav v-if="faculties"
						 class="flex h-[calc(100vh-200px)] flex-col overflow-hidden pr-2 pb-4">
					<div class="text-gray-1000 mb-2 text-md font-medium">Факультеты</div>
					<div class="flex gap-x-1">
						<ul class="px-0.5 last-of-type:mb-0 mb-8">
							<li v-for="faculty in faculties.data" :key="faculty.id" class="my-1.5 flex">
								<a :class="{'text-white hover:text-gray-200 font-semibold bg-[#135aae]': isSameRoute(route('client.faculty.show', faculty.slug)), 'text-gray-600 hover:text-[#2C6288]': !isSameRoute(route('client.faculty.show', faculty.id)) }"
									 :href="route('client.faculty.show', faculty.slug) + '/'"
									 class="relative duration-300 flex w-full rounded-md cursor-pointer items-center px-2 py-1 text-left text-sm">{{
										faculty.title
									}}</a>
							</li>
						</ul>
					</div>

				</nav>
			</div>

			<nav class="order-last hidden w-56 shrink-0 lg:block">
				<div class="sticky top-[110px] h-[calc(100vh-110px)]">
					<div class="text-gray-1000 mb-2 text-md font-medium">На этой странице</div>
					<ul class="max-h-[70vh] space-y-1.5 overflow-hidden py-2 text-sm">
						<li class="anchor-li">
							<a :class="{ 'translate-x-2 text-[#135aae]' : currentNavSection  === 'persons', 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== 'persons' }"
								 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
								 href="#persons">Состав</a>
						</li>
						<li class="anchor-li">
							<a :class="{ 'translate-x-2 text-[#135aae]' : currentNavSection  === 'department', 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== 'department' }"
								 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
								 href="#department">Кафедры факультета</a>
						</li>
						<li class="anchor-li">
							<a :class="{ 'translate-x-2 text-[#135aae]' : currentNavSection  === 'description', 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== 'description' }"
								 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
								 href="#description">Описание</a>
						</li>

						<transition name="fade">
							<li class="anchor-li flex items-center py-2 border-t" v-if="scrollTop" @click.prevent="scrollToTop">
								<button class="bg-transperant text-gray-600 cursor-pointer hover:text-gray-900 duration-300 block px-2 leading-[1.6] rounded-md">К началу</button>
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[17px] text-gray-600">
									<path stroke-linecap="round" stroke-linejoin="round" d="M15 11.25l-3-3m0 0l-3 3m3-3v7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</li>
						</transition>
					</ul>
				</div>
			</nav>

			<article class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
				<div class="space-y-5 md:space-y-5">
					<div class="flex justify-between items-center mb-6">
						<div class="flex w-full sm:items-center gap-x-5 sm:gap-x-3">
							<div class="grow">
								<div class="grid sm:flex sm:justify-between sm:items-center gap-2">
									<FacultyItemBreadcrumbs :faculty-title="faculty.data.title" />
								</div>
							</div>
						</div>
					</div>

					<div class="space-y-3">
						<h1 class="text-2xl mb-10 font-bold md:text-3xl">{{ faculty.data.title }}</h1>
					</div>

					<div id="page-area" class="space-y-5 md:space-y-5">

						<h2 id="persons" class="font-bold text-xl">Состав</h2>


						<template v-for="worker in faculty.data.workers">
							<ClientFacultyWorkerCard :worker="worker" />
						</template>


						<!-- End FAQ -->



						<h2 id="department" class="font-bold text-xl">Кафедры факультета</h2>

						<!-- Card Section -->
						<!-- Grid -->
						<div class="grid sm:grid-cols-2 md:grid-cols-2 xl:grid-cols-2 gap-3 sm:gap-6">
							<template v-for="department in faculty.data.departments">

								<Link :href="route('client.department.show', { facultySlug: faculty.data.slug, departmentSlug: department.slug })" class="flex justify-center items-center bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition">
									<div class="p-4 md:p-5">
										<div class="flex justify-between items-center gap-x-3">
											<div class="grow">
												<h3 class="group-hover:text-blue-600 font-semibold text-gray-800">
													{{ department.title }}
												</h3>
											</div>
											<div>
												<svg class="shrink-0 size-5 text-gray-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
											</div>
										</div>
									</div>
								</Link>
							</template>

						</div>

						<h2 id="description" class="font-bold text-xl">Описание</h2>


						<div class="space-y-3 md:space-y-4">
							<FacultyBuilder :blocks="faculty.data.content "/>
						</div>

					</div>


				</div>
			</article>
		</div>

		<ClientFooterDown/>
	</div>



</template>

<script>


import {Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue/v3";
import MainNavbar from "@/Navbars/MainNavbar.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import { Head } from '@inertiajs/vue3'
import slugify from "slugify";
import axios from "axios";
import PageNavigateLinks from "@/Components/BuilderUi/Pages/PageNavigateLinks.vue";
import PageBreadcrumbs from "@/Components/BuilderUi/Pages/PageBreadcrumbs.vue";
import PageBuilder from "@/Components/BuilderUi/Pages/PageBuilder.vue";
import PageSubSectionLinks from "@/Components/BuilderUi/Pages/PageSubSectionLinks.vue";
import PageTitle from "@/Components/BuilderUi/Pages/PageTitle.vue";
import ClientFacultyWorkerCard from "@/Components/PersonCards/ClientFacultyWorkerCard.vue";
import PostBuilder from "@/Components/BuilderUi/Posts/PostBuilder.vue";
import FacultyBuilder from "@/Components/BuilderUi/Faculties/FacultyBuilder.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import FacultyItemBreadcrumbs from "@/Components/BuilderUi/Faculties/FacultyItemBreadcrumbs.vue";
import AppHead from "@/Components/AppHead.vue";


export default {
	name: "Page",
	data() {
		return {
			scrollTop: false,
			currentNavSection: null,
		}
	},
	
	props: {
		faculty: {
			type: Object,
		},
		faculties: {
			type: Object
		},
		seo: {
			type: Object,
		}
	},

	components: {
		AppHead,
		FacultyItemBreadcrumbs,
		MainPageNavBar,
		FacultyBuilder,
		PostBuilder,
		ClientFacultyWorkerCard,
		PageTitle, PageSubSectionLinks, PageBuilder, PageBreadcrumbs, PageNavigateLinks,
		ClientFooterDown,
		MainNavbar,
		Link,
		FsLightbox,
		Head
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
		openEditorImagesOnSlide: function (number) {
			this.slide = number;
			this.toggler = !this.toggler;
		},
		isSameRoute(route) {
			if (this.$page.props.ziggy.location === route) {
				return true
			}
		},
		generateSlug: function (text) {
			return slugify(text, {
				lower: true,
				strict: true,
				locale: 'ru'
			});
		},
		onScroll(e) {
			const windowTop = window.top.scrollY
			if (windowTop > 100) {
				this.scrollTop = true
			} else {
				this.scrollTop = false
			}
		},
		scrollToTop() {
			window.scrollTo(0, 0)
		},


	},
	mounted() {
		window.addEventListener("scroll", this.onScroll)

		// this.editorImages = this.blocksWithSlideNumber.filter(block => block.type === 'image').map(block => block.data.file.url);

		window.addEventListener("scroll", () => {
			const headings = document.querySelectorAll('h2');
			const visor = document.querySelector('#visor');
			let lastVisibleHeading = null;

			const visorRect = visor.getBoundingClientRect();

			// Проверяем, находится ли визор в пределах видимости
			if (visorRect.top > window.scrollY) {
				this.currentNavSection = null;
				lastVisibleHeading = null; // Сбрасываем заголовок, если визор не виден
				return; // Выходим из функции, если визор не виден
			}

			for (let i = 0; i < headings.length; i++) {
				const heading = headings[i];
				const rect = heading.getBoundingClientRect();

				// Проверяем, находится ли заголовок в видимой области и касается ли он элемента visor
				if (rect.top >= 0 && rect.bottom <= window.innerHeight && rect.bottom >= visorRect.top && rect.top <= visorRect.bottom) {
					// Проверяем, изменился ли заголовок
					if (heading !== lastVisibleHeading) {
						this.currentNavSection = heading.id;
						lastVisibleHeading = heading;
					}
					break; // Выходим из цикла, если нашли видимый заголовок
				}
			}
		});


	},
	beforeDestroy() {
		window.removeEventListener('scroll', this.handleScroll);
		window.removeEventListener("scroll", this.onScroll)
	},

	computed: {
	}
}
</script>

<style>



.paragraph-container a {
	@apply text-[#1E57A3];
	@apply underline;
}

.paragraph-container p {
	@apply mb-2
}



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
	from { transform: scaleX(0); }
	to { transform: scaleX(1); }
}

#progress {
	height: 2px;
	background: #26ACB8;
	z-index: 10000;

	transform-origin: 0 50%;
	animation: grow-progress auto linear;
	animation-timeline: scroll();
}


.active {
	color: blue !important;
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
