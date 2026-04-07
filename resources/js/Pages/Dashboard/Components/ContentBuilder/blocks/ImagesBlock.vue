<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Изображения <span class="text-danger">*</span> (макс. 5)
      </label>
      <div class="grid grid-cols-3 gap-3 mb-3">
        <div
          v-for="(url, index) in modelValue.url"
          :key="index"
          class="relative group"
        >
          <img :src="url" alt="Preview" class="w-full h-24 object-cover rounded-lg border border-layer-line" />
          <button
            type="button"
            @click="removeImage(index)"
            class="absolute -top-2 -right-2 p-1 bg-danger text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
            title="Удалить"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <input
        type="file"
        @change="handleFilesUpload"
        accept="image/*"
        multiple
        :disabled="modelValue.url.length >= 5"
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
      />
      <p class="mt-1 text-xs text-muted-foreground-1">
        Можно загрузить до 5 изображений (JPG, PNG, WebP)
      </p>
    </div>

    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Alt текст
      </label>
      <input
        :value="modelValue.alt"
        @input="update('alt', $event.target.value)"
        type="text"
        placeholder="Описание изображений"
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'ImagesBlock',
  props: {
    modelValue: {
      type: Object,
      required: true
    }
  },
  emits: ['update:modelValue'],
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', {
        ...this.modelValue,
        [field]: value
      });
    },
    handleFilesUpload(event) {
      const files = Array.from(event.target.files);
      const remaining = 5 - this.modelValue.url.length;
      const toProcess = files.slice(0, remaining);

      let processed = 0;
      toProcess.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
          const newUrls = [...this.modelValue.url, e.target.result];
          this.update('url', newUrls);
          processed++;
        };
        reader.readAsDataURL(file);
      });
    },
    removeImage(index) {
      const newUrls = this.modelValue.url.filter((_, i) => i !== index);
      this.update('url', newUrls);
    }
  }
}
</script>
