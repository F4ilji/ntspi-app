<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Страница <span class="text-danger">*</span>
      </label>
      <select
        :value="modelValue.page"
        @change="update('page', parseInt($event.target.value))"
        required
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      >
        <option :value="null">Выберите страницу...</option>
        <option v-for="page in pages" :key="page.id" :value="page.id">
          {{ page.title }}
        </option>
      </select>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PageItemBlock',
  props: { modelValue: { type: Object, required: true } },
  emits: ['update:modelValue', 'update'],
  data() { return { pages: [] }; },
  async mounted() {
    try {
      const response = await fetch('/api/dashboard/pages/visible');
      this.pages = await response.json();
    } catch (e) { console.error('Failed to load pages:', e); }
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
      this.$emit('update');
    }
  }
}
</script>
