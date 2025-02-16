<template>
	<div ref="sliderRef" class="relative z-0 min-h-[calc(100vh)] items-center">
		<a :href="(!slider.settings.link_text) ? slider.link : '#'" v-for="(slider, index) in slidersCarousel.data" :class="`brightness-[${slider.image.shading}]`"
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
			<a v-if="item.settings.link_text" :href="item.link" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-white text-white hover:border-white/70 hover:text-white/70 disabled:opacity-50 disabled:pointer-events-none">
				{{ item.settings.link_text }}
			</a>
		</div>
		<div v-if="slidersCarousel.data.length >= 2" class="mx-auto max-w-screen-md px-5">
			<div class="mt-8 text-gray-500 absolute bottom-[100px]">
				<div class="">
					<div class="flex space-x-3 items-center justify-between">
						<button @click="prev" class="bg-gray-200 w-8 h-8 hover:bg-gray-300 text-gray-800 font-bold rounded-full">
							<svg class="w-4 h-4 mx-auto my-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
							</svg>
						</button>

						<div class="flex space-x-3 w-[100px] items-center font-semibold text-xl text-white">
							<span>{{ currentIndex + 1 }}</span>
							<div class="flex w-full h-1 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
								<div class="flex flex-col justify-center rounded-full overflow-hidden bg-blue-600 text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500 my-slider-progress-bar" :style="{ 'width': `${percentage}%` }"></div>
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
export default {
	name: 'slider',
	props: {
		slidersCarousel: {
			type: Object,
			required: true, // Убедитесь, что пропс передан
		},
	},
	data() {
		return {
			currentIndex: 0,
			percentage: 0,
			intervalId: null,
			slideDuration: 5000 // Продолжительность слайда в миллисекундах
		}
	},
	computed: {
		totalSlides() {
			return this.slidersCarousel && this.slidersCarousel.data ? this.slidersCarousel.data.length : 0;
		},
	},
	methods: {
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
		progressStatus() {
			if (this.percentage >= 100) {
				this.resetTimer();
				this.next();
			} else {
				this.percentage += (100 / (this.slideDuration / 60)); // Увеличиваем процент равномерно
			}
		},
		startTimer() {
			this.stopTimer(); // Останавливаем предыдущий таймер, если он есть
			this.percentage = 0; // Сбрасываем процент
			this.intervalId = setInterval(this.progressStatus, 60); // Запускаем новый таймер
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
		this.$emit('slider-mounted', this.$refs.sliderRef);
		this.startTimer();
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

Найти еще