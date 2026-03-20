<template>
  <div class="flex flex-col h-screen">
    <slot/>
  </div>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import BasicFooter from "@/footers/BasicFooter.vue";
import Builder from "@/componentss/shared/builder/pageBuilder/Builder.vue";
import BackButton from "@/componentss/ui/buttons/BackButton.vue";
import ProgramTitle from "@/componentss/features/educationalPrograms/components/ProgramTitle.vue";
import ProgramItemBreadcrumbs from "@/componentss/features/educationalPrograms/components/ProgramItemBreadcrumbs.vue";

export default {
	name: "BasicPageWrapper",
}

</script>
