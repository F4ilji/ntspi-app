<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Название группы <span class="text-rose-500">*</span>
      </label>
      <input
        v-model="localData.title_field"
        type="text"
        @input="emitUpdate"
        placeholder="Например: Ваши интересы"
        class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm"
      />
    </div>

    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <label class="block text-sm font-medium text-foreground">Варианты выбора</label>
        <button type="button" @click="addOption" class="text-sm text-primary hover:text-primary-hover">+ Добавить вариант</button>
      </div>
      <div v-for="(option, i) in (localData.columns || [])" :key="i" class="flex items-center gap-2">
        <input v-model="option.title_field" @input="emitUpdate" type="text" placeholder="Вариант" class="flex-1 px-3 py-2 border border-layer-line rounded-lg text-sm" />
        <button type="button" @click="removeOption(i)" class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded">
          <DashboardIcon name="trash" size="4" />
        </button>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <input v-model="localData.rules.required" @change="emitUpdate" type="checkbox" class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary" />
      <label class="text-sm text-foreground">Обязательное поле</label>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../../../DashboardIcon.vue';
export default {
  name: 'FormFieldSingleChoice',
  components: { DashboardIcon },
  props: { modelValue: Object },
  emits: ['update:modelValue'],
  data() {
    return { localData: { columns: [], rules: { required: false }, ...this.modelValue } }
  },
  methods: {
    emitUpdate() { this.$emit('update:modelValue', { ...this.localData }) },
    addOption() {
      if (!this.localData.columns) this.localData.columns = [];
      this.localData.columns.push({ title_field: '', name_field: '' });
      this.emitUpdate();
    },
    removeOption(i) { this.localData.columns.splice(i, 1); this.emitUpdate(); }
  }
}
</script>
