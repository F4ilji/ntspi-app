<template>
  <h2 class="text-brand-primary my-6 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight">Мероприятия</h2>
  <div class="max-w-[85rem] pb-10 sm:px-6 lg:px-8 lg:pb-14 mx-auto">
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <template v-for="event in events.data">
        <EventListItem :event="event" />
      </template>
    </div>
  </div>
  <div class="flex justify-center">
    <Link :href="route('client.event.index')" class="group mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-primary">
      Все мероприятия
      <svg class="flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </Link>
  </div>
</template>

<script>
import { debounce } from "lodash";
import {Link} from "@inertiajs/vue3";
import PostListItem from "@/componentss/features/posts/components/PostListItem.vue";
import EventListItem from "@/componentss/features/events/components/EventListItem.vue";

export default {
	name: "EventSection",
  components: {EventListItem, Link},
	props: {
    events: {
      type: Object,
      required: true
    }
  },

}
</script>

<style scoped>

</style>