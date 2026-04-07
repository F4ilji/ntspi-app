<template>
  <PostForm
    :post="post"
    :categories="categories"
    :sliders="sliders"
    :statuses="statuses"
  />
</template>

<script>
import PostForm from './Form.vue';

export default {
  name: 'EditPost',
  components: { PostForm },

  props: {
    post: {
      type: Object,
      required: true
    },
    categories: {
      type: Array,
      required: true
    },
    sliders: {
      type: Array,
      required: true
    },
    statuses: {
      type: Array,
      required: true
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование новости - ${this.post.title}`);
  }
}
</script>
