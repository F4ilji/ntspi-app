<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Список ресурсов <span class="text-danger">*</span>
      </label>
      <select
        :value="modelValue.resource"
        @change="update('resource', $event.target.value)"
        required
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      >
        <option value="">Выберите ресурс...</option>
        <option v-for="resource in resources" :key="resource.slug" :value="resource.slug">
          {{ resource.title }}
        </option>
      </select>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PageResourceListBlock',
  props: { modelValue: { type: Object, required: true } },
  emits: ['update:modelValue', 'update'],
  data() { return { resources: [] }; },
  async mounted() {
    try {
      const response = await fetch('/api/dashboard/page-reference-lists/active');
      this.resources = await response.json();
    } catch (e) { console.error('Failed to load resources:', e); }
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
      this.$emit('update');
    }
  }
}
</script>
