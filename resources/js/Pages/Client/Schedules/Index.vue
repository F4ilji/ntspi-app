<script>
import MainPageNavBar from "@/Navbars/MainPageNavBar.vue";
import ScheduleListTitle from "@/componentss/features/schedules/components/ScheduleListTitle.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import {Link} from "@inertiajs/vue3";

export default {
	name: "Index",
	components: {
		MetaTags,
		BasicFooter,
		ScheduleListTitle,
		MainPageNavBar,
		Link,
	},
	props: {
		faculties: {
			type: Array,
			default: () => [],
		},
		seo: {
			type: Object,
			default: () => ({}),
		},
	},
	methods: {
		pluralize(n, one, few, many) {
			const mod10 = n % 10;
			const mod100 = n % 100;
			if (mod100 >= 11 && mod100 <= 19) return many;
			if (mod10 === 1) return one;
			if (mod10 >= 2 && mod10 <= 4) return few;
			return many;
		},
	},
}
</script>

<template>
	<MetaTags :seo="seo" />
	<MainPageNavBar :sections="$page.props?.navigation" />

	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:py-10">
				<article class="w-full min-w-0 mt-4 px-1 md:px-6">
					<ScheduleListTitle
						bottom-text="Выберите факультет, чтобы посмотреть расписание"
						header="Расписание занятий" />

					<div class="mt-10 sm:mt-14 mx-auto max-w-3xl">
						<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
							<Link
								v-for="faculty in faculties"
								:key="faculty.id"
								:href="route('client.schedule.faculty', faculty.slug)"
								class="group block rounded-xl border border-gray-200 bg-white p-6 transition-all hover:shadow-md hover:border-primary hover:-translate-y-0.5"
							>
								<div class="flex items-center gap-3 mb-3">
									<div class="flex items-center justify-center size-10 rounded-lg bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-white">
										<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
											<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
										</svg>
									</div>
									<div>
										<h3 class="font-semibold text-gray-800 group-hover:text-primary transition-colors">
											{{ faculty.title }}
										</h3>
										<p class="text-sm text-gray-500">
											{{ faculty.groups_count }} {{ pluralize(faculty.groups_count, 'группа', 'группы', 'групп') }}
										</p>
									</div>
								</div>
							</Link>
						</div>

						<div v-if="faculties.length === 0" class="text-center py-12">
							<p class="text-gray-500">Факультеты не найдены</p>
						</div>
					</div>
				</article>
			</div>
		</main>

		<BasicFooter />
	</div>
</template>
