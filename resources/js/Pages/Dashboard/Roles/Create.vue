<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="plus" size="5" class="text-primary" />
    </template>
    <template #header-title>Создание роли</template>
    <template #header-subtitle>Добавление новой роли в систему</template>
    <template #header-actions>
      <a
        :href="route('dashboard.roles.index')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-surface text-foreground text-sm font-medium rounded-lg border border-layer-line hover:bg-muted-hover transition-all duration-200"
      >
        <DashboardIcon name="arrow-left" size="4" />
        Назад к списку
      </a>
    </template>

    <FlashMessages />

    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <form @submit.prevent="submit">
        <div class="p-6 space-y-6">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-foreground mb-1">
              Название роли <span class="text-danger">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              maxlength="255"
              placeholder="Например: editor, viewer, admin"
              class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            />
            <p v-if="errors.name" class="mt-1 text-sm text-danger">{{ errors.name }}</p>
          </div>

          <!-- Permissions -->
          <div>
            <h3 class="text-lg font-medium text-foreground mb-3">Пермишены</h3>
            <p class="text-sm text-muted-foreground-1 mb-4">Выберите пермишены для этой роли</p>

            <div v-for="(group, prefix) in groupedPermissions" :key="prefix" class="mb-4">
              <div
                class="flex items-center justify-between px-4 py-2.5 bg-surface/50 border border-layer-line rounded-t-lg cursor-pointer hover:bg-muted-hover transition-colors"
                @click="toggleGroup(prefix)"
              >
                <div class="flex items-center gap-2">
                  <input
                    type="checkbox"
                    :checked="isGroupSelected(prefix)"
                    @change.stop="toggleGroup(prefix)"
                    class="w-4 h-4 rounded border-layer-line text-primary focus:ring-primary focus:ring-offset-0"
                  />
                  <span class="text-sm font-medium text-foreground">{{ prefixLabels[prefix] || prefix }}</span>
                  <span class="text-xs text-muted-foreground-1">({{ group.length }})</span>
                </div>
              </div>
              <div class="border border-t-0 border-layer-line rounded-b-lg px-4 py-3 bg-white">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                  <label
                    v-for="perm in group"
                    :key="perm.name"
                    class="flex items-center gap-2 cursor-pointer hover:bg-muted-hover/50 px-2 py-1 rounded transition-colors"
                  >
                    <input
                      type="checkbox"
                      :value="perm.name"
                      v-model="form.permissions"
                      class="w-4 h-4 rounded border-layer-line text-primary focus:ring-primary focus:ring-offset-0"
                    />
                    <span class="text-sm text-foreground">{{ perm.name }}</span>
                  </label>
                </div>
              </div>
            </div>

            <p v-if="errors.permissions" class="mt-1 text-sm text-danger">{{ errors.permissions }}</p>
          </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 bg-surface/50 border-t border-layer-line flex items-center justify-end gap-3">
          <a
            :href="route('dashboard.roles.index')"
            class="px-4 py-2 text-sm font-medium text-foreground bg-surface border border-layer-line rounded-lg hover:bg-muted-hover transition-all"
          >
            Отмена
          </a>
          <button
            type="submit"
            :disabled="processing"
            class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ processing ? 'Создание...' : 'Создать роль' }}
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { reactive, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

const props = defineProps({
  permissions: { type: Array, required: true },
  errors: { type: Object, default: () => ({}) },
});

const form = reactive({
  name: '',
  permissions: [],
});

const processing = false;

const prefixLabels = {
  view_any: 'Просмотр',
  create: 'Создание',
  update: 'Редактирование',
  delete: 'Удаление',
  restore: 'Восстановление',
  force_delete: 'Принудительное удаление',
  replicate: 'Дублирование',
  reorder: 'Переупорядочивание',
};

const groupedPermissions = computed(() => {
  const groups = {};
  for (const perm of props.permissions) {
    const parts = perm.name.split('_');
    let prefix;
    if (parts[0] === 'view' && parts[1] === 'any') {
      prefix = 'view_any';
    } else if (parts[0] === 'force' && parts[1] === 'delete') {
      prefix = 'force_delete';
    } else {
      prefix = parts[0];
    }
    if (!groups[prefix]) groups[prefix] = [];
    groups[prefix].push(perm);
  }
  return groups;
});

const isGroupSelected = (prefix) => {
  const group = groupedPermissions.value[prefix] || [];
  return group.length > 0 && group.every(p => form.permissions.includes(p.name));
};

const toggleGroup = (prefix) => {
  const group = groupedPermissions.value[prefix] || [];
  if (isGroupSelected(prefix)) {
    const groupNames = group.map(p => p.name);
    form.permissions = form.permissions.filter(name => !groupNames.includes(name));
  } else {
    const groupNames = group.map(p => p.name);
    form.permissions = [...new Set([...form.permissions, ...groupNames])];
  }
};

const submit = () => {
  router.post(route('dashboard.roles.store'), {
    name: form.name,
    permissions: form.permissions,
  });
};

onMounted(() => {
  document.title = 'Создание роли — Dashboard';
});
</script>
