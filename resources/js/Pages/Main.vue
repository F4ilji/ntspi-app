<template>
	<MetaTags :seo="seo" />

	<MainPageNavBar :sections="$page.props.navigation" />
  <Slider class="bvi-hide" slider-id="quos-velit-quisquam" />

	<section class="max-w-screen-xl w-full mx-auto px-4 py-3 pb-10">
		<h2 class="text-brand-primary my-6 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight bvi-show">Последние новости</h2>
		<div class="grid gap-10 pb-10 md:grid-cols-2 lg:gap-10 xl:grid-cols-3 bvi-no-styles">
			<template v-for="post in posts.data" :key="post.id">
				<PostListItem :post="post" />
			</template>
		</div>


    <div class="flex justify-center">
				<a :href="route('client.post.index')" class="group mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-primary">
					Все новости
					<svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
				</a>
			</div>

			<h2 class="text-brand-primary my-6 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight">Мероприятия</h2>

			<!-- Card Blog -->
			<div class="max-w-[85rem] pb-10 sm:px-6 lg:px-8 lg:pb-14 mx-auto">
				<!-- Title -->
				<!-- End Title -->

				<!-- Grid -->
				<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

					<template v-for="event in events.data">
						<Link class="group hover:bg-gray-100 rounded-xl p-5 transition-all" :href="route('client.event.show', event.slug)">
							<div class="aspect-w-16 aspect-h-10">
								<div class="w-full h-[250px] object-cover rounded-xl bg-[#F8F9FB]">
									<div class="flex flex-col px-5 py-5 w-full h-full">
										<div class="flex flex-col gap-3 mb-4">
											<div class="gap-3 flex items-center">
												<div class="shadow-sm">
													<div class="block w-[35px] h-[8px] bg-red-400 rounded-t" />
													<div class="block w-[35px] h-[27px] bg-white rounded-b text-center font-medium">{{ event.event_date_start.day }}</div>
												</div>
												<span class=" block first-letter:uppercase">{{ event.event_date_start.month }}</span>
												<div class="flex">
													<span class="bg-[#E9F2FE] text-sm text-primary px-2 py-1 rounded block">Начало - {{ event.event_date_start.time }}</span>
												</div>
											</div>

										</div>
										<div class="flex gap-2">
											<span v-if="event.is_online === 1" class="bg-[#E9F2FE] text-sm text-primary px-2 py-1 rounded block">Онлайн</span>
											<div v-if="event.is_online === 0">
												<span class="bg-[#E9F2FE] text-sm text-primary px-2 py-1 rounded block">{{ event.address }}</span>
											</div>

										</div>
									</div>
								</div>
							</div>
							<h3 class="mt-5 text-xl text-gray-800">
								{{ event.title }}
							</h3>
							<p class="mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-gray-800">
								Перейти
								<svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
							</p>
						</Link>


					</template>

					<!-- End Card -->
				</div>
				<!-- End Grid -->
			</div>


    <div class="flex justify-center">
				<Link :href="route('client.event.index')" class="group mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-primary">
					Все мероприятия
					<svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
				</Link>
			</div>

    <!-- End Card Blog -->

			<h2 class="text-brand-primary my-10 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight">Образование</h2>

			<!-- Card Blog -->
			<div class="w-full mx-auto">
				<!-- Grid -->
				<div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-6">
					<template v-for="(level, index) in educations.admission_campaign">
						<Link :href="route('client.program.index', {level: LevelEducational[index].name})">
							<div class="group flex flex-col h-full bg-white hover:opacity-70 hover:border-primary-dark duration-300 border border-gray-200 shadow-sm rounded-xl">
								<div class="p-4 md:p-6">
        			<span class="block mb-1 text-xs font-semibold uppercase text-primary">
								{{ LevelEducational[index].type_label }}
       				 </span>
									<h3 class="text-3xl font-semibold text-gray-800">
										{{ LevelEducational[index].label }}
									</h3>
									<p class="mt-3 text-gray-500">
										Построй свою индивидуальную траекторию
									</p>
								</div>
								<div class="mt-auto p-4 md:p-6 grid grid-cols-2 lg:grid-cols-3 sm:space-y-0">
									<div class="pb-4">
										<p class="text-2xl font-semibold text-primary">{{ level.total_programs }}</p>
										<p class="mt-1 text-sm text-gray-500">Программ</p>
									</div>
									<div class="pb-4">
										<p class="text-2xl font-semibold text-primary">{{ level.places.och_count }}</p>
										<p class="mt-1 text-sm text-gray-500">Очных</p>
									</div>
									<div class="pb-4">
										<p class="text-2xl font-semibold text-primary">{{ level.places.zaoch_count }}</p>
										<p class="mt-1 text-sm text-gray-500">Заочных</p>
									</div>
									<div class="pb-4">
										<p class="text-2xl font-semibold text-primary">{{ level.places.budget_places }}</p>
										<p class="mt-1 text-sm text-gray-500">Бюджетных</p>
									</div>
									<div class="pb-4">
										<p class="text-2xl font-semibold text-primary">{{ level.places.non_budget_places }}</p>
										<p class="mt-1 text-sm text-gray-500">Платных</p>
									</div>

								</div>
							</div>
						</Link>

					</template>
					<Link :href="route('client.additionalEducation.index')">
						<div class="group flex flex-col h-full bg-white hover:opacity-70 hover:border-primary-dark duration-300 border border-gray-200 shadow-sm rounded-xl">
							<div class="p-4 md:p-6">
        			<span class="block mb-1 text-xs font-semibold uppercase text-primary">
								Доп. образование
       				 </span>
								<h3 class="text-3xl font-semibold text-gray-800">
									Дополнительное образование
								</h3>
								<p class="mt-3 text-gray-500">
									Построй свою индивидуальную траекторию
								</p>
							</div>
							<div class="mt-auto p-4 md:p-6 grid grid-cols-2 lg:grid-cols-3">
								<div class="pb-4">
									<p class="text-2xl font-semibold text-primary">{{ educations.additional_education.educations_count }}</p>
									<p class="mt-1 text-sm text-gray-500">Программ</p>
								</div>
								<div class="pb-4">
									<p class="text-2xl font-semibold text-primary">{{ educations.additional_education.categories_count }}</p>
									<p class="mt-1 text-sm text-gray-500">Направлений</p>
								</div>


							</div>
						</div>
					</Link>

				</div>
				<!-- End Grid -->
			</div>

			<!-- End Card Blog -->
		</section>
	<PageResourceList resource-id="glavnaia-stranica-resurs" />
	<ContactSectionBlock contact-id="glavnaia-stranica-kontakty" />
	<BasicFooter />

</template>

<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import LevelEducational from "../Enum/LevelEducational.js";
import Slider from "@/componentss/features/sliders/Slider.vue";
import PostListItem from "@/componentss/features/posts/components/PostListItem.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import Cookies from "js-cookie";
import PageResourceList from "@/componentss/shared/pageResource/PageResourceList.vue";
import ContactSectionBlock from "@/componentss/shared/contactSection/ContactSectionBlock.vue";



export default {
	name: "Main",
	computed: {
		LevelEducational() {
			return LevelEducational
		}
	},

	props: {
		posts: {
			type: Object,
		},
		events: {
			type: Object,
		},
		sliders: {
			type: Object,
		},
		educations: {
			type: Object,
		},
		icons: {
			type: String,
		},
		seo: {
			type: Object,
		}
	},

	components: {
    MetaTags,
    BasicFooter,
    PostListItem,
    Slider,
		ContactSectionBlock,
		PageResourceList,
		MainPageNavBar,
		Head,
		Link,


	},

	methods: {
		Cookies,
	},


}


</script>

<style scoped>

</style>
