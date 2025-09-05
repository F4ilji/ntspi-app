<template>
  <div class="relative z-0 min-h-screen flex flex-col">
    <div class="w-full">
      <div class="flex w-full h-1 bg-gray-200 overflow-hidden" role="progressbar"
           aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
        <div
            class="flex flex-col justify-center overflow-hidden bg-primary-light text-xs text-white text-center whitespace-nowrap transition-all duration-500 ease-in-out"
            :style="{ 'width': `${progressBarStep}%` }"></div>
      </div>
    </div>

    <!-- Основной контент, который занимает все доступное пространство -->
    <div class="flex-1 relative">
      <!-- Слайды с изображениями -->
      <a
          :href="(!slide.settings.link_text) ? slide.link : '#'"
          v-for="(slide, index) in sortedSlides"
          :key="index"
          :style="{ filter: `brightness(${slide.image.shading})` }"
          class="absolute -z-10 h-full w-full before:absolute before:z-10 before:h-full before:w-full"
      >
        <img
            alt="Thumbnail"
            loading="eager"
            decoding="async"
            data-nimg="fill"
            class="object-cover transition-opacity duration-1000 absolute inset-0 h-full w-full"
            sizes="100vw"
            :src="'/storage/' + slide.image.url"
            :loading="index === 0 ? 'eager' : 'lazy'"
            :class="{ 'opacity-1': currentIndex === index, 'opacity-0': currentIndex !== index }"
        />
      </a>

      <!-- Текст слайдов с анимацией -->
      <transition name="fade" mode="out-in">
        <div :key="currentIndex">
          <div
              v-show="currentIndex === index"
              v-for="(item, index) in sortedSlides"
              :key="index"
              class="mx-auto max-w-screen-lg px-5 pt-[150px] pb-0 bvi-no-styles"
          >
            <h1
                v-if="item.title"
                :class="`text-${item.settings.text_position}`"
                class="text-brand-primary mb-3 mt-2 text-3xl font-semibold tracking-tight text-white lg:text-7xl lg:leading-tight"
            >
              {{ item.title }}
            </h1>
            <div v-if="item.content" class="mt-8 space-x-3 text-gray-500 mb-8">
              <div class="gap-3 md:flex-row md:items-center">
                <div class="gap-3">
                  <p :class="`text-${item.settings.text_position}`" class="text-gray-100 text-4xl font-light line-clamp-3">
                    {{ item.content }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>

  </div>
</template>

<script>
import {mapActions} from "vuex";
import axios from "axios";
import {helpers} from "@/mixins/Helpers.js";

export default {
  mixins: [helpers],
  name: 'TvSliderBody',
  props: {
    slides: {
      type: Object,
    }
  },
  data() {
    return {
      currentIndex: 0,
      intervalId: null,
      reloadIntervalId: null,
      slideDuration: 9000,
      reloadDuration: 600000, // 10 минут в миллисекундах
      sortedSlides: null,

    }
  },
  computed: {
    totalSlides() {
      return this.slides && this.slides ? this.slides.length : 0;
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
    },
    startReloadTimer() {
      this.stopReloadTimer();
      this.reloadIntervalId = setInterval(() => {
        window.location.reload();
      }, this.reloadDuration);
    },
    stopReloadTimer() {
      clearInterval(this.reloadIntervalId);
    },
  },
  mounted() {
    this.sortedSlides = this.slides.slice().sort((a, b) => a.sort - b.sort);

    this.startTimer();
    this.startReloadTimer();
  },
  beforeUnmount() {
    this.stopTimer();
    this.stopReloadTimer();
  },
}
</script>

<style>
/* Стили для изображений слайдов */
.opacity-1 {
  opacity: 1;
}

.opacity-0 {
  opacity: 0;
}
</style>