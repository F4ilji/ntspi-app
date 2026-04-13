<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Заголовок <span class="text-danger">*</span>
      </label>
      <input
        :value="modelValue.content"
        @input="update('content', $event.target.value)"
        type="text"
        required
        maxlength="255"
        placeholder="Введите заголовок раздела"
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Якорь (ID)
      </label>
      <input
        :value="modelValue.id"
        @input="update('id', $event.target.value)"
        type="text"
        placeholder="anchor-link-..."
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-muted text-muted-foreground-1 text-sm"
        readonly
      />
      <p class="mt-1 text-xs text-muted-foreground-1">Генерируется автоматически</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HeadingBlock',
  props: {
    modelValue: {
      type: Object,
      required: true
    }
  },
  emits: ['update:modelValue', 'update'],
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', {
        ...this.modelValue,
        [field]: value
      });
      this.$emit('update');
    }
  }
}
</script>
