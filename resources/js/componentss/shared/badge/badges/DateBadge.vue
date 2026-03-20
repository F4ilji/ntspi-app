<template>
    <span v-if="this.filter.value !== null" class="inline-flex items-center gap-x-1.5 py-1.5 ps-3 pe-2 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
        Дата: {{ firstDate }} {{ secondDate }}
        <button @click.prevent="clearFilter" type="button" class="shrink-0 size-4 inline-flex items-center justify-center rounded-full hover:bg-primary-lighter focus:outline-none focus:bg-blue-200 focus:text-blue-500">
            <span class="sr-only">Remove badge</span>
            <svg class="shrink-0 size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
        </button>
    </span>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import slugify from "slugify";

export default {
	name: "DateBadge",
	components: { Link },
	data() {
		return {};
	},
  computed: {
    firstDate() {
      return this.filter.value[0]
    },
    secondDate() {
      return this.filter.value[1] ? "- " + this.filter.value[1] : ""
    },
  },
	methods: {
		clearFilter() {
			let url = new URL(window.location.href);

			// Создаем массив для хранения всех ключей, которые нужно удалить
			const keysToDelete = [];

			// Перебираем все параметры и добавляем ключи, начинающиеся с 'category', в массив
			for (const [key] of url.searchParams) {
				if (key.startsWith(this.filter.param)) {
					keysToDelete.push(key);
				}
			}

			// Удаляем все ключи из массива
			keysToDelete.forEach(key => url.searchParams.delete(key));

			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
			});
		},
	},
	props: {
		filter: {
			type: Object,
		},
	},
}
</script>

<style scoped>
</style>