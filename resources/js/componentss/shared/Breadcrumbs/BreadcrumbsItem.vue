<template>
  <li class="text-sm">
    <Link :href="url" class="flex items-center text-gray-500 hover:text-primary-light">
      <BasicIcon v-if="separator" class="flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400" name="breadcrumb-tag" />
      {{ title }}
    </Link>
  </li>
</template>

<script>
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {Link} from "@inertiajs/vue3";

export default {
  name: "BreadcrumbsItem",
  props: {
    title: {
      type: String,
      required: true
    },
    url: {
      type: String,
      // default: this.$page.url
    },
    separator: {
      type: Boolean,
      default: true
    },
  },
  components: {
    BasicIcon,
    Link,
  },
}

</script>
