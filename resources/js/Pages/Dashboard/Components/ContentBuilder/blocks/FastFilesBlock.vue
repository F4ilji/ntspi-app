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

    <!-- Upload Progress -->
    <div v-if="uploading" class="flex items-center gap-2 p-3 bg-primary/5 border border-primary/20 rounded-lg">
      <svg class="animate-spin h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
      <span class="text-sm text-primary">Загрузка файлов...</span>
    </div>

    <input
      type="file"
      @change="handleFilesUpload"
      :disabled="uploading"
      accept=".pdf,.docx,.xlsx,.pptx,.zip"
      multiple
      class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
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
  emits: ['update:modelValue', 'update'],
  data() {
    return {
      uploading: false
    };
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
      this.$emit('update');
    },
    removeFile(index) {
      this.update('path', this.modelValue.path.filter((_, i) => i !== index));
    },
    async handleFilesUpload(event) {
      const files = Array.from(event.target.files);
      if (files.length === 0) return;

      // Reset input to allow selecting same files again
      event.target.value = '';

      await this.uploadFilesToServer(files);
    },
    async uploadFilesToServer(files) {
      if (files.length === 0) return;

      this.uploading = true;

      try {
        const formData = new FormData();
        files.forEach(file => {
          formData.append('files[]', file);
        });

        const response = await fetch(route('dashboard.pages.upload-files'), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'Accept': 'application/json',
          },
          body: formData,
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success && result.files) {
          const newPaths = result.files.map(file => file.path);
          this.update('path', [...this.modelValue.path, ...newPaths]);
        } else {
          console.error('File upload failed:', result.error);
        }
      } catch (error) {
        console.error('File upload error:', error);
      } finally {
        this.uploading = false;
      }
    }
  }
}
</script>
