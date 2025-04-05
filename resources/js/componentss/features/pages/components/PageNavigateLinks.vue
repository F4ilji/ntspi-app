<template>
	<div class="w-full h-[67px] fixed pointer-events-none bvi-no-styles" id="visor"></div>

	<nav class="order-last hidden w-56 shrink-0 lg:block">
		<div v-if="headerNavs.length > 0" class="sticky top-[100px] h-[calc(100vh-121px)]">
			<div class="text-gray-1000 mb-2 text-md font-medium">На этой странице</div>
			<ul class="max-h-[70vh] space-y-1.5 overflow-hidden py-2 text-sm">
				<li class="anchor-li" v-for="pageNav in headerNavs" :key="pageNav.id">
					<a :class="{ 'translate-x-2 text-primary' : currentNavSection === generateSlug(pageNav.text), 'bg-transperant text-gray-600 hover:text-gray-900' : currentNavSection !== generateSlug(pageNav.text) }"
						 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
						 :href="'#' + generateSlug(pageNav.text)">{{ pageNav.text }}</a>
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


</template>

<script>
import { Link } from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "PageNavigateLinks",
	components: { Link },
	data() {
		return {
			currentNavSection: null,
			scrollTop: false,
		};
	},
	methods: {
		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText;
				LimitedText = text.substring(0, symbols);
				return LimitedText + "...";
			}
			return text;
		},
		generateSlug(text) {
			return slugify(text, {
				lower: true,
				strict: true,
				locale: "ru",
			});
		},
		onScroll(e) {
			const windowTop = window.scrollY;
			this.scrollTop = windowTop > 100;

			const headings = document.querySelectorAll("h2");
			const visor = document.querySelector("#visor");
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
				if (
						rect.top >= 0 &&
						rect.bottom <= window.innerHeight &&
						rect.bottom >= visorRect.top &&
						rect.top <= visorRect.bottom
				) {
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
			window.scrollTo(0, 0);
		},
	},
	mounted() {
		window.addEventListener("scroll", this.onScroll);
	},
	unmounted() {
		window.removeEventListener("scroll", this.onScroll);
	},
	props: {
		headerNavs: {
			type: Array,
		},
	},
};
</script>

<style scoped>

</style>