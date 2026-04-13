<template>
  <div class="space-y-4">
    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium text-foreground mb-1">
          ФИО <span class="text-danger">*</span>
        </label>
        <input
          :value="modelValue.name"
          @input="update('name', $event.target.value)"
          type="text"
          required
          maxlength="255"
          placeholder="Иванов Иван Иванович"
          class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-foreground mb-1">
          Фото
        </label>
        <input
          type="file"
          @change="handlePhotoUpload"
          accept="image/*"
          class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
        />
      </div>
    </div>

    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <label class="block text-sm font-medium text-foreground">
          Информация <span class="text-danger">*</span>
        </label>
        <button
          type="button"
          @click="addInfoRow"
          class="text-sm text-primary hover:text-primary-hover transition-colors"
        >
          + Добавить
        </button>
      </div>

      <div
        v-for="(info, index) in modelValue.info"
        :key="index"
        class="grid grid-cols-2 gap-2 p-3 border border-layer-line rounded-lg"
      >
        <input
          :value="info.column"
          @input="updateInfo(index, 'column', $event.target.value)"
          type="text"
          required
          placeholder="Должность"
          class="px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground text-sm"
        />
        <div class="flex gap-2">
          <textarea
            :value="info.content"
            @input="updateInfo(index, 'content', $event.target.value)"
            required
            maxlength="1000"
            placeholder="Описание"
            rows="2"
            class="flex-1 px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground text-sm resize-none"
          ></textarea>
          <button
            type="button"
            @click="removeInfo(index)"
            class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all self-center"
            title="Удалить"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PersonBlock',
  props: {
    modelValue: { type: Object, required: true }
  },
  emits: ['update:modelValue', 'update'],
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
      this.$emit('update');
    },
    updateInfo(index, field, value) {
      const info = [...this.modelValue.info];
      info[index] = { ...info[index], [field]: value };
      this.update('info', info);
    },
    addInfoRow() {
      this.update('info', [...this.modelValue.info, { column: '', content: '' }]);
    },
    removeInfo(index) {
      if (this.modelValue.info.length > 1) {
        this.update('info', this.modelValue.info.filter((_, i) => i !== index));
      }
    },
    handlePhotoUpload(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => this.update('photo', e.target.result);
        reader.readAsDataURL(file);
      }
    }
  }
}
</script>
