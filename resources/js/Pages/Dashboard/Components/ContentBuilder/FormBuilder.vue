<template>
  <div class="space-y-4">
    <!-- Toolbar -->
    <div class="flex items-center justify-between px-4 py-3 bg-white border border-layer-line rounded-lg">
      <h3 class="text-sm font-medium text-foreground">
        {{ label || 'Поля формы' }}
      </h3>
      <button
        type="button"
        @click="showBlockPicker = true"
        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-all"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Добавить поле
      </button>
    </div>

    <!-- Fields List -->
    <div class="space-y-3">
      <div
        v-for="(field, index) in fields"
        :key="field._uid"
        class="group relative bg-white border border-layer-line rounded-lg overflow-hidden"
      >
        <!-- Field Header -->
        <div class="flex items-center gap-2 px-4 py-3 bg-muted/30 border-b border-layer-line">
          <div class="flex items-center gap-2 flex-1">
            <span class="text-xs font-mono text-muted-foreground-1 bg-muted px-2 py-0.5 rounded">{{ field.type }}</span>
            <span class="text-sm font-medium text-foreground">{{ field.data?.title_field || 'Без названия' }}</span>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button
              type="button"
              @click="duplicateField(index)"
              class="p-1.5 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded transition-all"
              title="Клонировать"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            </button>
            <button
              type="button"
              @click="toggleField(index)"
              class="p-1.5 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded transition-all"
              :title="collapsedFields.includes(index) ? 'Развернуть' : 'Свернуть'"
            >
              <svg
                class="w-4 h-4 transition-transform"
                :class="{ 'rotate-180': !collapsedFields.includes(index) }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <button
              type="button"
              @click="removeField(index)"
              class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
              title="Удалить"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Field Content -->
        <div v-show="!collapsedFields.includes(index)" class="p-4">
          <component
            :is="getFieldComponent(field.type)"
            v-model="field.data"
            @update="emitChange"
          />
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="fields.length === 0"
        class="flex flex-col items-center justify-center py-12 px-4 border-2 border-dashed border-layer-line rounded-lg bg-muted/20"
      >
        <svg class="w-12 h-12 text-muted-foreground-1 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-sm font-medium text-foreground mb-1">Нет полей формы</p>
        <p class="text-xs text-muted-foreground-1 text-center">Нажмите "Добавить поле" чтобы начать</p>
      </div>
    </div>

    <!-- Block Picker Modal -->
    <div
      v-if="showBlockPicker"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
      @click.self="showBlockPicker = false"
    >
      <div class="bg-layer border border-layer-line rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-layer-line flex items-center justify-between">
          <h3 class="text-lg font-medium text-foreground">Выберите тип поля</h3>
          <button
            type="button"
            @click="showBlockPicker = false"
            class="p-1.5 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[60vh]">
          <div class="grid grid-cols-3 gap-3">
            <button
              v-for="fieldType in availableFields"
              :key="fieldType.type"
              type="button"
              @click="addField(fieldType.type)"
              class="flex flex-col items-center gap-2 p-4 border border-layer-line rounded-lg hover:border-primary hover:bg-primary/5 transition-all group"
            >
              <DashboardIcon :name="fieldType.icon" size="8" class="text-muted-foreground-1 group-hover:text-primary transition-colors" />
              <span class="text-xs font-medium text-foreground text-center group-hover:text-primary transition-colors">
                {{ fieldType.label }}
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue';
import DashboardIcon from '../DashboardIcon.vue';
import FormFieldText from './FormBuilder/blocks/FormFieldText.vue';
import FormFieldTextarea from './FormBuilder/blocks/FormFieldTextarea.vue';
import FormFieldEmail from './FormBuilder/blocks/FormFieldEmail.vue';
import FormFieldPhone from './FormBuilder/blocks/FormFieldPhone.vue';
import FormFieldDate from './FormBuilder/blocks/FormFieldDate.vue';
import FormFieldUrl from './FormBuilder/blocks/FormFieldUrl.vue';
import FormFieldSingleChoice from './FormBuilder/blocks/FormFieldSingleChoice.vue';
import FormFieldMultipleChoice from './FormBuilder/blocks/FormFieldMultipleChoice.vue';

