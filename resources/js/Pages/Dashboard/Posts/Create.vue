<template>
  <PostForm
    :categories="categories"
    :sliders="sliders"
    :statuses="statuses"
  />
</template>

<script>
import PostForm from './Form.vue';

export default {
  name: 'CreatePost',
  components: { PostForm },

  props: {
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
    this.SET_DOCUMENT_TITLE('Создание новости');
  }
}
</script>
