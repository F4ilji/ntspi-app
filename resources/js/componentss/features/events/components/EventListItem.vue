<template>
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
                <span class="bg-[#E9F2FE] text-sm text-primary px-2 py-1 rounded block">Начало - {{ event.event_date_start.time }}</span>
              </div>
            </div>
          </div>
          <div class="flex gap-2">
            <span v-if="event.is_online === 1" class="bg-[#E9F2FE] text-sm text-primary px-2 py-1 rounded block">Онлайн</span>
            <div v-if="event.is_online === 0">
              <span class="bg-[#E9F2FE] text-sm text-primary px-2 py-1 rounded block">{{ event.address }}</span>
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

<script>
import { debounce } from "lodash";
import {Link} from "@inertiajs/vue3";

export default {
	name: "EventListItem",
  components: {Link},
	props: {
    event: {
      type: Object,
      required: true
    }
  },

}
</script>

<style scoped>

</style>