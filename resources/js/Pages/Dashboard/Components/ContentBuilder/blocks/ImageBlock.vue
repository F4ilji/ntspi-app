<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Изображение <span class="text-danger">*</span>
      </label>
      <div class="flex items-center gap-3">
        <div v-if="modelValue.url" class="relative">
          <img :src="RESOLVE_ASSET_URL(modelValue.url)" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-layer-line" />
          <button
            type="button"
            @click="update('url', '')"
            class="absolute -top-2 -right-2 p-1 bg-danger text-white rounded-full hover:bg-danger-hover transition-colors"
            title="Удалить"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="flex-1">
          <input
            type="file"
            ref="fileInput"
            @change="handleFileUpload"
            accept="image/*"
            class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          />
          <p v-if="uploading" class="mt-1 text-xs text-primary">Загрузка...</p>
          <p v-else class="mt-1 text-xs text-muted-foreground-1">
            Загрузите изображение (JPG, PNG, WebP)
          </p>
        </div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Alt текст
      </label>
      <input
        :value="modelValue.alt"
        @input="update('alt', $event.target.value)"
        type="text"
        placeholder="Описание изображения"
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'ImageBlock',
  props: {
    modelValue: {
      type: Object,
      required: true
    }
  },
  emits: ['update:modelValue', 'update'],
  data() {
    return {
      uploading: false,
    };
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', {
        ...this.modelValue,
        [field]: value
      });
      this.$emit('update');
    },
    async handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      this.uploading = true;

      try {
        const formData = new FormData();
        formData.append('images[]', file);

        const response = await fetch(route('dashboard.posts.upload-images'), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: formData,
        });

        const result = await response.json();

        if (result.success && result.paths && result.paths.length > 0) {
          this.update('url', result.paths[0]);
        }
      } catch (error) {
        console.error('Image upload error:', error);
      } finally {
        this.uploading = false;
        // Reset input so the same file can be selected again
        event.target.value = '';
      }
    }
  }
}
</script>
