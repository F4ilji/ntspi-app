<script>
import MainNavbar from "@/Navbars/MainNavbar.vue";
import { debounce } from "lodash";
import { Link } from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import ScheduleListTitle from "@/componentss/features/schedules/components/ScheduleListTitle.vue";
import ScheduleListFilter from "@/componentss/features/schedules/components/ScheduleListFilter.vue";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import BasicListFilter from "@/componentss/shared/filter/BasicListFilter.vue";
import FormEducationalFilter from "@/componentss/shared/filter/filters/FormEducationalFilter.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";


export default {
	name: "Index",
	data() {
		return {
			searchInput: this.filters.search_filter.value,
			favoriteGroups: JSON.parse(localStorage.getItem('favoriteGroups')),
			showFavorites: false,
			loading: false,
		};
	},
	components: {
    MetaTags,
    FormEducationalFilter,
    BasicListFilter,
    BasicFooter,
    ScheduleListFilter,
    ScheduleListTitle, BasicIcon, MainPageNavBar, MainNavbar, Link},

	props: {
		navigation: {
			type: Object
		},
		filters: {
			type: Object
		},
		forms_education: {
			type: Object
		},
		schedulesByFaculty: {
			type: Object
		},
    seo: {
      type: Object
    }
	},
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
    toggleFavorite(group) {
      if (!this.favoriteGroups) {
        this.favoriteGroups = [];
      }

      const index = this.favoriteGroups.findIndex(g => g === group);
      if (index === -1) {
        this.favoriteGroups.push(group);
      } else {
        this.favoriteGroups.splice(index, 1);
      }

      localStorage.setItem('favoriteGroups', JSON.stringify(this.favoriteGroups));
    },

    isFavorite(group) {
      // Если favoriteGroups не существует или не является массивом, возвращаем false
      if (!Array.isArray(this.favoriteGroups)) {
        return false;
      }
      return this.favoriteGroups.some(g => g === group);
    },
		toggleShowFavorites() {
			this.loading = true; // Включаем состояние загрузки

			if (this.filters.favorite_filter.value === null) {
				this.filterFavorite(() => {
					this.loading = false; // Выключаем состояние загрузки после завершения
				});
			} else {
				this.clearFilterFavorite(() => {
					this.loading = false; // Выключаем состояние загрузки после завершения
				});
			}
		},

		filterFavorite: debounce(function (callback) {
			let url = new URL(window.location.href);
			// Создаем массив для хранения всех ключей, которые нужно удалить
			const keysToDelete = [];

			// Перебираем все параметры и добавляем ключи, начинающиеся с 'category', в массив
			for (const [key] of url.searchParams) {
				if (key.startsWith('favorite')) {
					keysToDelete.push(key);
				}
			}
			// Удаляем все ключи из массива
			keysToDelete.forEach(key => url.searchParams.delete(key));
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
				data: {
					favorite: (this.favoriteGroups?.length !== 0) ? this.favoriteGroups : "",
				},
				onFinish: callback, // Вызываем колбэк после завершения запроса
			});
		}, 500),
		clearFilterFavorite(callback) {
			let url = new URL(window.location.href);
			// Создаем массив для хранения всех ключей, которые нужно удалить
			const keysToDelete = [];

			// Перебираем все параметры и добавляем ключи, начинающиеся с 'category', в массив
			for (const [key] of url.searchParams) {
				if (key.startsWith('favorite')) {
					keysToDelete.push(key);
				}
			}
			// Удаляем все ключи из массива
			keysToDelete.forEach(key => url.searchParams.delete(key));
			let newUrl = url.toString();
			this.$inertia.visit(newUrl, {
				method: 'get',
				preserveState: true,
				onFinish: callback, // Вызываем колбэк после завершения запроса

			});
		},
  },
  computed: {
  },
};
</script>

