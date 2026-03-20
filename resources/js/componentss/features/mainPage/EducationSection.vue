<template>
  <h2 class="text-brand-primary my-10 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight">Образование</h2>

  <div class="w-full mx-auto">
    <div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-6">
      <template v-for="(level, index) in educations.admission_campaign">
        <a target="_blank" :href="route('client.program.index', {level: LevelEducational[index].name})">
          <div class="group flex flex-col h-full bg-white hover:opacity-70 hover:border-primary-dark duration-300 border border-gray-200 shadow-sm rounded-xl">
            <div class="p-4 md:p-6">
                <span class="block mb-1 text-xs font-semibold uppercase text-primary">
                  {{ LevelEducational[index].type_label }}
       				  </span>
              <h3 class="text-3xl font-semibold text-gray-800">
                {{ LevelEducational[index].label }}
              </h3>
              <p class="mt-3 text-gray-500">
                Построй свою индивидуальную траекторию
              </p>
            </div>
            <div class="mt-auto p-4 md:p-6 grid grid-cols-2 lg:grid-cols-3 sm:space-y-0">
              <div class="pb-4">
                <p class="text-2xl font-semibold text-primary">{{ level.total_programs }}</p>
                <p class="mt-1 text-sm text-gray-500">Программ</p>
              </div>
              <div class="pb-4">
                <p class="text-2xl font-semibold text-primary">{{ level.places.och_count }}</p>
                <p class="mt-1 text-sm text-gray-500">Очных</p>
              </div>
              <div class="pb-4">
                <p class="text-2xl font-semibold text-primary">{{ level.places.zaoch_count }}</p>
                <p class="mt-1 text-sm text-gray-500">Заочных</p>
              </div>
              <div class="pb-4">
                <p class="text-2xl font-semibold text-primary">{{ level.places.budget_places }}</p>
                <p class="mt-1 text-sm text-gray-500">Бюджетных</p>
              </div>
              <div class="pb-4">
                <p class="text-2xl font-semibold text-primary">{{ level.places.non_budget_places }}</p>
                <p class="mt-1 text-sm text-gray-500">Платных</p>
              </div>

            </div>
          </div>
        </a>

      </template>
      <Link :href="route('client.additionalEducation.index')">
        <div class="group flex flex-col h-full bg-white hover:opacity-70 hover:border-primary-dark duration-300 border border-gray-200 shadow-sm rounded-xl">
          <div class="p-4 md:p-6">
        			<span class="block mb-1 text-xs font-semibold uppercase text-primary">
								Доп. образование
       				 </span>
            <h3 class="text-3xl font-semibold text-gray-800">
              Дополнительное образование
            </h3>
            <p class="mt-3 text-gray-500">
              Построй свою индивидуальную траекторию
            </p>
          </div>
          <div class="mt-auto p-4 md:p-6 grid grid-cols-2 lg:grid-cols-3">
            <div class="pb-4">
              <p class="text-2xl font-semibold text-primary">{{ educations.additional_education.educations_count }}</p>
              <p class="mt-1 text-sm text-gray-500">Программ</p>
            </div>
            <div class="pb-4">
              <p class="text-2xl font-semibold text-primary">{{ educations.additional_education.categories_count }}</p>
              <p class="mt-1 text-sm text-gray-500">Направлений</p>
            </div>


          </div>
        </div>
      </Link>

    </div>
    <!-- End Grid -->
  </div>
</template>

<script>
import { debounce } from "lodash";
import {Link} from "@inertiajs/vue3";
import PostListItem from "@/componentss/features/posts/components/PostListItem.vue";
import LevelEducational from "@/Enum/LevelEducational.js";

export default {
	name: "EducationSection",
  components: {PostListItem, Link},
  computed: {
    LevelEducational() {
      return LevelEducational
    }
  },
  props: {
    educations: {
      type: Object,
      required: true
    }
  },

}
</script>

<style scoped>

</style>