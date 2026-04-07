<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Название поля <span class="text-rose-500">*</span>
      </label>
      <input
        v-model="localData.title_field"
        type="text"
        @input="emitUpdate"
        placeholder="Например: Ваше имя"
        class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Подсказка
      </label>
      <textarea
        v-model="localData.description"
        @input="emitUpdate"
        rows="2"
        placeholder="Подсказка для пользователя"
        class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm resize-none"
      ></textarea>
    </div>

    <div class="flex items-center gap-3">
      <input
        v-model="localData.rules.required"
        @change="emitUpdate"
        type="checkbox"
        class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary"
      />
      <label class="text-sm text-foreground">Обязательное поле</label>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormFieldText',
  props: {
    modelValue: { type: Object, default: () => ({}) }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      localData: { ...this.modelValue }
    }
  },
  methods: {
    emitUpdate() {
      this.$emit('update:modelValue', { ...this.localData });
    }
  }
}
</script>
