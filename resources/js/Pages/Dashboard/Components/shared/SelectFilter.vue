<template>
  <div>
    <label :for="id" class="block text-xs font-medium text-muted-foreground-1 mb-1.5">
      {{ label }}
    </label>
    <select
      :id="id"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value); $emit('change')"
      class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
    >
      <option value="">{{ placeholder }}</option>
      <slot></slot>
    </select>
  </div>
</template>

<script>
export default {
  name: 'SelectFilter',
  props: {
    modelValue: {
      type: [String, Number],
      default: ''
    },
    label: {
      type: String,
      default: 'Фильтр'
    },
    placeholder: {
      type: String,
      default: 'Все'
    },
    id: {
      type: String,
      default: 'filter'
    }
  },
  emits: ['update:modelValue', 'change']
}
</script>
