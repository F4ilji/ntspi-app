<template>
	<Head>
		<title>{{ division.data.title }}</title>
		<meta name="description" content="Your page description">
	</Head>



	<div class="flex flex-col h-screen">
		<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

		<main class="flex-grow">
			<div class="relative  mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="w-full h-[67px] fixed" id="visor"></div>

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
										<ol class="flex items-center whitespace-normal min-w-0"
												aria-label="Breadcrumb">
											<li class="text-sm">
										<span class="flex items-center text-gray-500 hover:text-blue-600">
											Главная
											<svg class="flex-shrink-0 mx-3 overflow-visible h-2.5 w-2.5 text-gray-400"
													 width="16" height="16"
													 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
															stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
											</svg>
										</span>
											</li>
											<li class="text-sm">
										<span class="flex items-center text-gray-500 hover:text-blue-600" @click.prevent>
											Факультеты
											<svg class="flex-shrink-0 mx-3 overflow-visible h-2.5 w-2.5 text-gray-400"
													 width="16" height="16"
													 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
															stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
											</svg>
										</span>
											</li>

										</ol>
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

							<!-- FAQ -->

							<!-- End FAQ -->


							<!-- Card Section -->
							<!-- Grid -->

							<!-- End Grid -->
							<!-- End Card Section -->

							<!--										<template v-for="block in this.blocks" :key="block.id">-->
							<!--						<div v-if="block.type === 'image'">-->
							<!--							<figure :class="block.data.withBackground ? 'bg-gray-100 rounded-lg' : ''">-->
							<!--								<img loading="lazy" @click="openEditorImagesOnSlide(block.slideNumber)"-->
							<!--									 :src="block.data.file.url"-->
							<!--									 :class="block.data.withBackground ? 'rounded-none' : 'w-full'"-->
							<!--									 class="mx-auto max-h-[350px] object-cover rounded-lg hover:opacity-95 hover:duration-200 transition"-->
							<!--									 :alt="block.data.caption">-->
							<!--								<figcaption class="mt-3 text-sm text-center text-gray-500">-->
							<!--									{{ block.data.caption }}-->
							<!--								</figcaption>-->
							<!--							</figure>-->
							<!--						</div>-->
							<!--						<div v-if="block.type === 'heading'">-->
							<!--							<h2 :id="generateSlug(block.data.content)" class="font-bold text-xl">{{ block.data.content }}</h2>-->
							<!--						</div>-->
							<!--						<div v-if="block.type === 'image'">-->
							<!--							<template v-for="img in block.data.url">-->
							<!--								<img loading="lazy" class="mx-auto object-cover rounded-sm hover:opacity-95 hover:duration-200 transition" :src="'/storage/' + img" alt="">-->
							<!--							</template>-->
							<!--						</div>-->
							<!--						<div class="text-[16px] text-[#374151] dark:text-gray-200 leading-8 font-light" v-html="block.data.content" v-if="block.type === 'paragraph'"></div>-->
							<!--						<div v-if="block.type === 'attaches'">-->
							<!--							<div class="flex border rounded-lg px-4 py-2 items-center justify-between">-->
							<!--								<div class="flex items-center">-->
							<!--									<div class="w-[35px] h-[35px] bg-black flex justify-center items-center rounded-xl mr-2">-->
							<!--										<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"-->
							<!--											 stroke-width="1.5"-->
							<!--											 stroke="currentColor" class="w-6 h-6">-->
							<!--											<path stroke-linecap="round" stroke-linejoin="round"-->
							<!--												  d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"-->
							<!--												  stroke="white"/>-->
							<!--										</svg>-->
							<!--									</div>-->
							<!--									<div>{{ block.data.title }}</div>-->
							<!--								</div>-->

							<!--								<a :href="block.data.file.url" download type="button"-->
							<!--								   class="w-[35px] h-[35px] flex bg-gray-100 rounded-lg justify-center items-center hover:bg-gray-200 duration-200">-->
							<!--									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"-->
							<!--										 stroke-width="1.5"-->
							<!--										 stroke="currentColor" class="w-6 h-6">-->
							<!--										<path stroke-linecap="round" stroke-linejoin="round"-->
							<!--											  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>-->
							<!--									</svg>-->
							<!--								</a>-->
							<!--							</div>-->
							<!--						</div>-->
							<!--						<div v-if="block.type === 'linkTool'">-->
							<!--							<div v-if="block.data.meta.type === 'post'" class="w-full mx-auto">-->
							<!--								<a class="group relative block rounded-xl dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" :href="block.data.link">-->
							<!--										<div class="flex-shrink-0 relative w-full rounded-xl overflow-hidden w-full h-[350px] before:absolute before:inset-x-0 before:w-full before:h-full before:bg-gradient-to-t before:from-gray-900/[.7] before:z-[1]">-->
							<!--										</div>-->

							<!--										<div class="absolute top-0 inset-x-0 z-10">-->
							<!--											<div class="p-4 flex flex-col h-full sm:p-6">-->
							<!--												&lt;!&ndash; Avatar &ndash;&gt;-->
							<!--												<div class="flex items-center">-->
							<!--													<div class="ms-2.5 sm:ms-4">-->
							<!--														<h4 class="font-semibold text-white">-->
							<!--															{{ block.data.meta.data.authors[0].name }}-->
							<!--														</h4>-->
							<!--														<p class="text-xs text-white/[.8]">-->
							<!--															{{ block.data.meta.data.created_post }}-->
							<!--														</p>-->
							<!--													</div>-->
							<!--												</div>-->
							<!--												&lt;!&ndash; End Avatar &ndash;&gt;-->
							<!--											</div>-->
							<!--										</div>-->

							<!--										<div class="absolute bottom-0 inset-x-0 z-10">-->
							<!--											<div class="flex flex-col h-full p-4 sm:p-6">-->
							<!--												<h3 class="text-lg sm:text-3xl font-semibold text-white group-hover:text-white/[.8]">-->
							<!--													{{ block.data.meta.data.title }}-->
							<!--												</h3>-->
							<!--												<p class="mt-2 text-white/[.8]">-->
							<!--													{{ textLimit(block.data.meta.description, 200) }}-->
							<!--												</p>-->
							<!--											</div>-->
							<!--										</div>-->
							<!--									</a>-->
							<!--							</div>-->
							<!--							&lt;!&ndash; End Card Blog &ndash;&gt;-->
							<!--							<div v-if="block.data.meta.type === 'student'" class="flex flex-col rounded-xl p-4 md:p-6 bg-white border border-gray-200 dark:bg-slate-900 dark:border-gray-700">-->
							<!--								<div class="flex items-center gap-x-4">-->
							<!--									<img loading="lazy" class="rounded-xl w-[150px]" :src="block.data.meta.data.photo" alt="Image Description">-->
							<!--									<div class="grow">-->
							<!--										<Link :href="block.data.link" class="font-medium text-gray-800 hover:text-gray-500 underline">-->
							<!--											{{ block.data.meta.title }}-->
							<!--										</Link>-->
							<!--										<p class="text-xs text-gray-500 mt-2">-->
							<!--											{{ block.data.meta.data.position }}-->
							<!--										</p>-->
							<!--										<p class="text-xs text-gray-500 mt-2">-->
							<!--											{{ block.data.meta.data.contactEmail }} / <a :href="block.data.meta.data.vk_link">Вконтакте</a>-->
							<!--										</p>-->
							<!--									</div>-->
							<!--								</div>-->

							<!--								&lt;!&ndash; Social Brands &ndash;&gt;-->
							<!--								&lt;!&ndash; End Social Brands &ndash;&gt;-->
							<!--							</div>-->
							<!--						</div>-->

							<!--					</template>-->
						</div>


					</div>
				</article>
			</div>
		</main>
		<ClientFooterDown/>
	</div>

	<FsLightbox class="z-1000" :slide="slide" :toggler="toggler" :sources="editorImages"/>


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
