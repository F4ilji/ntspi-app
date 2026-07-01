<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="key" size="5" class="text-primary" />
    </template>
    <template #header-title>Интеграционные ключи</template>
    <template #header-subtitle>Управление API-ключами сервисов</template>
    <template #header-actions>
      <a
        :href="route('dashboard.integration-credentials.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Добавить провайдер
      </a>
    </template>

    <FlashMessages />

    <!-- Credentials Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <div class="px-6 py-4 border-b border-layer-line bg-surface/50">
        <div class="flex items-center justify-between">
          <span class="text-sm text-foreground">
            Всего: <span class="font-medium">{{ credentials.length }}</span>
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-layer-line">
          <thead class="bg-surface/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Провайдер
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Ключи
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Статус
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Дата создания
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-layer-line">
            <tr
              v-for="credential in credentials"
              :key="credential.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ credential.provider }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1.5">
                  <span
                    v-for="(value, key) in credential.payload"
                    :key="key"
                    class="inline-flex items-center gap-1 px-2 py-0.5 bg-background-2 rounded text-xs text-muted-foreground-1"
                  >
                    <span class="font-medium">{{ key }}:</span>
                    <span class="font-mono">{{ maskValue(value) }}</span>
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <span :class="STATUS_BADGE_CLASS(credential.is_active)">
                  {{ credential.is_active ? 'Активен' : 'Неактивен' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(credential.created_at, 'short') }}</div>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.integration-credentials.edit', credential.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="CONFIRM_AND_DELETE(credential, 'dashboard.integration-credentials.destroy')"
                    class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>
              </td>
            </tr>

            <EmptyState
              v-if="credentials.length === 0"
              :columns="5"
              title="Интеграционные ключи не найдены"
              description="Добавьте первый провайдер для настройки интеграций"
              :action-url="route('dashboard.integration-credentials.create')"
              action-text="Добавить провайдер"
            />
          </tbody>
        </table>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import EmptyState from '../Components/shared/EmptyState.vue';

export default {
  name: 'IntegrationCredentialsIndex',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
    EmptyState,
  },
  props: {
    credentials: {
      type: Array,
      required: true,
    },
  },
  mounted() {
    this.SET_DOCUMENT_TITLE('Интеграционные ключи');
  },
  methods: {
    maskValue(value) {
      if (!value || value.length <= 8) return '••••••••';
      return value.substring(0, 4) + '••••' + value.substring(value.length - 4);
    },
  },
}
</script>
