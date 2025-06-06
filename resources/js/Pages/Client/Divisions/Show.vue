<template>
	<MetaTags :seo="seo" />

	<div class="flex flex-col h-screen">
		<MainPageNavBar :sections="$page.props.navigation" />

		<main class="flex-grow">
			<div class="relative  mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<div class="w-full h-[67px] fixed pointer-events-none" id="visor"></div>

				<div class="sticky top-[110px] hidden h-[calc(100vh-110px)] max-w-[20%] md:flex md:shrink-0 md:flex-col md:justify-between">
					<nav v-if="divisions"
							 class="flex h-[calc(100vh-200px)] flex-col overflow-hidden pr-2 pb-4">
						<div class="text-gray-1000 mb-2 text-md font-medium">Подразделения</div>
						<div class="flex gap-x-1">
							<ul class="px-0.5 last-of-type:mb-0 mb-8">
								<li v-for="division in divisions.data" :key="division.id" class="my-1.5 flex">
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
						<ul class="max-h-[70vh] space-y-1.5 overflow-hidden py-2 text-sm">
							<li v-if="division.data.workers.length > 0" class="anchor-li">
								<a :class="{ 'translate-x-2 text-primary-light' : currentNavSection  === 'persons', 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== 'persons' }"
									 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
									 href="#persons">Состав</a>
							</li>
							<li v-if="division.data.description.length > 0" class="anchor-li">
								<a :class="{ 'translate-x-2 text-primary-light' : currentNavSection  === 'description', 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== 'description' }"
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
            <BaseBreadcrumbs :breadcrumbs="breadcrumbs">
              <BreadcrumbsItem title="Подразделения института" :url="route('client.division.index')" />
              <BreadcrumbsItem :title="division.data.title" :url="$page.url" />
            </BaseBreadcrumbs>

						<div class="space-y-3">
							<h1 class="text-2xl mb-10 font-bold md:text-3xl">{{ division.data.title }}</h1>
						</div>

						<div id="page-area" class="space-y-5 md:space-y-5">

							<h2 v-if="division.data.workers.length > 0" :id="'anchor-link-' + generateSlug('Состав')" class="font-bold text-xl">Состав</h2>
							<template v-for="worker in division.data.workers">
								<DivisionWorkerCard :worker="worker" />
							</template>



							<h2 :id="'anchor-link-' + generateSlug('Описание')" v-if="division.data.description.length > 0" class="font-bold text-xl">Описание</h2>

              <div class="space-y-3 md:space-y-4">
                <Builder :blocks="division.data.description" />
              </div>

						</div>


					</div>
				</main>
			</div>
		</main>

		<BasicFooter/>
	</div>



</template>

<script>


import {Link} from "@inertiajs/vue3";
import { Head } from '@inertiajs/vue3'
import slugify from "slugify";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import DivisionWorkerCard from "@/componentss/features/divisions/components/DivisionWorkerCard.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import DivisionItemBreadcrumbs from "@/componentss/features/divisions/components/DivisionItemBreadcrumbs.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import BaseBreadcrumbs from "@/componentss/shared/Breadcrumbs/BaseBreadcrumbs.vue";


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
		division: {
			type: Object,
		},
		divisions: {
			type: Object
		},
		seo: {
			type: Object,
		},
    breadcrumbs: {
      type: Object
    }
	},

	components: {
    BaseBreadcrumbs, BreadcrumbsItem,
    BasicFooter,
    DivisionWorkerCard,
    Builder,
    MetaTags,
		DivisionItemBreadcrumbs,
		MainPageNavBar,
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
    handleScroll() {
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
    extractH2Headers() {
      const h2Elements = document.querySelectorAll('h2'); // выбираем все h2 на странице
      this.headerNavs = Array.from(h2Elements).map(h2 => ({
        id: h2.id,           // id заголовка
        text: h2.textContent // содержимое заголовка
      }));
    }


	},
	mounted() {
    this.extractH2Headers()

    window.addEventListener("scroll", this.handleScroll);

    window.addEventListener("scroll", this.onScroll)

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

</style>
