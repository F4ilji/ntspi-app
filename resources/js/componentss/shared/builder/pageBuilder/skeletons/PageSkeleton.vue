<template>
  <div class="space-y-2.5 animate-pulse w-full">
    <div v-for="slide in 16" :key="slide" class="flex items-center w-full">
      <div
          class="h-2.5 rounded-full"
          :class="getRandomBgColor()"
          :style="{ width: getRandomInt(20, 80) + '%' }"
      ></div>
      <div
          class="h-2.5 ms-2 rounded-full"
          :class="getRandomBgColor()"
          :style="{ width: getRandomInt(10, 40) + '%' }"
      ></div>
      <div
          class="h-2.5 ms-2 rounded-full"
          :class="getRandomBgColor()"
          :style="{ width: getRandomInt(10, 40) + '%' }"
      ></div>
    </div>
  </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
  name: "PageSkeleton",
  components: { Link },
  props: {
    header: {
      type: String,
    },
  },
  methods: {
    // Функция для генерации случайного числа в диапазоне
    getRandomInt(min, max) {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    // Функция для случайного выбора цвета фона
    getRandomBgColor() {
      const colors = ["bg-gray-100", "bg-gray-200", "bg-gray-300"];
      return colors[this.getRandomInt(0, colors.length - 1)];
    },
  },
};
</script>

<style scoped>
/* Ваши стили, если нужно */
</style>