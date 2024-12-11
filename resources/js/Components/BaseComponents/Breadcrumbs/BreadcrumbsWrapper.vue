<template>
	<ol class="flex items-center whitespace-normal min-w-0 flex-nowrap hide-scrollbar overflow-x-scroll" aria-label="Breadcrumb">
		<slot />
	</ol>
</template>

<script>
import slugify from "slugify";
import icons from "@/Components/other/icons.js";
import {Link} from "@inertiajs/vue3";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";

export default {
	name: "BreadcrumbsWrapper",
	data() {
		return {
		}
	},
	methods: {
	},
}
</script>

<style scoped>

/* Скрытие горизонтальной полосы прокрутки */
.hide-scrollbar {
	overflow-x: scroll; /* или auto, в зависимости от вашего случая */
}

.hide-scrollbar::-webkit-scrollbar {
	display: none; /* Для WebKit-браузеров (Chrome, Safari) */
}

.hide-scrollbar {
	-ms-overflow-style: none;  /* Для IE и Edge */
	scrollbar-width: none;  /* Для Firefox */
}

</style>