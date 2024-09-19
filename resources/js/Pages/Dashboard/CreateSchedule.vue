<template>
	<!-- ========== MAIN CONTENT ========== -->
	<!-- Sidebar -->
	<div id="sidebar-mini" class="transform fixed top-0 start-0 bottom-0 z-[60] w-20 bg-white border-e border-gray-200 lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-800 dark:border-neutral-700" role="dialog" tabindex="-1" aria-label="Mini Sidebar">
		<div class="flex flex-col justify-center items-center gap-y-2 py-4">
			<div class="mb-4">
				<Link
						class="flex-none text-xl font-semibold"
						href="/" aria-label="Brand">
					<img class="max-w-[40px]" src="/logos/only_logo.svg" alt="">
				</Link>
			</div>

			<div class="hs-tooltip [--placement:right] inline-block">
				<button type="button" class="hs-tooltip-toggle size-[38px] inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
					<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
					<span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 inline-block absolute invisible z-20 py-1.5 px-2.5 bg-gray-900 text-xs text-white rounded-lg whitespace-nowrap dark:bg-neutral-700" role="tooltip">
              Home
            </span>
				</button>
			</div>

			<div class="hs-tooltip [--placement:right] inline-block">
				<button type="button" class="hs-tooltip-toggle size-[38px] inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
					<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
					<span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 inline-block absolute invisible z-20 py-1.5 px-2.5 bg-gray-900 text-xs text-white rounded-lg whitespace-nowrap dark:bg-neutral-700" role="tooltip">
              Users
            </span>
				</button>
			</div>

			<div class="hs-tooltip [--placement:right] inline-block">
				<button type="button" class="hs-tooltip-toggle size-[38px] inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
					<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>
					<span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 inline-block absolute invisible z-20 py-1.5 px-2.5 bg-gray-900 text-xs text-white rounded-lg whitespace-nowrap dark:bg-neutral-700" role="tooltip">
              Notifications
            </span>
				</button>
			</div>
		</div>
	</div>	<!-- End Sidebar -->

	<!-- Content -->
	<div class="relative h-screen w-full">
		<div class="py-10 lg:py-14">
			<!-- Title -->
			<div class="max-w-4xl px-4 sm:px-6 lg:px-8 mx-auto text-center">
				<h1 class="text-3xl font-bold text-gray-800 sm:text-4xl dark:text-white">
					Расписание НТГСПИ
				</h1>
				<p class="mt-3 text-gray-600 dark:text-neutral-400">
					Онлайн веб конструктор
				</p>

				<div>
					<Repeater>
						<template class="" #block="{ block, index }">
							<!-- Отображение и редактирование содержимого блока -->
							<div class="w-full py-3 border">{{ weekDays[index] }}</div>
						</template>
					</Repeater>
					<button @click="saveBlocks" type="button" class="hidden py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 text-gray-500 hover:border-blue-600 hover:text-blue-600 focus:outline-none focus:border-blue-600 focus:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:border-neutral-700 dark:text-neutral-400 dark:hover:text-blue-500 dark:hover:border-blue-600 dark:focus:text-blue-500 dark:focus:border-blue-600">
						Сохранить
					</button>
				</div>

			</div>

			<!-- End Title -->

		</div>


		<!-- Textarea -->
		<!-- End Textarea -->
	</div>
	<!-- End Content -->
	<!-- ========== END MAIN CONTENT ========== -->



</template>

<script>


import {Link} from "@inertiajs/vue3";
import Repeater from "@/Components/Dashboard/Repeater.vue";

export default {
	name: "CreateSchedule",
	data() {
		return {
			days: []
		}
	},

	computed: {
		weekDays() {
			return ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
		}
	},

	props: {
	},
	components: {
		Repeater,
		Link
	},
	methods: {
		saveBlocks() {
			// Сохранение массива blocks в JSON формате
			const blocksJson = JSON.stringify(this.blocks);
			// Отправка данных на сервер или сохранение в другом месте
			console.log(blocksJson);
		}
	}
}


</script>

<style scoped>

</style>