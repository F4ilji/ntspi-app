<template>
	<Head>
		<title>{{ division.data.title }}</title>
		<meta name="description" content="Your page description">
	</Head>



	<div class="flex flex-col h-screen">
		<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

		<main class="flex-grow">
			<div class="relative  mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="w-full h-[67px] fixed pointer-events-none" id="visor"></div>

				<div class="sticky top-[110px] hidden h-[calc(100vh-110px)] max-w-[20%] md:flex md:shrink-0 md:flex-col md:justify-between">
					<nav v-if="this.divisions"
							 class="styled-scrollbar flex h-[calc(100vh-200px)] flex-col overflow-y-scroll pr-2 pb-4">
						<div class="text-gray-1000 mb-2 text-md font-medium">Подразделения</div>
						<div class="flex gap-x-1">
							<ul class="px-0.5 last-of-type:mb-0 mb-8">
								<li v-for="division in this.divisions.data" :key="division.id" class="my-1.5 flex">
									<a :class="{'text-white hover:text-gray-200 font-semibold bg-[#135aae]': isSameRoute(route('client.division.show', division.slug)), 'text-gray-600 hover:text-[#2C6288]': !isSameRoute(route('client.division.show', division.id)) }"
										 :href="route('client.division.show', division.slug) + '/'"
										 class="relative duration-300 flex w-full rounded-md cursor-pointer items-center px-2 py-1 text-left text-sm">{{
											division.title
										}}</a>
								</li>
							</ul>
						</div>

					</nav>
				</div>

				<nav class="order-last hidden w-56 shrink-0 lg:block">
					<div class="sticky top-[110px] h-[calc(100vh-110px)]">
						<div class="text-gray-1000 mb-2 text-md font-medium">На этой странице</div>
						<ul class="styled-scrollbar max-h-[70vh] space-y-1.5 overflow-y-auto py-2 text-sm">
							<li v-if="division.data.workers.length > 0" class="anchor-li">
								<a :class="{ 'translate-x-2 text-[#135aae]' : this.currentNavSection  === 'persons', 'bg-transperant text-gray-600 hover:text-gray-900' : this.currentNavSection !== 'persons' }"
									 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
									 href="#persons">Состав</a>
							</li>
							<li v-if="division.data.description.length > 0" class="anchor-li">
								<a :class="{ 'translate-x-2 text-[#135aae]' : this.currentNavSection  === 'description', 'bg-transperant text-gray-600 hover:text-gray-900' : this.currentNavSection !== 'description' }"
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

				<main class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
					<div class="space-y-5 md:space-y-5">
						<div class="flex justify-between items-center mb-6">
							<div class="flex w-full sm:items-center gap-x-5 sm:gap-x-3">
								<div class="grow">
									<div class="grid sm:flex sm:justify-between sm:items-center gap-2">
										<DivisionItemBreadcrumbs :division-title="division.data.title" />
									</div>
								</div>
							</div>
						</div>

						<div class="space-y-3">
							<h1 class="text-2xl mb-10 font-bold md:text-3xl">{{ division.data.title }}</h1>
						</div>

						<div id="page-area" class="space-y-5 md:space-y-5">

							<h2 v-if="division.data.workers.length > 0" id="persons" class="font-bold text-xl">Состав</h2>
							<template v-for="worker in division.data.workers">
								<div class="flex flex-col rounded-xl p-4 md:p-6 bg-white border border-gray-200 dark:bg-slate-900 dark:border-gray-700">
									<div class="flex items-center gap-x-4">
										<img loading="lazy" class="rounded-xl w-[150px]" :src="'/storage/' + worker.details.photo" alt="Image Description">
										<div class="grow">
											<Link :href="route('client.person.show', worker.id)" class="font-medium text-gray-800 hover:text-gray-500 underline">
												{{ worker.administrativePosition }}: {{ worker.name }}
											</Link>
											<p v-if="worker.details.academicTitle" class="text-xs text-gray-500 mt-2">
												Ученая степень: {{ worker.details.academicTitle }}
											</p>
											<p class="text-xs text-gray-500 mt-2">
												Контактный телефон: {{ worker.details.contactPhone }}
											</p>
											<p class="text-xs text-gray-500 mt-2">
												Электронная почта: {{ worker.details.contactEmail }}
											</p>
											<p class="text-xs text-gray-500 mt-2">
												Кабинет: 207В
											</p>
										</div>
									</div>

									<!-- Social Brands -->
									<!-- End Social Brands -->
								</div>
							</template>



							<h2 v-if="division.data.description.length > 0" id="description" class="font-bold text-xl">Описание</h2>

							<DivisionBuilder :blocks="division.data.description" />




						</div>


					</div>
				</main>
			</div>
		</main>
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
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import DivisionBuilder from "@/Components/BuilderUi/Divisions/DivisionBuilder.vue";
import DivisionItemBreadcrumbs from "@/Components/BuilderUi/Divisions/DivisionItemBreadcrumbs.vue";


export default {
	name: "Show",
	data() {
		return {
			scrollTop: false,
			currentNavSection: null,
		}
	},

	props: {
		division: {
			type: Object,
		},
		divisions: {
			type: Object
		},
	},

	components: {
		DivisionItemBreadcrumbs,
		DivisionBuilder,
		MainPageNavBar,
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

.styled-scrollbar {
	overflow: hidden;
}


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
