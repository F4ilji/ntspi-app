<template>
  <div v-if="loading" class="relative z-0 min-h-[calc(100vh)] items-center">
    <div class="group block rounded-xl overflow-hidden animate-pulse min-h-[calc(100vh)] bg-gray-200"></div>
  </div>

  <transition v-else name="fade">
    <SliderBody :slides="slider.slides" />
  </transition>
</template>

<script>
import axios from "axios";
import SliderBody from "@/componentss/features/sliders/components/SliderBody.vue";

export default {
  name: 'Slider',
  components: {SliderBody},
  props: {
    sliderId: {
      type: String,
      required: true,
    }
  },
  data() {
    return {
      loading: true,
      slider: null,
    }
  },
  methods: {
    getSlider(id) {
      axios.get(route('client.widget.slider.show', id))
          .then(response => {
            this.slider = response.data;
            this.loading = false;
          })
          .catch(error => {
            console.error('Ошибка:', error);
          });
    },
  },
  mounted() {
    const id = this.sliderId
    this.getSlider(id)
  },
}
</script>

<style>
/* Анимация появления */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>