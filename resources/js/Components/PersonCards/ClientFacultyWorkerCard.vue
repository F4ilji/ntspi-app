<template>

	<div class="flex flex-col rounded-xl p-4 md:p-6 bg-white border border-gray-200 dark:bg-slate-900 dark:border-gray-700">
		<div class="flex items-center gap-y-4 gap-x-4 flex-wrap md:flex-nowrap">
			<img v-if="worker.photo" @click="toggler = !toggler" loading="lazy" class="rounded-xl md:w-[150px]" :src="'/storage/' + worker.photo" alt="Image Description">
			<div class="grow">
				<Link :href="route('client.person.show', worker.slug)" class="font-medium text-gray-800 hover:text-gray-500 underline">
					{{ worker.position }}: {{ worker.name }}
				</Link>
				<p v-if="worker.academicTitle !== null" class="text-xs text-gray-500 mt-2">
					Ученая степень: {{ worker.academicTitle }}
				</p>
				<p v-if="worker.contactPhone" class="text-xs text-gray-500 mt-2">
					Контактный телефон: {{ worker.contactPhone }}
				</p>
				<p v-if="worker.contactEmail" class="text-xs text-gray-500 mt-2">
					Электронная почта: {{ worker.contactEmail }}
				</p>
				<p v-if="worker.cabinet" class="text-xs text-gray-500 mt-2">
					Кабинет: {{ worker.cabinet }}
				</p>
			</div>
		</div>
	</div>

	<FsLightbox class="" :toggler="toggler" :sources="['/storage/' + worker.photo]"/>




</template>


<script>

import {Head, Link} from "@inertiajs/vue3";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import MainNavbar from "@/Navbars/MainNavbar.vue";
import FsLightbox from "fslightbox-vue/v3";

export default {
	name: "ClientFacultyWorkerCard",
	data() {
		return {
			toggler: false,
		}
	},

	props: {
		worker: {
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