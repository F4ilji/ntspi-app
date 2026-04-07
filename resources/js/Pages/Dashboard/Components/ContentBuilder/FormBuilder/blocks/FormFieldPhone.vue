<template><FormFieldText v-model="localData" @update:model-value="$emit('update:modelValue', $event)" /></template>
<script>
import FormFieldText from './FormFieldText.vue';
export default { name: 'FormFieldPhone', components: { FormFieldText }, props: { modelValue: Object }, emits: ['update:modelValue'], data() { return { localData: { ...this.modelValue } } } }
</script>