export default {
  name: 'FormBuilder',
  components: {
    DashboardIcon,
    FormFieldText,
    FormFieldTextarea,
    FormFieldEmail,
    FormFieldPhone,
    FormFieldDate,
    FormFieldUrl,
    FormFieldSingleChoice,
    FormFieldMultipleChoice,
  },

  props: {
    modelValue: {
      type: Array,
      default: () => []
    },
    label: {
      type: String,
      default: ''
    }
  },

  emits: ['update:modelValue'],

  setup(props, { emit }) {
    const fields = ref([]);
    const collapsedFields = ref([]);
    const showBlockPicker = ref(false);
    let uidCounter = 0;

    const availableFields = [
      { type: 'text', label: 'Короткий текст', icon: 'pencil' },
      { type: 'textarea', label: 'Длинный текст', icon: 'document-text' },
      { type: 'email', label: 'Email', icon: 'envelope' },
      { type: 'phone', label: 'Телефон', icon: 'phone' },
      { type: 'date', label: 'Дата', icon: 'calendar' },
      { type: 'url', label: 'Ссылка', icon: 'link' },
      { type: 'single_choice', label: 'Одиночный выбор', icon: 'radio' },
      { type: 'multiple_choice', label: 'Множественный выбор', icon: 'check-circle' },
    ];

    const fieldDefaults = {
      text: () => ({ title_field: '', name_field: '', description: '', rules: { required: false } }),
      textarea: () => ({ title_field: '', name_field: '', description: '', rules: { required: false } }),
      email: () => ({ title_field: '', name_field: '', description: '', rules: { required: false } }),
      phone: () => ({ title_field: '', name_field: '', description: '', rules: { required: false } }),
      date: () => ({ title_field: '', name_field: '', description: '', rules: { required: false } }),
      url: () => ({ title_field: '', name_field: '', description: '', rules: { required: false } }),
      single_choice: () => ({ title_field: '', name_field: '', description: '', columns: [], rules: { required: false } }),
      multiple_choice: () => ({ title_field: '', name_field: '', description: '', columns: [], rules: { required: false } }),
    };

    function generateUid() {
      return `field-${Date.now()}-${uidCounter++}`;
    }

    function addField(type) {
      fields.value.push({
        _uid: generateUid(),
        type,
        data: fieldDefaults[type]()
      });
      showBlockPicker.value = false;
      emitChange();
    }

    function removeField(index) {
      fields.value.splice(index, 1);
      collapsedFields.value = collapsedFields.value.filter(i => i !== index);
      emitChange();
    }

    function duplicateField(index) {
      const original = fields.value[index];
      fields.value.splice(index + 1, 0, {
        _uid: generateUid(),
        type: original.type,
        data: JSON.parse(JSON.stringify(original.data))
      });
      emitChange();
    }

    function toggleField(index) {
      const idx = collapsedFields.value.indexOf(index);
      if (idx === -1) {
        collapsedFields.value.push(index);
      } else {
        collapsedFields.value.splice(idx, 1);
      }
    }

    function getFieldComponent(type) {
      const componentMap = {
        text: 'FormFieldText',
        textarea: 'FormFieldTextarea',
        email: 'FormFieldEmail',
        phone: 'FormFieldPhone',
        date: 'FormFieldDate',
        url: 'FormFieldUrl',
        single_choice: 'FormFieldSingleChoice',
        multiple_choice: 'FormFieldMultipleChoice',
      };
      return componentMap[type] || 'FormFieldText';
    }

    function emitChange() {
      const output = fields.value.map(({ _uid, ...field }) => field);
      emit('update:modelValue', output);
    }

    onMounted(() => {
      if (props.modelValue && props.modelValue.length > 0) {
        fields.value = props.modelValue.map(field => ({
          _uid: generateUid(),
          ...field
        }));
      }
    });

    watch(
      () => props.modelValue,
      (newValue) => {
        if (newValue && newValue.length > 0 && fields.value.length === 0) {
          fields.value = newValue.map(field => ({
            _uid: generateUid(),
            ...field
          }));
        }
      },
      { deep: true }
    );

    return {
      fields,
      collapsedFields,
      showBlockPicker,
      availableFields,
      addField,
      removeField,
      duplicateField,
      toggleField,
      getFieldComponent,
      emitChange,
    };
  }
}
</script>
