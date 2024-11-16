<template>

	<div class="flex flex-col rounded-xl p-4 md:p-6 bg-white border border-gray-200 dark:bg-slate-900 dark:border-gray-700">
		<div class="flex items-center gap-y-4 gap-x-4 md:flex-nowrap">
			<img v-if="teacher.photo" @click="toggler = !toggler" loading="lazy" class="rounded-xl md:w-[150px]" :src="'/storage/' + teacher.photo" alt="Image Description">
			<div class="grow">
				<Link :href="route('client.person.show', teacher.slug)" class="font-medium text-gray-800 hover:text-gray-500 underline">
					{{ teacher.position }}: {{ teacher.name }}
				</Link>
				<p v-if="teacher.academicTitle !== null" class="text-xs text-gray-500 mt-2">
					Ученая степень: {{ teacher.academicTitle }}
				</p>
				<p class="text-xs text-gray-500 mt-2">
					Контактный телефон: {{ teacher.contactPhone }}
				</p>
				<p class="text-xs text-gray-500 mt-2">
					Электронная почта: {{ teacher.contactEmail }}
				</p>
				<p class="text-xs text-gray-500 mt-2">
					Кабинет: 207В
				</p>
			</div>
		</div>
	</div>

	<FsLightbox class="" :toggler="toggler" :sources="['/storage/' + teacher.photo]"/>




</template>


<script>

import {Head, Link} from "@inertiajs/vue3";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import MainNavbar from "@/Navbars/MainNavbar.vue";
import FsLightbox from "fslightbox-vue/v3";

export default {
	name: "ClientDepartmentTeacherCard",
	data() {
		return {
			toggler: false,
		}
	},

	props: {
		teacher: {
			type: Object,
		},
	},

	components: {
		ClientFooterDown,
		MainNavbar,
		Link,
		FsLightbox,
		Head
	},

}
</script>


<style scoped>

</style>