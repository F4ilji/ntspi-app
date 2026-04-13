<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Название процесса <span class="text-danger">*</span>
      </label>
      <input
        :value="modelValue.step_name"
        @input="update('step_name', $event.target.value)"
        type="text"
        required
        maxlength="255"
        placeholder="Процесс оформления"
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      />
    </div>

    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <label class="block text-sm font-medium text-foreground">
          Шаги <span class="text-danger">*</span>
        </label>
        <button
          type="button"
          @click="addStep"
          class="text-sm text-primary hover:text-primary-hover transition-colors"
        >
          + Добавить шаг
        </button>
      </div>

      <div
        v-for="(step, index) in modelValue.steps"
        :key="index"
        class="p-4 border border-layer-line rounded-lg space-y-2"
      >
        <div class="flex items-center gap-2 mb-2">
          <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-medium">
            {{ index + 1 }}
          </span>
          <input
            :value="step.title"
            @input="updateStep(index, 'title', $event.target.value)"
            type="text"
            required
            maxlength="255"
            placeholder="Название шага"
            class="flex-1 px-3 py-1.5 border border-layer-line rounded-lg bg-white text-foreground text-sm"
          />
          <button
            type="button"
            @click="removeStep(index)"
            class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
            :disabled="modelValue.steps.length <= 1"
            title="Удалить"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
        <textarea
          :id="`step-content-${index}-${_uid}`"
          :value="step.content"
          @input="updateStep(index, 'content', $event.target.value)"
          required
          rows="3"
          placeholder="Описание шага"
          class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground text-sm resize-y"
        ></textarea>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StepperBlock',
  props: {
    modelValue: { type: Object, required: true }
  },
  emits: ['update:modelValue', 'update'],
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
      this.$emit('update');
    },
    updateStep(index, field, value) {
      const steps = [...this.modelValue.steps];
      steps[index] = { ...steps[index], [field]: value };
      this.update('steps', steps);
    },
    addStep() {
      this.update('steps', [...this.modelValue.steps, { title: '', content: '' }]);
    },
    removeStep(index) {
      if (this.modelValue.steps.length > 1) {
        this.update('steps', this.modelValue.steps.filter((_, i) => i !== index));
      }
    }
  }
}
</script>
