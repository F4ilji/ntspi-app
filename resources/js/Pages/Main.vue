<template>
	<Head>
		<title>Главная</title>
		<meta name="description" content="Your page description">
		<meta name="robots" content="index, follow">
		<meta property="og:title" content="Заголовок страницы">
		<meta property="og:description" content="Описание страницы">
		<meta property="og:image" content="URL_изображения">
	</Head>
	<MainPageNavBar :sections="$page.props.navigation" :slider-ref="sliderRef" />
	<ClientMainSlider @slider-mounted="setSliderRef" :slidersCarousel="sliders" />
		<section class="max-w-screen-xl w-full mx-auto px-4 py-3 pb-10">
			<h2 class="text-brand-primary my-6 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight">Последние новости</h2>
			<div class="grid gap-10 pb-10 md:grid-cols-2 lg:gap-10 xl:grid-cols-3">
				<template v-for="post in posts.data" :key="post.id">
					<ClientPost :post="post" />
				</template>
			</div>


			<div class="flex justify-center">
				<a :href="route('client.post.index')" class="group mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-primaryBlue">
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
													<span class="bg-[#E9F2FE] text-sm text-blue-600 px-2 py-1 rounded block">Начало - {{ event.event_date_start.time }}</span>
												</div>
											</div>

										</div>
										<div class="flex gap-2">
											<span v-if="event.is_online === 1" class="bg-[#E9F2FE] text-sm text-blue-600 px-2 py-1 rounded block">Онлайн</span>
											<div v-if="event.is_online === 0">
												<span class="bg-[#E9F2FE] text-sm text-blue-600 px-2 py-1 rounded block">{{ event.address }}</span>
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
				<Link :href="route('client.event.index')" class="group mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-[#1A5AAF]">
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
							<div class="group flex flex-col h-full bg-white hover:opacity-70 hover:border-secondDarkBlue duration-300 border border-gray-200 shadow-sm rounded-xl">
								<div class="p-4 md:p-6">
        			<span class="block mb-1 text-xs font-semibold uppercase text-blue-600">
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
										<p class="text-2xl font-semibold text-blue-600">{{ level.total_programs }}</p>
										<p class="mt-1 text-sm text-gray-500">Программ</p>
									</div>
									<div class="pb-4">
										<p class="text-2xl font-semibold text-blue-600">{{ level.places.och_count }}</p>
										<p class="mt-1 text-sm text-gray-500">Очных</p>
									</div>
									<div class="pb-4">
										<p class="text-2xl font-semibold text-blue-600">{{ level.places.zaoch_count }}</p>
										<p class="mt-1 text-sm text-gray-500">Заочных</p>
									</div>
									<div class="pb-4">
										<p class="text-2xl font-semibold text-blue-600">{{ level.places.budget_places }}</p>
										<p class="mt-1 text-sm text-gray-500">Бюджетных</p>
									</div>
									<div class="pb-4">
										<p class="text-2xl font-semibold text-blue-600">{{ level.places.non_budget_places }}</p>
										<p class="mt-1 text-sm text-gray-500">Платных</p>
									</div>

								</div>
							</div>
						</Link>

					</template>
					<Link :href="route('client.additionalEducation.index')">
						<div class="group flex flex-col h-full bg-white hover:opacity-70 hover:border-secondDarkBlue duration-300 border border-gray-200 shadow-sm rounded-xl">
							<div class="p-4 md:p-6">
        			<span class="block mb-1 text-xs font-semibold uppercase text-blue-600">
								Доп. образование
       				 </span>
								<h3 class="text-3xl font-semibold text-gray-800">
									Дополнительное образование
								</h3>
								<p class="mt-3 text-gray-500">
									Построй свою индивидуальную траекторию
								</p>
							</div>
							<div class="mt-auto p-4 md:p-6 grid grid-cols-2 lg:grid-cols-3 sm:space-y-0 space-y-3">
								<div class="pb-4">
									<p class="text-2xl font-semibold text-blue-600">{{ educations.additional_education.educations_count }}</p>
									<p class="mt-1 text-sm text-gray-500">Программ</p>
								</div>
								<div class="pb-4">
									<p class="text-2xl font-semibold text-blue-600">{{ educations.additional_education.categories_count }}</p>
									<p class="mt-1 text-sm text-gray-500">Направлений</p>
								</div>


							</div>
						</div>
					</Link>

				</div>
				<!-- End Grid -->
			</div>


			<PageResourceList resource-id="glavnaia-stranica-resurs" />

			<!-- End Card Blog -->
		</section>




		<section class="bg-[#F5F5F5] w-full py-10">
			<div class="max-w-screen-xl md:flex justify-around w-full mx-auto px-4 py-[50px] flex-wrap">
				<div class="mb-[50px] space-y-3">
					<h2 class="font-semibold text-[#1A5AAF] text-lg">Контакты</h2>
					<div>
						<h3 class="font-semibold mb-4">Главный корпус</h3>
						<div class="font-light">
							<p>622031, Нижний Тагил,  Красногвардейская 57</p>
						</div>
					</div>
					<div>
						<h3 class="font-semibold mb-4">Свяжитесь с нами</h3>
						<div class="flex gap-x-3 font-light">
							<p>+7(906)-802-55-59</p>
							<p>ntgspi@yandex.ru</p>
						</div>
					</div>
				</div>
				<div class="mb-[50px] space-y-3">
					<h2 class="font-semibold text-[#1A5AAF] text-lg">Приемная комиссия</h2>
					<div>
						<h3 class="font-semibold mb-4">Расписание</h3>
						<div class="font-light">
							<p>Понедельник - Пятница с 08.30 до 17.00</p>
						</div>
					</div>
					<div>
						<h3 class="font-semibold mb-4">Ответственный секретарь приемной комиссии</h3>
						<div class="font-light">
							<p>+7(906)-802-55-59</p>
							<p>ntgspi@yandex.ru</p>
						</div>
					</div>
				</div>
				<div class="space-y-3">
					<h2 class="font-semibold text-[#1A5AAF] text-lg">Полезное</h2>
					<div>
						<h3 class="font-semibold mb-4">Главный корпус</h3>
						<div class="font-light">
							<p>Понедельник - Пятница <br> с 08.30 до 17.00</p>
						</div>
					</div>
					<div>
						<h3 class="font-semibold mb-4">Ответственный секретарь <br> приемной комиссии</h3>
						<div class="font-light">
							<p>+7(906)-802-55-59</p>
							<p>ntgspi@yandex.ru</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<ClientFooterDown />



</template>

<script>
import {Head, Link} from "@inertiajs/vue3";
import ClientMainSlider from "@/Components/ClientMainSlider.vue";
import ClientMainSliderSecond from "@/Components/ClientMainSliderSecond.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import ClientPost from "@/Components/ClientPost.vue";
import { ref } from 'vue';
import LevelEducational from "../Enum/LevelEducational.js";
import BaseMetaHead from "@/Components/BaseComponents/BaseMetaHead.vue";
import PageResourceList from "@/Components/BuilderUi/Pages/Blocks/PageResourceList.vue";



export default {
	name: "Main",
	computed: {
		LevelEducational() {
			return LevelEducational
		}
	},
	data() {
		return {
			sliderRef: null,
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
		}
	},

	components: {
		PageResourceList,
		BaseMetaHead,
		ClientPost,
		MainPageNavBar,
		ClientFooterDown,
		ClientMainSlider,
		ClientMainSliderSecond,
		Head,
		Link,


	},

	methods: {
		setSliderRef(ref) {
			this.sliderRef = ref;
		},
	},




}


</script>

<style scoped>

</style>