<template>
  <MetaTags :seo="this.seo" />
	<MainPageNavBar :sections="$page.props.navigation" />

	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
				<article class="w-full min-w-0 mt-4 px-1 md:px-6">
					<div class="relative overflow-hidden">
						<div class="max-w-[85rem] mx-auto sm:px-6 lg:px-8 py-10 sm:pb-12 sm:py-5">
							<div>
                <ScheduleListTitle
                    bottom-text="Просто введите название группы"
                    header="Расписание занятий" />
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
													<input
															@keydown.enter.prevent
															autocomplete="off"
															v-model="searchInput"
															@input="search"
															type="search"
															id="hs-search-article-1"
															class="py-2.5 px-4 block w-full border-transparent rounded-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-100"
															placeholder="Поиск"
													>
												</div>
											</div>
										</div>
									</form>
									<div class="">
										<div class="grid grid-cols-2 gap-3">
											<button
													@click="toggleShowFavorites"
													type="button"
													:disabled="loading"
													:class="
															filters.favorite_filter.value !== null ? 'bg-primary text-white hover:bg-primary-dark': 'text-gray-700 hover:bg-gray-100',
															loading ? 'animate-pulse' : ''
															"
													class="flex w-full py-2 px-4 items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
													>
												<BasicIcon name="heart" class="shrink-0 size-4"/>
												<span>Избранное</span>
											</button>
                      <ScheduleListFilter>
                        <FormEducationalFilter :forms="this.forms_education" :form-edu_filter="this.filters.form_education_filter" />
                      </ScheduleListFilter>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="mx-auto max-w-2xl hs-accordion-group grid gap-3">
						<div class="space-y-4">
              <template v-for="(faculty, title) in schedulesByFaculty">
									<div>
										<h2 class="text-center text-gray-800 font-medium">{{ title }}</h2>
										<hr class="mt-2 mb-4">
										<template v-for="educationalGroup in faculty" :key="educationalGroup.data.id">
											<div class="hs-accordion hs-accordion-active:border-gray-200 bg-white border border-transparent rounded-xl" id="hs-active-bordered-heading-one">
												<div class="flex py-4 px-5 gap-x-3">
													<button @click="toggleFavorite(educationalGroup.data.id)">
														<transition name="heart" mode="out-in">
															<BasicIcon
																	v-if="isFavorite(educationalGroup.data.id)"
																	name="heart_filled"
																	class="shrink-0 size-4 text-red-500"
																	key="filled"
															/>
															<BasicIcon
																	v-else
																	name="heart"
																	class="shrink-0 size-4"
																	key="outline"
															/>
														</transition>

													</button>
													<button class="hs-accordion-toggle hs-accordion-active:text-primary inline-flex justify-between items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none" aria-expanded="false" aria-controls="hs-basic-active-bordered-collapse-one">
														<span class="flex items-center gap-x-2">{{ educationalGroup.data.title }}</span>
														<svg class="hs-accordion-active:hidden block size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M5 12h14"></path>
															<path d="M12 5v14"></path>
														</svg>
														<svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M5 12h14"></path>
														</svg>
													</button>
												</div>

												<div id="hs-basic-active-bordered-collapse-one" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="hs-active-bordered-heading-one">
													<div class="pb-4 px-5 grid gap-3 grid-cols-1">
														<template v-for="schedule in educationalGroup.data.schedules" :key="schedule.id">
															<template v-for="file in schedule.file">
																<a :href="'storage/' + file.path" target="_blank"
																	 class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
																	{{ file.title }}
																</a>
															</template>
														</template>
													</div>
												</div>
											</div>
										</template>
									</div>
								</template>
              <div v-if="schedulesByFaculty.length === 0">
                <div class="h-full flex flex-col items-center justify-center gap-1">
                  <p class="font-light">Извините, ничего не найдено</p>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-700">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                  </svg>
                </div>
              </div>

						</div>
					</div>
				</article>
			</div>
		</main>

		<BasicFooter />
	</div>
</template>

<style scoped>


.heart-enter-active,
.heart-leave-active {
	transition: opacity 0.1s ease;
}

.heart-enter-from,
.heart-leave-to {
	opacity: 0;
}
.fade-enter-active,
.fade-leave-active {
	transition: all 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
	opacity: 0;
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