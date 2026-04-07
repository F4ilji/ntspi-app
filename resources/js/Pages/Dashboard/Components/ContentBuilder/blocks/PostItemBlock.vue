<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Новость <span class="text-danger">*</span>
      </label>
      <select
        :value="modelValue.post"
        @change="update('post', parseInt($event.target.value))"
        required
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
      >
        <option :value="null">Выберите новость...</option>
        <option v-for="post in posts" :key="post.id" :value="post.id">
          {{ post.title }}
        </option>
      </select>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PostItemBlock',
  props: { modelValue: { type: Object, required: true } },
  emits: ['update:modelValue'],
  data() { return { posts: [] }; },
  async mounted() {
    try {
      const response = await fetch('/api/dashboard/posts/published?limit=100');
      this.posts = await response.json();
    } catch (e) { console.error('Failed to load posts:', e); }
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
    }
  }
}
</script>
