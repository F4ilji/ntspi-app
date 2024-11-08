<script>
import {Head, Link} from "@inertiajs/vue3";
import FsLightbox from "fslightbox-vue/v3";
import ClientScrollTimeline from "@/Components/ClientScrollTimeline.vue";
import ClientFooterDown from "@/Components/ClientFooterDown.vue";
import AdminIndexSearch from "@/Components/AdminIndexSearch.vue";
import AdminIndexFilter from "@/Components/AdminIndexFilter.vue";
import AdminIndexHeader from "@/Components/AdminIndexHeader.vue";
import AdminIndexHeaderTitle from "@/Components/AdminIndexHeaderTitle.vue";
import ClientPostFilter from '@/Components/ClientPostFilter.vue';
import ClientPost from '@/Components/ClientPost.vue';
import ClientPostSearch from '@/Components/ClientPostSearch.vue';
import ClientImageSlider from "@/Components/ClientImageSlider.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import LevelEduFilter from "@/Components/BuilderUi/AdditionalEducationPrograms/Filters/LevelEduFilter.vue";
import ClientAdditionalProgramFilter
	from "@/Components/BuilderUi/AdditionalEducationPrograms/ClientAdditionalProgramFilter.vue";
import PostBadge from "@/Components/BuilderUi/Events/EventBadgeBuilder.vue";
import ProgramBadge from "@/Components/BuilderUi/AdditionalEducationPrograms/ProgramBadge.vue";
import AdditionalEducationProgramListBreadcrumbs
	from "@/Components/BuilderUi/AdditionalEducationPrograms/AdditionalEducationProgramListBreadcrumbs.vue";
import AppHead from "@/Components/AppHead.vue";

export default {
  name: "Index",
  components: {
		AppHead,
		AdditionalEducationProgramListBreadcrumbs,
		ProgramBadge,
		PostBadge,
		ClientAdditionalProgramFilter,
		LevelEduFilter,
		MainPageNavBar,
		ClientImageSlider,
    AdminIndexHeaderTitle, AdminIndexHeader,
    AdminIndexFilter, AdminIndexSearch,
    ClientFooterDown,
    ClientScrollTimeline,
    ClientPostFilter,
    Link,
    FsLightbox,
    Head,
    ClientPost,
    ClientPostSearch
  },
  data() {
    return {
			direction_id: this.filters.dir_id,
    };
  },
  props: {
		directionAdditionalEducations: {
        type: Object,
    },
		additionalEducations: {
			type: Object
		},
		filters: {
			type: Object
		},
		forms_education: {
			type: Object
		},
		categories: {
			type: Object
		},
		breadcrumbs: {
			type: Object
		},
		seo: {
			type: Object,
		}
  },
  methods: {
		transformToColumns(originalArray) {
			const chunkArray = (arr, size) => {
				return arr.reduce((acc, _, i) => (i % size ? acc : [...acc, arr.slice(i, i + size)]), []);
			};

			const dividedArrays = chunkArray(originalArray, Math.ceil(originalArray.length / 2));

			return dividedArrays.reverse();
		},
		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText
				LimitedText = text.substring(0, symbols)
				return LimitedText + "..."
			}
			return text
		},


  },
  mounted() {
  }
};
</script>

<template>

	<AppHead :seo="seo" />

	<MainPageNavBar class="border-b" :sections="$page.props.navigation"></MainPageNavBar>


	<div class="flex flex-col h-screen">
		<main class="flex-grow">
			<div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:py-10">
				<div class="">
					<div class="">
						<div class="space-y-5 md:space-y-4">
							<h1 class="text-brand-primary text-center mb-3 mt-2 text-3xl font-semibold tracking-tight dark:text-white lg:text-[40px] lg:leading-tight">
								Дополнительное образование

							</h1>
							<div class="space-y-5 md:space-y-4 mx-auto max-w-3xl">
								<div>
									<div class="">
										<div class="my-10 w-full">
											<AdditionalEducationProgramListBreadcrumbs :breadcrumbs="breadcrumbs" />
											<div class="flex items-center gap-x-2">
												<LevelEduFilter :directions="directionAdditionalEducations" :direction_filter="filters.direction_filter" />
												<ClientAdditionalProgramFilter
														:forms_educational="forms_education"
														:form_education_filter="filters.form_education_filter"
														:category_filter="filters.category_filter"
														:categories="categories"
												/>
											</div>
										</div>
										<div class="px-2 mx-auto">
											<div class="flex-wrap flex gap-3 md:items-center">
												<ProgramBadge :filters="filters" />
											</div>
										</div>
									</div>
									<div class="container py-5 lg:py-4">
										<div class="w-full mx-auto flex flex-wrap lg:justify-between">
											<template v-for="educations in transformToColumns(additionalEducations.data)">
												<div class="flex flex-col">
													<template v-for="education in educations">
														<div style="height: max-content" class="px-2">
															<h1 class="text-brand-primary mb-2 mt-2 text-lg font-semibold upper tracking-tight dark:text-white lg:text-md lg:leading-tight">
																{{ education.title }}
															</h1>
															<template v-for="program in education.additionalEducations" :key="program.id">
																<Link class="block text-[#1E57A3] hover:text-blue-600 duration-200 text-sm underline underline-offset-2 py-1" :href="route('client.additionalEducation.show', program.slug)">{{ program.title }}</Link>
															</template>
														</div>
													</template>
												</div>
											</template>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ClientFooterDown/>
	</div>
</template>

<style scoped></style>
