<template>
  <DashboardLayout>
    <template #header-title>Редактирование провайдера</template>
    <template #header-subtitle>{{ credential.provider }}</template>

    <FlashMessages />

    <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
      <div class="p-6 border-b border-layer-line">
        <div class="flex items-center gap-2">
          <DashboardIcon name="key" size="5" class="text-primary" />
          <h2 class="text-base font-medium text-foreground">Данные провайдера</h2>
        </div>
        <p class="text-xs text-muted-foreground-1 mt-1">
          Обновите ключи сервиса
        </p>
      </div>

      <form @submit.prevent="submit" class="p-6">
        <div class="space-y-6">
          <!-- Provider Name -->
          <div>
            <label for="provider" class="block text-sm font-medium text-foreground mb-2">
              Название провайдера <span class="text-rose-500">*</span>
            </label>
            <input
              id="provider"
              v-model="form.provider"
              type="text"
              placeholder="Например: qwen, gemini, vk, smtp"
              :class="[
                'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                errors.provider ? 'border-rose-500' : 'border-layer-line focus:border-primary'
              ]"
            />
            <p v-if="errors.provider" class="mt-1.5 text-xs text-rose-500">{{ errors.provider }}</p>
          </div>

          <!-- Payload Key-Value Fields -->
          <div>
            <label class="block text-sm font-medium text-foreground mb-2">
              Ключи <span class="text-rose-500">*</span>
            </label>
            <div class="space-y-3">
              <div
                v-for="(field, index) in form.payloadFields"
                :key="index"
                class="flex items-center gap-3"
              >
                <input
                  v-model="field.key"
                  type="text"
                  placeholder="Ключ (напр. api_key)"
                  :class="[
                    'flex-1 px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                    errors['payload.' + index + '.key'] ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                  ]"
                />
                <input
                  v-model="field.value"
                  type="text"
                  placeholder="Значение"
                  :class="[
                    'flex-1 px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground placeholder-muted-foreground-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all font-mono',
                    errors['payload.' + index + '.value'] ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                  ]"
                />
                <button
                  type="button"
                  @click="removeField(index)"
                  class="p-2.5 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                  title="Удалить поле"
                >
                  <DashboardIcon name="trash" size="4" />
                </button>
              </div>
            </div>
            <button
              type="button"
              @click="addField"
              class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/10 rounded-lg transition-all"
            >
              <DashboardIcon name="plus" size="3" />
              Добавить поле
            </button>
            <p v-if="errors.payload" class="mt-1.5 text-xs text-rose-500">{{ errors.payload }}</p>
          </div>

          <!-- Is Active -->
          <div class="flex items-center gap-3">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="w-4 h-4 text-primary bg-surface border-layer-line rounded focus:ring-primary/20"
            />
            <label for="is_active" class="text-sm font-medium text-foreground">
              Активен
            </label>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-layer-line">
          <a
            :href="route('dashboard.integration-credentials.index')"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all"
          >
            Отмена
          </a>
          <button
            type="submit"
            :disabled="processing"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            <DashboardIcon v-else name="check" size="4" />
            {{ processing ? 'Сохранение...' : 'Сохранить изменения' }}
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

export default {
  name: 'IntegrationCredentialEdit',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
  },
  props: {
    credential: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      form: {
        provider: this.credential.provider || '',
        payloadFields: this.buildPayloadFields(this.credential.payload),
        is_active: Boolean(this.credential.is_active),
      },
      errors: {},
      processing: false,
    };
  },
  mounted() {
    this.SET_DOCUMENT_TITLE('Редактирование провайдера');
  },
  methods: {
    buildPayloadFields(payload) {
      if (!payload || typeof payload !== 'object') {
        return [{ key: '', value: '' }];
      }
      const fields = Object.entries(payload).map(([key, value]) => ({
        key,
        value: String(value),
      }));
      return fields.length > 0 ? fields : [{ key: '', value: '' }];
    },
    addField() {
      this.form.payloadFields.push({ key: '', value: '' });
    },
    removeField(index) {
      if (this.form.payloadFields.length > 1) {
        this.form.payloadFields.splice(index, 1);
      }
    },
    buildPayload() {
      const payload = {};
      for (const field of this.form.payloadFields) {
        if (field.key.trim()) {
          payload[field.key.trim()] = field.value;
        }
      }
      return payload;
    },
    submit() {
      this.processing = true;
      this.errors = {};

      const data = {
        provider: this.form.provider,
        payload: this.buildPayload(),
        is_active: this.form.is_active,
      };

      this.$inertia.put(route('dashboard.integration-credentials.update', this.credential.id), data, {
        onFinish: () => {
          this.processing = false;
        },
        onError: (errors) => {
          this.errors = errors;
        },
      });
    },
  },
}
</script>
