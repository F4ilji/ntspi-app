<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Слайдер <span class="text-danger">*</span>
      </label>
      <select
        :value="modelValue.slider"
        @change="update('slider', $event.target.value)"
        required
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      >
        <option value="">Выберите слайдер...</option>
        <option v-for="slider in sliders" :key="slider.slug" :value="slider.slug">
          {{ slider.title }}
        </option>
      </select>
      <p class="mt-1 text-xs text-muted-foreground-1">
        Только активные слайдеры с изображениями
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SliderBlock',
  props: {
    modelValue: { type: Object, required: true }
  },
  emits: ['update:modelValue'],
  data() {
    return { sliders: [] };
  },
  async mounted() {
    // Загрузка слайдеров с бэкенда
    try {
      const response = await fetch('/api/dashboard/sliders/active');
      this.sliders = await response.json();
    } catch (e) {
      console.error('Failed to load sliders:', e);
    }
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
    }
  }
}
</script>
