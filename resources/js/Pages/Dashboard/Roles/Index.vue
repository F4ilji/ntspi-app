<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="shield-check" size="5" class="text-primary" />
    </template>
    <template #header-title>Роли</template>
    <template #header-subtitle>Управление ролями и пермишенами</template>
    <template #header-actions>
      <a
        :href="route('dashboard.roles.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать роль
      </a>
    </template>

    <template #breadcrumbs>
      <Breadcrumbs :crumbs="[{ label: 'Роли', href: route('dashboard.roles.index') }]" />
    </template>

    <FlashMessages />

    <!-- Roles Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <span class="text-sm text-foreground">
            Всего: <span class="font-medium">{{ roles.length }}</span>
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-line-2">
          <thead class="bg-surface/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Название
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Пермишены
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="role in roles"
              :key="role.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ role.name }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                  <span
                    v-if="role.permissions && role.permissions.length > 0"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary"
                  >
                    {{ role.permissions.length }} пермишенов
                  </span>
                  <span v-else class="text-xs text-muted-foreground-1">Нет пермишенов</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.roles.edit', role.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="CONFIRM_AND_DELETE(role, 'dashboard.roles.destroy', {
                      message: 'Удалить роль «' + role.name + '»?'
                    })"
                    class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>
              </td>
            </tr>

            <EmptyState
              v-if="roles.length === 0"
              :columns="3"
              title="Роли не найдены"
              description="Создайте первую роль для управления доступом"
              :action-url="route('dashboard.roles.create')"
              action-text="Создать роль"
              icon-path="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
            />
          </tbody>
        </table>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import Breadcrumbs from '../Components/shared/Breadcrumbs.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import EmptyState from '../Components/shared/EmptyState.vue';

defineProps({
  roles: { type: Array, required: true },
});

onMounted(() => {
  document.title = 'Роли — Dashboard';
});
</script>
