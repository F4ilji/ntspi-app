<template>
  <select v-model="faculty" @change="filter" class="py-2 px-4 pe-9 block w-full border-gray-200 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
    <option :value="null">
      Выбрать факультет
    </option>
    <option v-for="facultyOption in faculties" :value="facultyOption.slug">{{ facultyOption.title }}</option>
  </select>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {debounce} from "lodash";

export default {
  name: "FacultyFilter",
  components: {
    BasicIcon,
    Link,
  },
  props: {
    faculties: {
      type: Object,
      required: true,
    },
    faculty_filter: {
      type: Object,
      default: () => ({ value: null }),
    },
  },
  data() {
    return {
      faculty: this.faculty_filter.value || null,
    }
  },
  methods: {
    filter: debounce(function () {
      let url = new URL(window.location.href);
      url.searchParams.delete('page');
      url.searchParams.delete('form');

      if (this.faculty === null) {
        url.searchParams.delete('faculty');
      } else {
        url.searchParams.set('faculty', this.faculty);
      }

      let newUrl = url.toString();

      this.$inertia.visit(newUrl, {
        method: 'get',
        preserveState: true,
      });
    }, 500),
  },
};
</script>

<style scoped>

</style>