<template>

	<AppHead :seo="seo" />


	<MainPageNavBar class="border-b" :sections="$page.props.navigation" />


	<div class="flex flex-col h-screen justify-between">
		<div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">

			<div class="w-full h-[67px] pointer-events-none fixed" id="visor"></div>

			<div class="sticky top-[110px] hidden h-[calc(100vh-110px)] max-w-[20%] md:flex md:shrink-0 md:flex-col md:justify-between">
				<nav v-if="departments"
						 class="flex h-[calc(100vh-200px)] flex-col overflow-hidden pr-2 pb-4">
					<div class="text-gray-1000 mb-2 text-md font-medium">Кафедры факультета - {{ department.data.faculty.abbreviation }}</div>
					<div class="flex gap-x-1">
						<ul class="px-0.5 last-of-type:mb-0 mb-8">
							<li v-for="depart in departments.data" :key="department.id" class="my-1.5 flex">
								<a :class="{'text-white hover:text-gray-200 font-semibold bg-[#135aae]': isSameRoute(route('client.department.show', { facultySlug: department.data.faculty.slug, departmentSlug: depart.slug })), 'text-gray-600 hover:text-[#2C6288]': !isSameRoute(route('client.department.show', { facultySlug: department.data.faculty.slug, departmentSlug: depart.slug })) }"
									 :href="route('client.department.show', { facultySlug: department.data.faculty.slug, departmentSlug: depart.slug }) + '/'"
									 class="relative duration-300 flex w-full rounded-md cursor-pointer items-center px-2 py-1 text-left text-sm">{{
										depart.title
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
							<a :class="{ 'translate-x-2 text-[#135aae]' : currentNavSection  === 'workers', 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== 'workers' }"
								 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
								 href="#workers">Сотрудники кафедры</a>
						</li>
						<li class="anchor-li">
							<a :class="{ 'translate-x-2 text-[#135aae]' : currentNavSection  === 'teachers', 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== 'teachers' }"
								 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
								 href="#teachers">Преподаватели кафедры</a>
						</li>
						<li class="anchor-li">
							<a :class="{ 'translate-x-2 text-[#135aae]' : currentNavSection  === 'external-teachers', 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== 'external-teachers' }"
								 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
								 href="#external-teachers">Внешние совместители</a>
						</li>
						<li class="anchor-li">
							<a :class="{ 'translate-x-2 text-[#135aae]' : currentNavSection  === 'programs', 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== 'programs' }"
								 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
								 href="#programs">Программы</a>
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
									<DepartmentItemBreadcrumbs :faculty="department.data.faculty" :department-title="department.data.title" />
								</div>
							</div>
						</div>
					</div>

					<div class="space-y-3">
						<h1 class="text-2xl mb-10 font-bold md:text-3xl">{{ department.data.title }}</h1>
					</div>

					<div id="page-area" class="space-y-5 md:space-y-5">
						<h2 id="workers" class="font-bold text-xl">Сотрудники кафедры</h2>

						<template v-for="worker in department.data.workers">
							<ClientDepartmentWorkerCard :worker="worker" />
						</template>
						<h2 id="teachers" class="font-bold text-xl">Преподаватели кафедры</h2>
						<template v-for="teacher in department.data.teachers">
							<ClientDepartmentTeacherCard :teacher="teacher" />
						</template>
						<h2 id="external-teachers" class="font-bold text-xl">Внешние совместители</h2>

						<h2 id="programs" class="font-bold text-xl">Программы</h2>



						<div class="hs-accordion-group">
							<div v-for="(direction, index) in directions" class="hs-accordion hs-accordion-active:bg-gray-100 rounded-xl px-4 py-6" id="hs-basic-with-title-and-arrow-stretched-heading-three">
								<button class="hs-accordion-toggle group pb-3 inline-flex items-center justify-between gap-x-3 w-full md:text-lg font-semibold text-start text-gray-800 rounded-lg transition hover:text-gray-500 focus:outline-none" aria-expanded="false" aria-controls="hs-basic-with-title-and-arrow-stretched-collapse-three">
									{{ index }}
									<svg class="hs-accordion-active:hidden block shrink-0 size-5 text-gray-600 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
									<svg class="hs-accordion-active:block hidden shrink-0 size-5 text-gray-600 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
								</button>
								<div id="hs-basic-with-title-and-arrow-stretched-collapse-three" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="hs-basic-with-title-and-arrow-stretched-heading-three">
									<ul class="list-disc list-outside space-y-3 ps-5 text-lg text-gray-800 dark:text-neutral-200">
										<li v-for="program in direction" class="ps-2">
											<Link :href="route('client.program.show', program.id)" class="text-base text-blue-600 decoration-2 hover:underline focus:outline-none focus:underline font-medium dark:text-blue-500">{{ program.name }}</Link>
										</li>
									</ul>
								</div>
							</div>


						</div>

						<h2 id="description" class="font-bold text-xl">Описание</h2>


						<div class="space-y-3 md:space-y-4">
							<DepartmentBuilder :blocks="department.data.content "/>
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
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import { Head } from '@inertiajs/vue3'
import slugify from "slugify";
import PageNavigateLinks from "@/Components/BuilderUi/Pages/PageNavigateLinks.vue";
import PageBreadcrumbs from "@/Components/BuilderUi/Pages/PageBreadcrumbs.vue";
import PageBuilder from "@/Components/BuilderUi/Pages/PageBuilder.vue";
import PageTitle from "@/Components/BuilderUi/Pages/PageTitle.vue";
import ClientDepartmentWorkerCard from "@/Components/PersonCards/ClientDepartmentWorkerCard.vue";
import ClientDepartmentTeacherCard from "@/Components/PersonCards/ClientDepartmentTeacherCard.vue";
import FacultyBuilder from "@/Components/BuilderUi/Faculties/FacultyBuilder.vue";
import DepartmentBuilder from "@/Components/BuilderUi/Departments/DepartmentBuilder.vue";
import DepartmentBackButton from "@/Components/BuilderUi/Departments/DepartmentBackButton.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import DepartmentItemBreadcrumbs from "@/Components/BuilderUi/Departments/DepartmentItemBreadcrumbs.vue";
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
		department: {
			type: Object,
		},
		departments: {
			type: Object
		},
		directions: {
			type: Object
		},
		seo: {
			type: Object
		}
	},

	components: {
		AppHead,
		DepartmentItemBreadcrumbs,
		MainPageNavBar,
		DepartmentBackButton,
		DepartmentBuilder,
		FacultyBuilder,
		ClientDepartmentTeacherCard,
		ClientDepartmentWorkerCard,
		PageTitle, PageBuilder, PageBreadcrumbs, PageNavigateLinks,
		ClientFooterDown,
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
