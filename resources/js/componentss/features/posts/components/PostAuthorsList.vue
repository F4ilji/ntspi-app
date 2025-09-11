<template>
  <dl v-if="!(authors[0] === 'Без автора' && null)" class="flex flex-col sm:flex-row gap-1 items-baseline">
    <dt class="min-w-40">
      <h3 class="block text-sm text-gray-500 m-0 underline">Над статьей работали:</h3>
    </dt>
    <dd class="m-0">
      <ul class="m-0 inline-flex flex-wrap items-center">
        <li
            v-for="(author, index) in authors"
            :key="index"
            class="me-1 after:content-[','] last:after:content-none inline-flex items-center text-sm text-gray-800 dark:text-neutral-200"
        >
          {{ author }}
        </li>
      </ul>
    </dd>
  </dl>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "PostAuthorsList",
	components: {Link},
	data() {
		return {
		}
	},
	methods: {
		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText
				LimitedText = text.substring(0, symbols)
				return LimitedText + "..."
			}
			return text
		},
	},

	props: {
		authors: {
			type: Object,
		},
	},
}

</script>


<style scoped>

</style>