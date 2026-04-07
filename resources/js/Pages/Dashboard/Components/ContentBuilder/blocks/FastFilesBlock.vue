<template>
  <div class="space-y-3">
    <div class="space-y-2">
      <div
        v-for="(path, index) in modelValue.path"
        :key="index"
        class="flex items-center gap-3 p-3 border border-layer-line rounded-lg"
      >
        <div class="flex-1 text-sm text-foreground truncate">
          {{ path.split('/').pop() || path }}
        </div>
        <button
          type="button"
          @click="removeFile(index)"
          class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
          title="Удалить"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>

    <input
      type="file"
      @change="handleFilesUpload"
      accept=".pdf,.docx,.xlsx,.pptx,.zip"
      multiple
      class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
    />
    <p class="text-xs text-muted-foreground-1">
      PDF, DOCX, XLSX, PPTX, ZIP
    </p>
  </div>
</template>

<script>
export default {
  name: 'FastFilesBlock',
  props: {
    modelValue: { type: Object, required: true }
  },
  emits: ['update:modelValue'],
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
    },
    removeFile(index) {
      this.update('path', this.modelValue.path.filter((_, i) => i !== index));
    },
    handleFilesUpload(event) {
      const files = Array.from(event.target.files);
      const newPaths = [...this.modelValue.path];
      files.forEach(file => {
        newPaths.push(URL.createObjectURL(file));
      });
      this.update('path', newPaths);
    }
  }
}
</script>
