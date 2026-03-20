<template>
  <div v-if="loading" class="relative z-0 min-h-[calc(100vh)] items-center">
    <div class="group block rounded-xl overflow-hidden animate-pulse min-h-[calc(100vh)] bg-gray-200"></div>
  </div>
  <div v-else>
    <SliderBlockBody :slides="slider.slides" />
  </div>
</template>

<script>
import axios from "axios";
import SliderBlockBody from "@/componentss/features/sliders/components/SliderBlockBody.vue";

export default {
  name: 'SliderBlock',
  components: {SliderBlockBody},
  props: {
    block: {
      type: Object,
    },
  },
  data() {
    return {
      loading: true,
      slider: null,
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
    getSlider(id) {
      axios.get(route('client.widget.slider.show', id))
          .then(response => {
            this.slider = response.data;
            this.loading = false; // Установить состояние загрузки в false
          })
          .catch(error => {
            console.error('Ошибка:', error);
          });
    },

  },
  mounted() {
    const id = this.block?.data.slider
    this.getSlider(id)
  },
  beforeUnmount() {
  },
}
</script>

<style>

</style>