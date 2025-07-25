<template>
  <div class="flex justify-between pb-4 items-center">
    <div class="flex w-full md:items-center gap-x-5 md:gap-x-3 truncate mask-fade">
      <div class="grow">
        <div class="grid md:flex md:justify-between md:items-center gap-2">
          <BreadcrumbsWrapper>
            <li class="text-sm">
              <Link :href="route('index')" class="flex items-center text-gray-500 hover:text-primary-hover" href="/">
                <BasicIcon class="size-5" name="home" />
              </Link>
            </li>
            <li v-if="breadcrumbs?.mainSection?.data?.title" class="text-sm">
              <span class="flex items-center text-gray-500 hover:text-primary-hover cursor-pointer" @click.prevent="handleSectionClick(breadcrumbs.mainSection)">
                <BasicIcon class="flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400" name="breadcrumb-tag" />
								{{ breadcrumbs.mainSection.data.title }}
							</span>
            </li>
            <li v-if="breadcrumbs?.subSection?.data?.title" class="text-sm">
              <span class="flex items-center text-gray-500 hover:text-primary-hover cursor-pointer" @click.prevent="handleSubSectionClick(breadcrumbs.mainSection, breadcrumbs.subSection)">
                <BasicIcon class="flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400" name="breadcrumb-tag" />
								{{ breadcrumbs.subSection.data.title }}
							</span>
            </li>
            <slot />
          </BreadcrumbsWrapper>
        </div>
      </div>
    </div>
  </div>


</template>

<script>
import {Link} from "@inertiajs/vue3";
import slugify from "slugify";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import BreadcrumbsWrapper from "@/componentss/ui/wrappers/BreadcrumbsWrapper.vue";
import BreadcrumbsItem from "@/componentss/shared/Breadcrumbs/BreadcrumbsItem.vue";
import {helpers} from "@/mixins/Helpers.js";
export default {
  mixins: [helpers],
  name: "BaseBreadcrumbs",
  components: {BreadcrumbsItem, BreadcrumbsWrapper, BasicIcon, Link},
  data() {
    return {
    }
  },
  methods: {
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
  },
}

</script>


<style scoped>
.mask-fade {
  mask-image: linear-gradient(to right, black 90%, transparent 100%);
  -webkit-mask-image: linear-gradient(to right, black 90%, transparent 100%);
}
</style>
