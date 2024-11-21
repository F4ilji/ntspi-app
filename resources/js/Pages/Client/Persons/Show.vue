<template>
	<AppHead :seo="seo" />


	<div class="flex flex-col h-screen">
		<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

		<main class="flex-grow">
			<div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">

				<PageNavigateLinks :header-navs="headerNavs" />

				<section class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6" style="">
					<div class="w-full mx-auto sm:px-6 lg:px-8">

						<PersonBreadcrumbs class="mb-4" :person-name="person.data.name" />

						<div class="flex items-center gap-x-10 gap-y-4 flex-wrap">
							<PersonAvatarBlock v-if="person.data.details.photo" :photo="person.data.details.photo" />
							<div class="grow">
								<h1 class="text-2xl font-medium text-gray-800 dark:text-neutral-200">
									{{ person.data.name }}
								</h1>
								<div class="">
									<template v-for="division_work in person.data.divisions_works">
										<Link :href="route('client.division.show', division_work.slug)" class="text-sm text-blue-500 dark:text-neutral-400 mr-2">
											{{ division_work.position }}
										</Link>
									</template>
									<template v-for="faculty_work in person.data.faculties_works">
										<Link :href="route('client.faculty.show', faculty_work.slug)" class="text-sm text-blue-500 dark:text-neutral-400 mr-2">
											{{ faculty_work.position }}
										</Link>
									</template>
									<template v-for="division_work in person.data.departments_work">
										<Link :href="route('client.department.show', { facultySlug: division_work.faculty_slug, departmentSlug: division_work.slug })" class="text-sm text-blue-500 dark:text-neutral-400 mr-2">
											{{ division_work.position }}
										</Link>
									</template>
									<template v-for="division_tech in person.data.departments_teach">
										<Link :href="route('client.department.show', { facultySlug: division_tech.faculty_slug, departmentSlug: division_tech.slug })" class="text-sm text-blue-500 dark:text-neutral-400 mr-2">
											{{ division_tech.position }}
										</Link>
									</template>
								</div>

								<ul class="mt-5 flex flex-col gap-y-3">

									<li class="flex items-center gap-x-2.5">
										<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="shrink-0 size-3.5">
											<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
										</svg>

										<a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 dark:text-neutral-500 dark:hover:text-neutral-400" href="#">
											{{ person.data.details.contactPhone }}
										</a>
									</li>
									<li class="flex items-center gap-x-2.5">
										<svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<rect width="20" height="16" x="2" y="4" rx="2"></rect>
											<path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
										</svg>
										<a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 dark:text-neutral-500 dark:hover:text-neutral-400" href="#">
											{{ person.data.details.contactEmail }}
										</a>
									</li>
								</ul>


							</div>
						</div>

						<div class="mt-10 sm:mt-14">
							<EducationBlock v-if="person.data.details.education.length > 0" title="Направление подготовки" :educations="person.data.details.education" />
						</div>

						<div class="mt-8">
							<ListBlock v-if="person.data.details.awards.length > 0" name-list="Награды" :list="person.data.details.awards" />
						</div>

						<div class="mt-8">
							<ListBlock v-if="person.data.details.professionalRetraining.length > 0" name-list="Профессиональная переподготовка" :list="person.data.details.professionalRetraining" />
						</div>

						<div class="mt-8">
							<ListBlock v-if="person.data.details.professionalDevelopment.length > 0" name-list="Повышение квалификации" :list="person.data.details.professionalDevelopment" />
						</div>

						<div class="mt-8">
							<ListBlock v-if="person.data.details.professDisciplines.length > 0" name-list="Преподаваемые дисциплины" :list="person.data.details.professDisciplines" />
						</div>

						<div class="mt-8">
							<WorkExperienceBlock v-if="person.data.details.workExperience.total" title="Стаж" :work-exp-by-prof="person.data.details.workExperience.byProf" :work-exp-total="person.data.details.workExperience.total" />
						</div>

						<div class="mt-8">
							<ListBlock v-if="person.data.details.attendedConferences.length > 0" name-list="Участие в конференциях" :list="person.data.details.attendedConferences" />
						</div>

						<div class="mt-8">
							<ListBlock v-if="person.data.details.participationScienceProjects.length > 0" name-list="Участие в научных проектах" :list="person.data.details.participationScienceProjects" />
						</div>

						<div class="mt-8">
							<PublicationBlock v-if="person.data.details.publications.length > 0" name-list="Публикации" :list="person.data.details.publications" />
						</div>

						<div class="mt-8">
							<OtherBlock v-if="person.data.details.other.length > 0" name-list="Другое" :blocks="person.data.details.other" />
						</div>
					</div>
				</section>
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
import AppHead from "@/Components/AppHead.vue";
import PersonBreadcrumbs from "@/Components/BuilderUi/Persons/PersonBreadcrumbs.vue";
import PersonAvatarBlock from "@/Components/BuilderUi/Persons/PersonAvatarBlock.vue";
import PersonNavLink from "@/Components/BuilderUi/Persons/PersonNavLink.vue";
import PageNavigateLinks from "@/Components/BuilderUi/Pages/PageNavigateLinks.vue";
import EducationBlock from "@/Components/BuilderUi/Persons/Blocks/EducationBlock.vue";
import ListBlock from "@/Components/BuilderUi/Persons/Blocks/ListBlock.vue";
import WorkExperienceBlock from "@/Components/BuilderUi/Persons/Blocks/WorkExperienceBlock.vue";
import OtherBlock from "@/Components/BuilderUi/Persons/Blocks/OtherBlock.vue";
import PublicationBlock from "@/Components/BuilderUi/Persons/Blocks/PublicationBlock.vue";


export default {
	name: "Show",
	data() {
		return {
			scrollTop: false,
			currentNavSection: null,
			headerNavs: []
		}
	},
	
	props: {
		person: {
			type: Object,
		},
		seo: {
			type: Object,
		}
	},

	components: {
		PublicationBlock,
		OtherBlock,
		WorkExperienceBlock,
		ListBlock,
		EducationBlock,
		PageNavigateLinks,
		PersonNavLink,
		PersonAvatarBlock,
		PersonBreadcrumbs,
		AppHead,
		MainPageNavBar,
		ClientFooterDown,
		MainNavbar,
		Link,
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
		handleScroll(e) {
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
		},
		scrollToTop() {
			window.scrollTo(0, 0)
		},
		extractH2Headers() {
			const h2Elements = document.querySelectorAll('h2'); // выбираем все h2 на странице
			this.headerNavs = Array.from(h2Elements).map(h2 => ({
				id: h2.id,           // id заголовка
				text: h2.textContent // содержимое заголовка
			}));
		}



	},
	mounted() {
		this.extractH2Headers();

		window.addEventListener("scroll", this.onScroll)


		window.addEventListener("scroll", this.handleScroll);


	},
	beforeDestroy() {
		window.removeEventListener("scroll", this.handleScroll);
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
