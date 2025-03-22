<template>
  <component
      v-for="(filter, index) in filters"
      :key="index"
      :is="getComponent(filter.type)"
      :filter="filter"
  />
</template>
<script>
import {Link} from "@inertiajs/vue3";
import SearchBadge from "@/componentss/shared/badge/badges/SearchBadge.vue";
import CategoryBadge from "@/componentss/shared/badge/badges/CategoryBadge.vue";
import TagBadge from "@/componentss/shared/badge/badges/TagBadge.vue";
import SortingByBadge from "@/componentss/shared/badge/badges/SortingByBadge.vue";
import IsOnlineBadge from "@/componentss/shared/badge/badges/IsOnlineBadge.vue";
export default {
  name: "BasicListBadge",
  components: {
    Link,
    SearchBadge,
    CategoryBadge,
    TagBadge,
    SortingByBadge,
    IsOnlineBadge
  },
  data() {
    return {
    }
  },
  methods: {
    getComponent(type) {
      const componentMap = {
        search: 'SearchBadge',
        category: 'CategoryBadge',
        tag: 'TagBadge',
        sort: 'SortingByBadge',
        is_online: 'IsOnlineBadge'
      };
      return componentMap[type] || null;
    },
  },
  props: {
    filters: {
      type: Object,
    },
  },
}

</script>


<style scoped>

</style>