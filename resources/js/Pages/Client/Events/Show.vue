<script>
import {Head, Link} from "@inertiajs/vue3";
import MainPageNavBar from "@/Navbars/MainPageNavbar.vue";
import BackButton from "@/componentss/ui/buttons/BackButton.vue";
import BasicTitle from "@/componentss/ui/titles/BasicTitle.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import BasicFooter from "@/footers/BasicFooter.vue";
import MetaTags from "@/componentss/shared/SEO/MetaTags.vue";
import EventItemBreadcrumbs from "@/componentss/features/events/components/EventItemBreadcrumbs.vue";
import BasicPageContainer from "@/componentss/ui/templates/BasicPageContainer.vue";
import BasicPageWrapper from "@/componentss/ui/wrappers/BasicPageWrapper.vue";
import ProgramTitle from "@/componentss/features/educationalPrograms/components/ProgramTitle.vue";
import ProgramItemBreadcrumbs from "@/componentss/features/educationalPrograms/components/ProgramItemBreadcrumbs.vue";

export default {
	name: "Show",
	components: {
    ProgramItemBreadcrumbs, ProgramTitle, BasicPageWrapper, BasicPageContainer,
    MetaTags,
    BasicFooter,
    Builder,
    BasicTitle,
    BackButton,
		EventItemBreadcrumbs,
		MainPageNavBar,
    Link
  },
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
	<MetaTags :seo="seo" />
	<MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <BasicPageContainer>
      <div class="space-y-5 md:space-y-10">
        <div class="space-y-3">
          <BackButton link="client.event.index" title="Все мероприятия" />
          <EventItemBreadcrumbs :breadcrumbs="breadcrumbs" :event-title="event.data.title" />
          <BasicTitle :header="event.data.title" />
        </div>

        <div class="flex gap-x-3 text-gray-500 ">
          <div class="flex items-center gap-3">
            <div>
              <div class="flex items-center flex-wrap gap-2 text-sm">
                <a :href="route('client.event.index', { 'is_online': 'online' })"
                   v-if="event.data.is_online === 1"
                   class="inline-flex items-center py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] hover:underline text-primary">
                  Онлайн
                </a>
                <a :href="route('client.event.index', { 'category[]': event.data.category.slug })"
                   v-if="event.data.category"
                   class="inline-flex items-center py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] hover:underline text-primary">
                  {{ event.data.category.title }}
                </a>
                <div class="inline-flex items-center py-1.5 px-3 rounded-md text-xs font-medium bg-[#E9F2FE] text-primary">
                  <span>Дата начала: {{ event.data.event_date_start }}, {{ event.data.event_time_start }}</span>
                </div>
                <div class="md:flex md:items-center md:justify-center w-full md:w-auto">
                  <span>Адрес: {{ event.data.address }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="gap-y-3 md:gap-y-4">
          <Builder :blocks="event.data.content"/>
        </div>
      </div>

    </BasicPageContainer>
    <BasicFooter />
  </BasicPageWrapper>

</template>

<style>


</style>