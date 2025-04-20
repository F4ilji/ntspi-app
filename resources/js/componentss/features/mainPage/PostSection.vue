<template>
  <h2 class="text-brand-primary my-6 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight bvi-show">Последние новости</h2>
  <div class="grid gap-10 pb-10 md:grid-cols-2 lg:gap-10 xl:grid-cols-3 bvi-no-styles">
    <template v-for="post in posts.data" :key="post.id">
      <PostListItem :post="post" />
    </template>
  </div>
  <div class="flex justify-center">
    <a :href="route('client.post.index')" class="group mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-primary">
      Все новости
      <svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </a>
  </div>
</template>

<script>
import { debounce } from "lodash";
import {Link} from "@inertiajs/vue3";
import PostListItem from "@/componentss/features/posts/components/PostListItem.vue";

export default {
	name: "PostSection",
  components: {PostListItem, Link},
	props: {
    posts: {
      type: Object,
      required: true
    }
  },

}
</script>

<style scoped>

</style>