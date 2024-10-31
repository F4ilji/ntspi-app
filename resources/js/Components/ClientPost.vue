<template>
                    <div class="group cursor-pointer">
                      <div
                          class="overflow-hidden rounded-md max-h-[250px] bg-gray-100 transition-all hover:scale-105 dark:bg-gray-800"
                      >
                        <a
                            class="relative block aspect-square"
                            :href="route('client.post.show', post.slug)"
                        ><img
                            alt="Thumbnail"
                            loading="lazy"
                            decoding="async"
                            data-nimg="fill"
                            class="object-cover transition-all"
                            style="
                              position: absolute;
                              height: 100%;
                              width: 100%;
                              inset: 0px;
                              color: transparent;
                            "
                            sizes="(max-width: 768px) 30vw, 33vw"
														:src="post.preview ? '/storage/' + post.preview : '/img/thumbnail-1.png'"
                        /></a>
                      </div>
                      <div class="">
                        <div>
                          <div v-if="post.category" class="flex gap-3">
                            <a :href="route('client.post.index', { 'category[]': post.category.slug })">
                                <span class="inline-block text-xs font-medium tracking-wider uppercase mt-5 text-blue-600">
                                    {{ post.category ? post.category.title : "Новости" }}
                                </span>
                            </a>
                          </div>
													<div v-else class="flex gap-3">
														<span>
                                <span class="inline-block text-xs font-medium tracking-wider uppercase mt-5 text-blue-600">
                                    Новости
                                </span>
														</span>
													</div>
                          <h2
                              class="text-lg font-semibold leading-snug tracking-tight mt-2 dark:text-white"
                          >
                            <a :href="route('client.post.show', post.slug)"
                            ><span class="duration-200 group-hover:text-gray-500">{{
                                post.title
                              }}</span></a
                            >
                          </h2>
                          <div class="">
                            <p
                                class="mt-2 line-clamp-3 text-sm text-gray-500 dark:text-gray-400"
                            >
                              <a
                                  :href="route('client.post.show', post.slug)"
                              >{{ post.preview_text }}
                              </a>
                            </p>
                          </div>
                          <div
                              class="mt-3 flex items-center space-x-3 text-gray-500 dark:text-gray-400"
                          >
                            <span>
                              <div class="flex items-center gap-3">
                                <span v-if="post" class="truncate text-sm">{{ textLimit(post.authors[0], 20) }}</span>
                              </div>
                            </span>
                            <span class="text-xs text-gray-300 dark:text-gray-600"
                            >•</span
                            >
                            <span class="truncate text-sm">{{ post.created_post }}</span>
                          </div>
                        </div>
                      </div>
                    </div>


</template>

<script>

export default {
	name: "ClientPost",
    	data() {
		return {
		}
	},
	methods: {
    textLimit(text, symbols) {
      if (text.length > symbols) {
        let LimitedText;
        LimitedText = text.substring(0, symbols);
        return LimitedText + "...";
      }
      return text;
    },
	},

  props: {
    post: {
        type: Object,
    },
  },
}
</script>

<style scoped>

</style>