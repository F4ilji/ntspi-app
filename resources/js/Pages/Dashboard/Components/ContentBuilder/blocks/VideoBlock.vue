<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Видеофайл <span class="text-danger">*</span>
      </label>
      <div v-if="modelValue.path" class="mb-3">
        <video :src="modelValue.path" controls class="w-full max-w-md rounded-lg border border-layer-line"></video>
      </div>
      <input
        type="file"
        @change="handleFileUpload"
        accept="video/*"
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      />
      <p class="mt-1 text-xs text-muted-foreground-1">
        mp4, mov, avi, webm, ogg
      </p>
    </div>

    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Название видео <span class="text-danger">*</span>
      </label>
      <input
        :value="modelValue.title"
        @input="update('title', $event.target.value)"
        type="text"
        required
        maxlength="255"
        placeholder="Введите название видео"
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'VideoBlock',
  props: {
    modelValue: { type: Object, required: true }
  },
  emits: ['update:modelValue', 'update'],
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
      this.$emit('update');
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.update('mime', file.type);
        this.update('path', URL.createObjectURL(file));
      }
    }
  }
}
</script>
