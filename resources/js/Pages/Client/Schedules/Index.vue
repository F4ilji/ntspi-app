<script>
import MainNavbar from "@/Navbars/MainNavbar.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import {debounce} from "lodash";
import {Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";


export default {
	name: "Index",
	data() {
		return {
			searchInput: this.searchRequest,
		}
	},
	components: {BaseIcon, MainPageNavBar, ClientFooterDown, MainNavbar, Link},
	props: [
		'educationalGroups',
		'mainSections',
		'searchRequest',
		'navigation',
	],
	methods: {
		search: debounce(function () {
			this.$inertia.reload({
				method: 'get',
				data: {
					search: this.searchInput,
				},
				preserveState: true,
				replace: true,
			});
		}, 300),
	},

}
</script>

<template>
	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>

	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<article class="w-full min-w-0 mt-4 px-1 md:px-6">
					<div class="relative overflow-hidden">
						<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:pb-12 sm:py-5">
							<div class="text-center">
								<h1 class="text-2xl sm:text-4xl font-bold text-gray-800 dark:text-gray-200">
									Расписание занятий
								</h1>

								<div class="text-center">
									<p class="mt-3 text-gray-600 dark:text-gray-400">
										Просто введите название группы
									</p>
								</div>


								<div class="mt-7 sm:mt-12 mx-auto max-w-xl relative space-y-4">
									<!-- Form -->
									<form>
										<div class="relative z-10 space-x-3 p-3 bg-white border rounded-lg shadow-lg shadow-gray-100">
											<div class="flex justify-between">
												<div class="flex w-full">
													<label for="hs-search-article-1"
																 class="block text-sm text-gray-700 font-medium dark:text-white">
														<span class="sr-only">Поиск</span>
													</label>
													<input @keydown.enter.prevent autocomplete="off" v-model="searchInput" @input="search" type="search"
																 id="hs-search-article-1"
																 class="py-2.5 px-4 block w-full border-transparent rounded-lg"
																 placeholder="Поиск">
												</div>
											</div>
										</div>
									</form>
									<div class="">
										<div class="grid grid-cols-2 gap-3">
											<button type="button" class="flex w-full py-2 px-4 items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
												<BaseIcon name="heart" class="shrink-0 size-4" />
												<span>Избранные расписания</span>
											</button>
											<button type="button" class="flex w-full py-2 px-4 items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
												<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
												<span>Follow</span>
											</button>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>

					<div class="mx-auto max-w-2xl hs-accordion-group grid grid-cols-1 lg:grid-cols-2 gap-3">
						<transition-group name="fade">
							<template v-for="educationalGroup in educationalGroups.data" :key="educationalGroup.id">
								<div class="hs-accordion hs-accordion-active:border-gray-200 bg-white border-b dark:hs-accordion-active:border-gray-700 dark:bg-gray-800 dark:border-transparent"
										 id="hs-active-bordered-heading-one">
									<button class="hs-accordion-toggle hs-accordion-active:text-blue-600 inline-flex justify-between items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:hs-accordion-active:text-blue-500 dark:text-gray-200 dark:hover:text-gray-400 dark:focus:outline-none dark:focus:text-gray-400"
													aria-controls="hs-basic-active-bordered-collapse-one">
										{{ educationalGroup.title }}
										<svg class="hs-accordion-active:hidden block w-3.5 h-3.5"
												 xmlns="http://www.w3.org/2000/svg"
												 width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
												 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M5 12h14"/>
											<path d="M12 5v14"/>
										</svg>
										<svg class="hs-accordion-active:block hidden w-3.5 h-3.5"
												 xmlns="http://www.w3.org/2000/svg"
												 width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
												 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M5 12h14"/>
										</svg>
									</button>
									<div id="hs-basic-active-bordered-collapse-one"
											 class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300"
											 aria-labelledby="hs-active-bordered-heading-one">
										<div class="pb-4 px-5 grid gap-3 grid-cols-1">
											<template v-for="schedule in educationalGroup.schedules" :key="schedule.id">
												<template v-for="file in schedule.file">
													<a :href="'storage/' + file.path" target="_blank" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
														{{ file.title }}
													</a>
												</template>
											</template>


										</div>
									</div>
								</div>
							</template>
						</transition-group>
					</div>
				</article>
			</div>
		</main>
		<ClientFooterDown/>
	</div>

</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
	transition: all 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
	opacity: 0;
	transform: translateY(30px);
}

.gg-enter-active,
.gg-leave-active {
	transition: all 0.5s ease;
}

.gg-enter-from,
.gg-leave-to {
	opacity: 0;
	transform: translateY(30px);
}
</style>