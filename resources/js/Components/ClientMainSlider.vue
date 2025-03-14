<template>
	<div ref="sliderRef" class="relative z-0 min-h-[calc(100vh)] items-center">
		<a :href="(!slider.settings.link_text) ? slider.link : '#'" v-for="(slider, index) in slidersCarousel.data" :style="{ filter: `brightness(${slider.image.shading})` }"
			 class="absolute -z-10 h-full w-full before:absolute before:z-10 before:h-full before:w-full ">
			<img
					alt="Thumbnail"
					loading="eager"
					decoding="async"
					data-nimg="fill"
					class="object-cover transition-opacity duration-1000 absolute inset-0 h-full w-full"
					sizes="100vw"
					:key="index"
					:src="'/storage/' + slider.image.url"
					:class="{ 'opacity-1': currentIndex === index, 'opacity-0': currentIndex !== index }"
			/>
		</a>
		<div v-show="currentIndex === index" v-for="(item, index) in slidersCarousel.data" :key="index" class="mx-auto max-w-screen-md px-5 pt-[150px] pb-0 bvi-no-styles">
			<h1 v-if="item.title" :class="`text-${item.settings.text_position}`" class="text-brand-primary mb-3 mt-2 text-3xl font-semibold tracking-tight text-white lg:text-5xl lg:leading-tight">
				{{ item.title }}
			</h1>
			<div v-if="item.content" class="mt-8 space-x-3 text-gray-500 mb-8">
				<div class="gap-3 md:flex-row md:items-center">
					<div class="gap-3">
						<p :class="`text-${item.settings.text_position}`" class="text-gray-100 line-clamp-3">
							{{ item.content }}
						</p>
					</div>
				</div>
			</div>
			<div :class="`text-${item.settings.text_position}`">
				<a
						v-if="item.settings.link_text"
						:href="item.link"
						class="py-3 px-4 inline-flex items-center duration-300 gap-x-2 text-sm font-semibold rounded-lg border border-white text-white bg-transparent  hover:bg-white hover:text-black hover:mix-blend-screen disabled:opacity-50 disabled:pointer-events-none"
				>
					{{ item.settings.link_text }}
				</a>
			</div>
		</div>

		<div v-if="slidersCarousel.data.length >= 2" class="mx-auto max-w-screen-md px-5">
			<div class="mt-8 text-gray-500 absolute bottom-[50px] left-1/2 transform -translate-x-1/2 lg:bottom-[100px]">
				<div class="">
					<div class="flex space-x-3 items-center justify-between">
						<button @click="prev" class="bg-gray-200 w-8 h-8 hover:bg-gray-300 text-gray-800 font-bold rounded-full">
							<svg class="w-4 h-4 mx-auto my-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
							</svg>
						</button>

						<div class="flex space-x-3 w-[200px] items-center font-semibold text-xl text-white">
							<span>{{ currentIndex + 1 }}</span>
							<div class="flex w-full h-1 bg-gray-200 rounded-full overflow-hidden" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
								<div class="flex flex-col justify-center rounded-full overflow-hidden bg-blue-600 text-xs text-white text-center whitespace-nowrap transition-all duration-500 ease-in-out" :style="{ 'width': `${progressBarStep}%` }"></div>
							</div>
							<span>{{ slidersCarousel.data.length }}</span>
						</div>

						<button @click="next" class="bg-gray-200 w-8 h-8 hover:bg-gray-300 text-gray-800 font-bold rounded-full">
							<svg class="w-4 h-4 mx-auto my-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19l7-7-7-7"></path>
							</svg>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	name: 'slider',
	props: {
		slidersCarousel: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			currentIndex: 0,
			intervalId: null,
			slideDuration: 5000,
		}
	},
	computed: {
		totalSlides() {
			return this.slidersCarousel && this.slidersCarousel.data ? this.slidersCarousel.data.length : 0;
		},
		progressBarStep() {
			const slides = this.totalSlides;
			const step = 100 / slides;
			return (this.currentIndex + 1) * step;
		}
	},
	methods: {
		...mapActions(['updateLastSlider']),
		next() {
			if (this.totalSlides > 0) {
				this.currentIndex = (this.currentIndex + 1) % this.totalSlides;
				this.resetTimer();
			}
		},
		prev() {
			if (this.totalSlides > 0) {
				this.currentIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides;
				this.resetTimer();
			}
		},
		startTimer() {
			this.stopTimer();
			this.intervalId = setInterval(this.next, this.slideDuration);
		},
		stopTimer() {
			clearInterval(this.intervalId);
		},
		resetTimer() {
			this.stopTimer();
			this.startTimer();
		}
	},
	mounted() {
		// Передаем данные в Vuex после монтирования компонента
		this.updateLastSlider({
			url: this.$page.props.ziggy.location,
			bottom: this.$refs.sliderRef.getBoundingClientRect().bottom, // Получаем актуальное значение bottom
		});
		this.startTimer();
	},
	beforeUnmount() {
		this.stopTimer();
	},
}
</script>

<style>
.fade-enter-active,
.fade-leave-active {
	transition: all 0.5s ease;
}

.my-slider-progress-bar {
	transition: width 60ms ease;
}

.fade-enter-from,
.fade-leave-to {
	opacity: 0;
	transform: translateY(30px);
}
</style>