<template>
  <div class="space-y-3">
    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium text-foreground mb-1">
          Количество
        </label>
        <input
          :value="modelValue.count"
          @input="update('count', parseInt($event.target.value) || 5)"
          type="number"
          min="1"
          max="20"
          class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-foreground mb-1">
          Категория
        </label>
        <select
          :value="modelValue.category"
          @change="update('category', $event.target.value ? parseInt($event.target.value) : null)"
          class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        >
          <option :value="null">Все категории</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.title }}
          </option>
        </select>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PostListBlock',
  props: { modelValue: { type: Object, required: true } },
  emits: ['update:modelValue'],
  data() { return { categories: [] }; },
  async mounted() {
    try {
      const response = await fetch('/api/dashboard/categories');
      this.categories = await response.json();
    } catch (e) { console.error('Failed to load categories:', e); }
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
    }
  }
}
</script>
