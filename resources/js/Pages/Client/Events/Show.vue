<script>
import MainNavbar from "@/Navbars/MainNavbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import ClientScrollTimeline from "@/Components/ClientScrollTimeline.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import EventBackButton from "@/Components/BuilderUi/Events/EventBackButton.vue";
import TitleEvent from "@/Components/BuilderUi/Events/TitleEvent.vue";
import PostBuilder from "@/Components/BuilderUi/Posts/PostBuilder.vue";
import EventBuilder from "@/Components/BuilderUi/Events/EventBuilder.vue";
import ClientEventSelectDate from "@/Components/ClientEventSelectDate.vue";
import ClientEventFilter from "@/Components/ClientEventFilter.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import EventItemBreadcrumbs from "@/Components/BuilderUi/Events/EventItemBreadcrumbs.vue";
import AppHead from "@/Components/AppHead.vue";


export default {
	name: "Show",
	components: {
		AppHead,
		EventItemBreadcrumbs,
		MainPageNavBar,
		ClientEventFilter, ClientEventSelectDate,
		EventBuilder,
		PostBuilder,
		TitleEvent,
		EventBackButton,
		ClientImageSlider, ClientFooterDown, ClientScrollTimeline, Link, MainNavbar, Head},
	data() {
		return {
		}
	},

	props: {
		event: {
			type: Object,
		},
		navigation: {
			type: Object,
		},
		breadcrumbs: {
			type: Object
		},
		seo: {
			type: Object,
		}
	},
	methods: {
		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText
				LimitedText = text.substring(0, symbols)
				return LimitedText + "..."
			}
			return text
		},

	},

}
</script>

<template>
	<AppHead :seo="seo" />


	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>
	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mx-auto mt-[67px] max-w-screen-xl py-10 md:flex md:flex-row md:py-10">
				<div class="max-w-4xl px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto">
					<div>
						<div class="space-y-5 md:space-y-10">
							<div class="space-y-3">
								<EventItemBreadcrumbs :breadcrumbs="breadcrumbs" :event-title="event.data.title" />
								<EventBackButton link="client.event.index" title="Все мероприятия" />
								<TitleEvent :header="event.data.title" />
							</div>


							<div class="flex space-x-3 text-gray-500 ">
								<div class="flex items-center gap-3">
									<div>

										<div class="md:flex md:items-center space-y-3 md:space-y-0 space-x-2 text-sm">
											<p v-if="event.data.is_online === 1" class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] text-blue-600">
												Онлайн
											</p>
											<p class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] text-blue-600">
												{{ event.data.category }}
											</p>
											<div class="col-start-2 text-center">
												<span>Адрес: {{ event.data.address }}</span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="space-y-3 md:space-y-4">
								<EventBuilder :blocks="event.data.content"/>
							</div>


						</div>
						<!-- End Content -->
					</div>
				</div>

			</div>
		</main>
		<ClientFooterDown/>
	</div>
</template>

<style>


</style>