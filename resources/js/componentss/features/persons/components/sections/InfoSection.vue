<!-- NavLink.vue -->
<template>
  <div class="flex items-center gap-x-10 gap-y-4 flex-wrap">
    <PersonAvatarBlock v-if="person.data?.details.photo" :photo="person.data.details.photo" />
    <div class="grow">
      <h1 class="text-2xl font-medium text-gray-800 dark:text-neutral-200">
        {{ person.data.name }}
      </h1>
      <div class="">
        <template v-for="division_work in person.data.divisions_works">
          <Link :href="route('client.division.show', division_work.slug)" class="text-sm text-blue-500 dark:text-neutral-400 mr-2">
            {{ division_work.position }}
          </Link>
        </template>
        <template v-for="faculty_work in person.data.faculties_works">
          <Link :href="route('client.faculty.show', faculty_work.slug)" class="text-sm text-blue-500 dark:text-neutral-400 mr-2">
            {{ faculty_work.position }}
          </Link>
        </template>
        <template v-for="division_work in person.data.departments_work">
          <Link :href="route('client.department.show', { facultySlug: division_work.faculty_slug, departmentSlug: division_work.slug })" class="text-sm text-blue-500 dark:text-neutral-400 mr-2">
            {{ division_work.position }}
          </Link>
        </template>
        <template v-for="division_tech in person.data.departments_teach">
          <Link :href="route('client.department.show', { facultySlug: division_tech.faculty_slug, departmentSlug: division_tech.slug })" class="text-sm text-blue-500 dark:text-neutral-400 mr-2">
            {{ division_tech.position }}
          </Link>
        </template>
      </div>

      <ul class="mt-5 flex flex-col gap-y-3">

        <li v-if="person.data.details.contactPhone" class="flex items-center gap-x-2.5">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="shrink-0 size-3.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
          </svg>
          <a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 dark:text-neutral-500 dark:hover:text-neutral-400" href="#">
            {{ person.data.details.contactPhone }}
          </a>
        </li>
        <li v-if="person.data.details.contactEmail" class="flex items-center gap-x-2.5">
          <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="20" height="16" x="2" y="4" rx="2"></rect>
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
          </svg>
          <a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 dark:text-neutral-500 dark:hover:text-neutral-400" href="#">
            {{ person.data.details.contactEmail }}
          </a>
        </li>
      </ul>


    </div>
  </div>

  <div class="mt-5 sm:mt-7">
    <EducationBlock v-if="person.data.details.education.length > 0" title="Направление подготовки" :educations="person.data.details.education" />
  </div>

  <div class="mt-8">
    <ListBlock v-if="person.data.details.awards.length > 0" name-list="Награды" :list="person.data.details.awards" />
  </div>

  <div class="mt-8">
    <ListBlock v-if="person.data.details.professionalRetraining.length > 0" name-list="Профессиональная переподготовка" :list="person.data.details.professionalRetraining" />
  </div>

  <div class="mt-8">
    <ListBlock v-if="person.data.details.professionalDevelopment.length > 0" name-list="Повышение квалификации" :list="person.data.details.professionalDevelopment" />
  </div>

  <div class="mt-8">
    <ListBlock v-if="person.data.details.professDisciplines.length > 0" name-list="Преподаваемые дисциплины" :list="person.data.details.professDisciplines" />
  </div>

  <div class="mt-8">
    <WorkExperienceBlock v-if="person.data.details.workExperience.total" title="Стаж" :work-exp-by-prof="person.data.details.workExperience.byProf" :work-exp-total="person.data.details.workExperience.total" />
  </div>

  <div class="mt-8">
    <ListBlock v-if="person.data.details.attendedConferences.length > 0" name-list="Участие в конференциях" :list="person.data.details.attendedConferences" />
  </div>

  <div class="mt-8">
    <PublicationBlock v-if="person.data.details.publications.length > 0" name-list="Публикации" :list="person.data.details.publications" />
  </div>

  <div class="mt-8">
    <OtherBlock v-if="person.data.details.other?.length > 0" name-list="Другое" :blocks="person.data.details.other" />
  </div>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import OtherBlock from "@/componentss/features/persons/components/sections/blocks/OtherBlock.vue";
import EducationBlock from "@/componentss/features/persons/components/sections/blocks/EducationBlock.vue";
import ListBlock from "@/componentss/features/persons/components/sections/blocks/ListBlock.vue";
import PersonAvatarBlock from "@/componentss/features/persons/components/PersonAvatarBlock.vue";
import WorkExperienceBlock from "@/componentss/features/persons/components/sections/blocks/WorkExperienceBlock.vue";
import PublicationBlock from "@/componentss/features/persons/components/sections/blocks/PublicationBlock.vue";


export default {
	name: 'InfoSection',
  components: {OtherBlock, EducationBlock, ListBlock, PersonAvatarBlock, Link, WorkExperienceBlock, PublicationBlock},
	props: {
		person: {
			type: Object,
			required: true
		},
	},
	computed: {
		linkClass() {
			return {
				'translate-x-2 text-primary-light': this.isActive,
				'bg-transperant text-gray-600 hover:text-gray-900': !this.isActive
			};
		}
	}
}
</script>

<style scoped>
/* Добавьте стили, если это необходимо */
</style>