<template>
  <div class="flex items-center gap-2">
    <div class="flex bg-layer border border-layer-line rounded-lg p-0.5">
      <button
        v-for="preset in presets"
        :key="preset.value"
        @click="selectPeriod(preset.value)"
        class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
        :class="modelValue === preset.value
          ? 'bg-primary text-white'
          : 'text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover'"
      >
        {{ preset.label }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AnalyticsPeriodFilter',
  props: {
    modelValue: {
      type: Number,
      default: 30,
    },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      presets: [
        { label: 'Сегодня', value: 0 },
        { label: '7 дней', value: 7 },
        { label: '30 дней', value: 30 },
        { label: 'Все', value: 365 },
      ],
    };
  },
  methods: {
    selectPeriod(value) {
      this.$emit('update:modelValue', value);
    },
  },
};
</script>
