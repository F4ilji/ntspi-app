<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Контактный виджет <span class="text-danger">*</span>
      </label>
      <select
        :value="modelValue.contact"
        @change="update('contact', $event.target.value)"
        required
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      >
        <option value="">Выберите контакт...</option>
        <option v-for="contact in contacts" :key="contact.slug" :value="contact.slug">
          {{ contact.title }}
        </option>
      </select>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContactBlock',
  props: { modelValue: { type: Object, required: true } },
  emits: ['update:modelValue'],
  data() { return { contacts: [] }; },
  async mounted() {
    try {
      const response = await fetch('/api/dashboard/contact-widgets/active');
      this.contacts = await response.json();
    } catch (e) { console.error('Failed to load contacts:', e); }
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
    }
  }
}
</script>
