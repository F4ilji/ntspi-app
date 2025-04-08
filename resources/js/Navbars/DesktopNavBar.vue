<template>
	<div id="desktop-nav"
			 class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow lg:block">
		<div
				class="overflow-hidden overflow-y-auto max-h-[75vh] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
			<div
					class="flex flex-col gap-x-0 mt-5 md:flex-row md:items-center md:justify-end md:gap-x-7 md:mt-0 md:ps-7 md:divide-y-0 md:divide-solid">
				<template v-if="sections && sections.data">
					<template v-for="section in sections.data" :key="section.id">
						<div
								:id="'nav-section-' + section.slug"
								class="hs-dropdown [--strategy:static] md:[--strategy:absolute] [--adaptive:none] md:[--trigger:hover] py-3 md:py-6">
							<button
									:id="'nav-section-btn-' + section.slug"
									type="button"
									:class="!underSliderHeader ? 'text-white' : 'text-black'"
									class="duration-300 flex items-center w-full hover:text-primary-hover font-normal hs-dropdown-open:mb-4 md:hs-dropdown-open:mb-0">
								{{ section.title }}
								<svg class="flex-shrink-0 ms-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
										 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
										 stroke-linejoin="round">
									<path d="m6 9 6 6 6-6"/>
								</svg>
							</button>
							<div
									:id="'nav-section-menu-' + section.slug"
									class="hs-dropdown-menu transition-opacity duration-150 md:duration-500 hs-dropdown-open:opacity-100 opacity-0 w-full hidden z-10 top-full start-0 min-w-[15rem] bg-white md:shadow-2xl rounded-lg py-2 md:p-4 before:absolute before:-top-5 before:start-0 before:w-full before:h-5">
								<div class="grid px-5 grid-cols-1 md:grid-cols-10">

									<template v-for="subsection in section.subSections" :key="subsection.id">
										<div :id="'nav-sub-section-block-' + subsection.slug" class="md:col-span-3">
											<div class="flex flex-col py-4 px-3 md:px-6">
												<div class="space-y-4">
													<div class="flex items-center mb-2 gap-x-2">
                                                        <span
																														:id="'nav-sub-section-title-' + subsection.slug"
																														class="text-xs font-bold uppercase text-gray-800 dark:text-gray-200">
                                                            {{ subsection.title }}
                                                        </span>
													</div>
													<template v-for="page in subsection.pages" :key="page.id">
														<a
																:class="{ 'text-primary-light hover:text-gray-800 font-semibold': IS_SAME_ROUTE(page.path), 'text-gray-800 hover:text-[#2C6288]': !IS_SAME_ROUTE(page.path) }"
																class="flex items-center gap-x-2"
																:href="(page.is_url) ? page.path : route('page.view', page.path) + '/'">
															<div class="grow">
																<p>{{ page.title }}</p>
															</div>
														</a>
													</template>
												</div>
											</div>
										</div>
									</template>

								</div>
							</div>

						</div>
					</template>
				</template>

				<a @click="this.toggleBvi" href="#" class="hover:opacity-70 py-3 open-bvi ">
					<svg :class="!underSliderHeader ? 'text-white' : 'text-black'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
							 stroke="currentColor"
							 class="w-6 h-6 duration-300 md:block hidden">
						<path stroke-linecap="round" stroke-linejoin="round"
									d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
						<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
					</svg>
					<span :class="!underSliderHeader ? 'text-white' : 'text-black'" class="md:hidden">Режим для слабовидящих</span>
				</a>


				<Link :href="route('client.schedule.index')" class="hover:opacity-70 py-3">
					<svg :class="!underSliderHeader ? 'text-white' : 'text-black'" xmlns="http://www.w3.org/2000/svg"
							 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
							 class="w-6 h-6 duration-300 md:block hidden">
						<path stroke-linecap="round" stroke-linejoin="round"
									d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"/>
					</svg>
					<span :class="!underSliderHeader ? 'text-white' : 'text-black'" class="md:hidden">Расписание</span>
				</Link>

				<a class="hover:opacity-70 py-3 cursor-pointer" data-hs-overlay="#open-search-modal">
					<svg :class="!underSliderHeader ? 'text-white' : 'text-black'" xmlns="http://www.w3.org/2000/svg"
							 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
							 class="w-6 h-6 duration-300 md:block hidden">
						<path stroke-linecap="round" stroke-linejoin="round"
									d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
					</svg>
					<span :class="!underSliderHeader ? 'text-white' : 'text-black'" class="md:hidden">Поиск</span>
				</a>

        <a class="hover:opacity-70 py-3 cursor-pointer" data-hs-overlay="#open-search-sveden-modal">


          <svg :class="!underSliderHeader ? 'text-white' : 'text-black'" xmlns="http://www.w3.org/2000/svg"
               fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
               class="w-6 h-6 duration-300 md:block hidden">
            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
          </svg>


          <span :class="!underSliderHeader ? 'text-white' : 'text-black'" class="md:hidden">Поиск</span>
        </a>



      </div>
		</div>
	</div>
</template>

<script>

import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {helpers} from "@/mixins/Helpers.js";
import {mapActions} from "vuex";


export default {
  mixins: [helpers],
  name: 'DesktopNavBar',
	props: {
		sections: {
			type: Object,
		},
		underSliderHeader: {
		}

	},

	components: {
		BasicIcon,
		Link,
	},
	methods: {
    ...mapActions(['toggleBvi'])
	},


}

</script>

<style scoped>

.header-filter {
	transition: all 0.3s;
	backdrop-filter: saturate(180%) blur(7px);
	background: hsla(0, 0%, 100%, .6);
}




</style>
