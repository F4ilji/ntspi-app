<template>
	<div class="flex justify-between pb-4 items-center">
		<div class="flex w-full sm:items-center gap-x-5 sm:gap-x-3">
			<div class="grow">
				<div class="grid sm:flex sm:justify-between sm:items-center gap-2">
					<BreadcrumbsWrapper v-if="breadcrumbs">
						<li class="text-sm">
							<Link :href="route('index')" class="flex items-center text-gray-500 hover:text-blue-600" href="/">
								<BaseIcon class="size-5" name="home" />
							</Link>
						</li>
						<li v-if="breadcrumbs.mainSection" class="text-sm">
							<span class="flex items-center text-gray-500 hover:text-primaryBlue cursor-pointer" @click.prevent="handleSectionClick(this.breadcrumbs.mainSection)">
								<svg class="flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400"
										 width="16" height="16"
										 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
											stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
							</svg>
								{{ textLimit(this.breadcrumbs.mainSection.data.title, 25) }}
							</span>
						</li>
						<li v-if="breadcrumbs.subSection" class="text-sm">
							<span class="flex items-center text-gray-500 hover:text-primaryBlue cursor-pointer" @click.prevent="handleSubSectionClick(this.breadcrumbs.mainSection, this.breadcrumbs.subSection)">
								<svg class="flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400"
										 width="16" height="16"
										 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
												stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
								</svg>
								{{ textLimit(this.breadcrumbs.subSection.data.title, 25) }}
							</span>
						</li>
						<li class="text-sm">
							<Link :href="route('client.faculty.index')" class="flex items-center text-gray-500 hover:text-blue-600">
								<svg class="flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400"
										 width="16" height="16"
										 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
												stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
								</svg>
								{{ textLimit('Факультеты', 30) }}
							</Link>
						</li>
					</BreadcrumbsWrapper>
					<BreadcrumbsWrapper v-else>
						<li class="text-sm">
							<Link :href="route('index')" class="flex items-center text-gray-500 hover:text-blue-600" href="/">
								<BaseIcon class="size-5" name="home" />
							</Link>
						</li>
						<li class="text-sm">
							<Link :href="route('client.faculty.index')" class="flex items-center text-gray-500 hover:text-blue-600">
								<svg class="flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400"
										 width="16" height="16"
										 viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14"
												stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
								</svg>
								{{ textLimit('Факультеты', 30) }}
							</Link>
						</li>
					</BreadcrumbsWrapper>

				</div>
			</div>
		</div>
	</div>


</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";
import BaseIcon from "@/Components/BaseComponents/BaseIcon.vue";
import BreadcrumbsWrapper from "@/Components/BaseComponents/Breadcrumbs/BreadcrumbsWrapper.vue";

export default {
	name: "FacultyListBreadcrumbs",
	components: {BreadcrumbsWrapper, BaseIcon, Link},
	data() {
		return {
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
		isMobileDevice() {
			if (typeof window !== 'undefined') {
				return window.innerWidth < 1024; // Проверка на мобильные устройства
			}
			return false; // По умолчанию возвращаем false, когда не в браузере
		},
		handleSectionClick(breadcrumb) {
			if (this.isMobileDevice()) {
				this.toggleMobileNavSection(breadcrumb)
			} else {
				this.toggleDesktopNavSection(breadcrumb)
			}
		},
		handleSubSectionClick(mainSectionBreadcrumb, breadcrumb) {
			if (this.isMobileDevice()) {
				this.toggleMobileNavSubSection(mainSectionBreadcrumb, breadcrumb)
			} else {
				this.toggleDesktopNavSubSection(mainSectionBreadcrumb, breadcrumb)
			}
		},
		highlightNavItem(item) {
			item.classList.add('animate-pulse');

			setTimeout(() => {
				item.classList.remove('animate-pulse');
			}, 4000);
		},
		openMobileNavMenu() {
			const openMobileNavBtn = document.getElementById('open-mobile-btn');
			openMobileNavBtn.click();
		},
		toggleMobileNavSubSection(mainSectionBreadcrumb, breadcrumb) {
			this.openMobileNavMenu()
			const mobileNavElement = document.getElementById('open-mobile-nav');
			const sectionNavBlock = mobileNavElement.querySelector('#nav-section-accordion-' + mainSectionBreadcrumb.data.slug);
			const sectionNavBlockBtn = mobileNavElement.querySelector('#nav-section-accordion-btn-' + mainSectionBreadcrumb.data.slug);
			if (!sectionNavBlock.classList.contains('active')) {
				sectionNavBlockBtn.click()
			}
			const subSectionNavBlock = mobileNavElement.querySelector('#nav-sub-section-accordion-' + breadcrumb.data.slug);
			const subSectionNavBlockBtn = mobileNavElement.querySelector('#nav-sub-section-accordion-btn-' + breadcrumb.data.slug);
			if (subSectionNavBlock.classList.contains('active')) {
				subSectionNavBlockBtn.click()
			}

			this.highlightNavItem(subSectionNavBlock)
		},
		toggleMobileNavSection(breadcrumb) {
			const mobileNavElement = document.getElementById('open-mobile-nav');
			const sectionNavBlock = mobileNavElement.querySelector('#nav-section-accordion-' + breadcrumb.data.slug);
			const sectionNavBlockBtn = mobileNavElement.querySelector('#nav-section-accordion-btn-' + breadcrumb.data.slug);
			if (sectionNavBlock.classList.contains('active')) {
				sectionNavBlockBtn.click()
			}
			this.openMobileNavMenu()

			this.highlightNavItem(sectionNavBlock)

		},
		toggleDesktopNavSubSection(mainSectionBreadcrumb, breadcrumb) {
			const desktopNavElement = document.getElementById('desktop-nav');
			const sectionNavTitle = desktopNavElement.querySelector('#nav-sub-section-title-' + breadcrumb.data.slug);
			const sectionNavBlockBtn = desktopNavElement.querySelector('#nav-section-btn-' + mainSectionBreadcrumb.data.slug);
			sectionNavBlockBtn.click()
			this.highlightNavItem(sectionNavTitle)
		},
		toggleDesktopNavSection(breadcrumb) {
			const desktopNavElement = document.getElementById('desktop-nav');
			const sectionNavBlock = desktopNavElement.querySelector('#nav-section-menu-' + breadcrumb.data.slug);
			const sectionNavBlockBtn = desktopNavElement.querySelector('#nav-section-btn-' + breadcrumb.data.slug);
			sectionNavBlockBtn.click()
			// this.highlightNavItem(sectionNavBlock)
		},


	},

	props: {
		breadcrumbs: {
			type: Object,
		},
		eventTitle: {
			type: String,
		},
	},
}

</script>


<style scoped>

</style>