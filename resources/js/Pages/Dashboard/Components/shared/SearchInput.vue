<template>
  <div>
    <label :for="id" class="block text-xs font-medium text-muted-foreground-1 mb-1.5">
      {{ label }}
    </label>
    <div class="relative">
      <input
        :id="id"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        @keyup.enter="$emit('search')"
        type="text"
        :placeholder="placeholder"
        class="w-full pl-9 pr-4 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
      />
      <svg class="absolute left-3 top-2.5 w-4 h-4 text-muted-foreground-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SearchInput',
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    label: {
      type: String,
      default: 'Поиск'
    },
    placeholder: {
      type: String,
      default: 'Введите текст...'
    },
    id: {
      type: String,
      default: 'search'
    }
  },
  emits: ['update:modelValue', 'search']
}
</script>
