<script>
import MainNavbar from "@/Navbars/MainNavbar.vue";
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
import LevelEduFilter from "@/Components/BuilderUi/Programs/Filters/LevelEduFilter.vue";
import ClientProgramFilter from "@/Components/ClientProgramFilter.vue";
import PostAuthorsList from "@/Components/BuilderUi/Posts/PostAuthorsList.vue";
import PostTimeRead from "@/Components/BuilderUi/Posts/PostTimeRead.vue";
import PostBackButton from "@/Components/BuilderUi/Posts/PostBackButton.vue";
import PostTitle from "@/Components/BuilderUi/Posts/PostTitle.vue";
import PostBuilder from "@/Components/BuilderUi/Posts/PostBuilder.vue";
import PostGallery from "@/Components/BuilderUi/Posts/PostGallery.vue";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import ProgramListBreadcrumbs from "@/Components/BuilderUi/Programs/ProgramListBreadcrumbs.vue";
import ClientAdditionalProgramFilter
	from "@/Components/BuilderUi/AdditionalEducationPrograms/ClientAdditionalProgramFilter.vue";
import ProgramBadge from "@/Components/BuilderUi/AdditionalEducationPrograms/ProgramBadge.vue";
import AdditionalEducationProgramListBreadcrumbs
	from "@/Components/BuilderUi/AdditionalEducationPrograms/AdditionalEducationProgramListBreadcrumbs.vue";
import AppHead from "@/Components/AppHead.vue";

export default {
  name: "Index",
  components: {
		AppHead,
		AdditionalEducationProgramListBreadcrumbs, ProgramBadge, ClientAdditionalProgramFilter,
		ProgramListBreadcrumbs,
		MainPageNavBar,
		PostGallery, PostBuilder, PostTitle, PostBackButton, PostTimeRead, PostAuthorsList,
		ClientProgramFilter,
		LevelEduFilter,
    AdminIndexHeaderTitle, AdminIndexHeader,
    AdminIndexFilter, AdminIndexSearch,
    ClientFooterDown,
    ClientScrollTimeline,
    ClientPostFilter,
    Link,
    MainNavbar,
    FsLightbox,
    Head,
    ClientPost,
    ClientPostSearch
  },
  data() {
    return {

    };
  },
  props: {
		campaignName: {
			type: String,
    },
		levelsEducational: {
			type: Array,
		},
		naprs: {
			type: Array
		},
		filters: {
			type: Array,
		},
		formsEdu: {
			type: Array,
		},
		budgetEdu: {
			type: Array,
		},
		direction_studies: {
			type: Array,
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
								{{ this.campaignName }}
							</h1>
							<div class="space-y-5 md:space-y-4 mx-auto max-w-3xl">
								<div>
									<div class="">
										<div class="my-10 w-full">
											<ProgramListBreadcrumbs :breadcrumbs="breadcrumbs" />
											<div class="flex items-center gap-x-2">
												<LevelEduFilter :levels="levelsEducational" :level_filter="filters.level_filter" />
												<ClientProgramFilter
														:budget_filter="filters.budget_filter"
														:direction_filter="filters.direction_filter"
														:formEdu_filter="filters.formEdu_filter"
														:types_budget="budgetEdu"
														:direction_studies="direction_studies"
														:forms_educational="formsEdu" />
											</div>
										</div>
									</div>
									<div class="container py-5 lg:py-4">
										<div class="w-full mx-auto flex flex-wrap lg:justify-between">
											<template v-for="naprs in transformToColumns(this.naprs.data)">
												<div class="flex flex-col">
													<template v-for="napr in naprs">
														<div style="height: max-content" class="px-4">
															<h1 class="text-brand-primary mb-2 mt-2 text-lg font-semibold upper tracking-tight dark:text-white lg:text-md lg:leading-tight">
																{{ napr.name }}
															</h1>
															<template v-for="program in napr.programs" :key="program.id">
																<Link
																		class="block text-[#1E57A3] hover:text-blue-600 duration-200 text-sm underline underline-offset-2 py-1"
																		:href="route('client.program.show', program.slug)">{{ program.name }}
																</Link>
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
