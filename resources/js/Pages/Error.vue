<template>
	<Head>
		<title>Ошибка {{ title }}</title>
		<meta name="description" content="Your page description">
	</Head>


	<div class="flex flex-col h-screen justify-between">
		<MainPageNavBar :sections="$page.props.navigation" />
		<div class="max-w-[50rem] flex flex-col mx-auto size-full">
			<!-- ========== HEADER ========== -->
			<!-- ========== END HEADER ========== -->

			<!-- ========== MAIN CONTENT ========== -->
			<main class="my-auto" id="content">
				<div class="text-center py-10 px-4 sm:px-6 lg:px-8">
					<h1 class="block text-7xl font-bold text-gray-800 sm:text-9xl">{{ title }}</h1>
					<p class="text-gray-600">{{ description }}</p>
					<div class="mt-5 flex flex-col justify-center items-center gap-2 sm:flex-row sm:gap-3">
						<a :href="route('index')" class="w-full sm:w-auto py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary text-white hover:bg-primary-light disabled:opacity-50 disabled:pointer-events-none" href="../examples.html">
							<svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
							На домашнюю страницу
						</a>
					</div>
				</div>
			</main>
			<!-- ========== END MAIN CONTENT ========== -->

			<!-- ========== FOOTER ========== -->

			<!-- ========== END FOOTER ========== -->
		</div>

		<BasicFooter />
	</div>

</template>

<script>

import {defineComponent} from "vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import BasicFooter from "@/footers/BasicFooter.vue";

export default defineComponent({
	components: {BasicFooter, MainPageNavBar},
	props: {
		status: Number,
	},
	computed: {
		title() {
			return {
				503: '503',
				500: '500',
				404: '404',
				403: '403',
			}[this.status]
		},
		description() {
			return {
				503: 'Простите, мы проводим технические работы на странице.',
				500: 'Упс, проблема на стороне сервера. Мы уже решаем проблему!',
				404: 'Упс, страница не найдена!',
				403: 'Извините, у вас нет доступа к этой секции.',
			}[this.status]
		},
	},
})

</script>

<style scoped>

</style>
