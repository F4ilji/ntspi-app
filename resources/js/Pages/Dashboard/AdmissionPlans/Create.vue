<template>
  <EditForm
    :admission-campaigns="admissionCampaigns"
    :educational-programs="educationalPrograms"
  />
</template>

<script>
import EditForm from './Edit.vue';

export default {
  name: 'AdmissionPlanCreate',
  components: { EditForm },
  props: {
    admissionCampaigns: { type: Array, required: true },
    educationalPrograms: { type: Array, required: true }
  }
}
</script>
