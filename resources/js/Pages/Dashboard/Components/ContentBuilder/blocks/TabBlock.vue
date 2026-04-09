<template>
  <div class="space-y-4">
    <!-- Settings -->
    <div class="flex items-center gap-2">
      <input
        type="checkbox"
        :checked="modelValue?.settings?.is_accordion"
        @change="updateSettings('is_accordion', $event.target.checked)"
        class="h-4 w-4 text-primary focus:ring-primary border-layer-line rounded"
      />
      <label class="text-sm text-foreground">
        Режим аккордеона (только одна вкладка открыта)
      </label>
    </div>

    <!-- Tabs -->
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <label class="block text-sm font-medium text-foreground">
          Вкладки <span class="text-danger">*</span>
        </label>
        <button
          type="button"
          @click="addTab"
          class="text-sm text-primary hover:text-primary-hover transition-colors"
        >
          + Добавить вкладку
        </button>
      </div>

      <div
        v-for="(tab, tabIndex) in tabs"
        :key="tab._uid"
        class="border border-layer-line rounded-lg overflow-hidden"
      >
        <!-- Tab Header -->
        <div class="flex items-center gap-2 px-4 py-3 bg-muted/30 border-b border-layer-line">
          <input
            :value="tab.title"
            @input="updateTab(tabIndex, 'title', $event.target.value)"
            type="text"
            required
            maxlength="255"
            placeholder="Название вкладки"
            class="flex-1 px-3 py-1.5 border border-layer-line rounded-lg bg-white text-foreground text-sm"
          />
          <button
            type="button"
            @click="removeTab(tabIndex)"
            class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
            :disabled="tabs.length <= 1"
            title="Удалить вкладку"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>

        <!-- Tab Content (Nested TabContentBuilder) -->
        <div class="p-4">
          <TabContentBuilder
            :model-value="tab.content"
            @update:model-value="updateTab(tabIndex, 'content', $event)"
            label="Содержимое вкладки"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TabContentBuilder from '../TabContentBuilder.vue';

export default {
  name: 'TabBlock',
  components: {
    TabContentBuilder
  },
  props: {
    modelValue: { type: Object, required: true }
  },
  emits: ['update:modelValue'],
  computed: {
    tabs() {
      const rawTabs = this.modelValue?.tab || [];
      // Нормализуем _uid для всех вкладок (если пришли с сервера без UID)
      return rawTabs.map(tab => ({
        ...tab,
        _uid: tab._uid || `tab-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`
      }));
    }
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
    },
    updateSettings(field, value) {
      this.update('settings', { ...(this.modelValue.settings || {}), [field]: value });
    },
    updateTab(index, field, value) {
      const tabs = [...this.tabs];
      tabs[index] = { ...tabs[index], [field]: value };
      this.update('tab', tabs);
    },
    addTab() {
      const newTab = {
        _uid: `tab-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`,
        title: '',
        content: []
      };
      this.update('tab', [...this.tabs, newTab]);
    },
    removeTab(index) {
      if (this.tabs.length > 1) {
        this.update('tab', this.tabs.filter((_, i) => i !== index));
      }
    }
  }
}
</script>
