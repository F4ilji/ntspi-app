<template><FormFieldSingleChoice v-model="localData" @update:model-value="$emit('update:modelValue', $event)" /></template>
<script>
import FormFieldSingleChoice from './FormFieldSingleChoice.vue';
export default { name: 'FormFieldMultipleChoice', components: { FormFieldSingleChoice }, props: { modelValue: Object }, emits: ['update:modelValue'], data() { return { localData: { ...this.modelValue } } } }
</script>
