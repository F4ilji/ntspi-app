<template>
	<MetaTags :seo="seo" />

	<MainPageNavBar :sections="$page.props.navigation" />
  <Slider class="bvi-hide bg-gray-300" slider-id="quos-velit-quisquam" />

  <section class="max-w-screen-xl w-full mx-auto px-4 py-3 pb-10">
    <PostSection :posts="posts" />
<!--    <div>-->
<!--      <EventSection :events="events" />-->
<!--    </div>-->
    <EducationSection :educations="educations" />
  </section>
	<PageResourceList resource-id="glavnaia-stranica-resurs" />
	<ContactSectionBlock contact-id="glavnaia-stranica-kontakty" />
	<BasicFooter />

</template>

<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import Slider from "@/componentss/features/sliders/Slider.vue";
import PostListItem from "@/componentss/features/posts/components/PostListItem.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import PageResourceList from "@/componentss/shared/pageResource/PageResourceList.vue";
import ContactSectionBlock from "@/componentss/shared/contactSection/ContactSectionBlock.vue";
import PostSection from "@/componentss/features/mainPage/PostSection.vue";
import EventSection from "@/componentss/features/mainPage/EventSection.vue";
import EducationSection from "@/componentss/features/mainPage/EducationSection.vue";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";

export default {
	name: "Main",
	props: {
		posts: {
			type: Object,
		},
		events: {
			type: Object,
		},
		educations: {
			type: Object,
		},
		seo: {
			type: Object,
		}
	},

	components: {
    BasicIcon,
    EducationSection,
    EventSection,
    PostSection,
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
}
</script>