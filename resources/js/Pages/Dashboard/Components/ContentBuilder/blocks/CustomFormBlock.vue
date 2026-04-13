<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Форма <span class="text-danger">*</span>
      </label>
      <select
        :value="modelValue.form"
        @change="update('form', $event.target.value)"
        required
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      >
        <option value="">Выберите форму...</option>
        <option v-for="form in forms" :key="form.form_id" :value="form.form_id">
          {{ form.title }}
        </option>
      </select>
    </div>

    <div class="flex items-center gap-2">
      <input
        type="checkbox"
        :checked="modelValue.settings.in_modal"
        @change="updateSettings('in_modal', $event.target.checked)"
        class="h-4 w-4 text-primary focus:ring-primary border-layer-line rounded"
      />
      <label class="text-sm text-foreground">
        Открывать в модальном окне
      </label>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CustomFormBlock',
  props: { modelValue: { type: Object, required: true } },
  emits: ['update:modelValue', 'update'],
  data() { return { forms: [] }; },
  async mounted() {
    try {
      const response = await fetch('/api/dashboard/custom-forms/published');
      this.forms = await response.json();
    } catch (e) { console.error('Failed to load forms:', e); }
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
      this.$emit('update');
    },
    updateSettings(field, value) {
      this.update('settings', { ...this.modelValue.settings, [field]: value });
    }
  }
}
</script>
