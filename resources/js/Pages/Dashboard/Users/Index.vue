<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="user-circle" size="5" class="text-primary" />
    </template>
    <template #header-title>Пользователи</template>
    <template #header-subtitle>Управление пользователями системы</template>
    <template #header-actions>
      <a
        :href="route('dashboard.users.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать пользователя
      </a>
    </template>

    <template #breadcrumbs>
      <Breadcrumbs :crumbs="[{ label: 'Пользователи', href: route('dashboard.users.index') }]" />
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по имени или email"
        placeholder="Введите имя или email..."
        @search="search"
      />

      <SelectFilter
        v-model="roleQuery"
        label="Роль"
        placeholder="Все роли"
        @change="filterByRole"
      >
        <option v-for="role in roles" :key="role.id" :value="role.id">
          {{ role.name }}
        </option>
      </SelectFilter>
    </DataFilters>

    <!-- Users Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ users.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ users.data.length }} на странице
            </span>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="refreshPage"
              class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
              title="Обновить"
            >
              <DashboardIcon name="arrow-path" size="4" />
            </button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-line-2">
          <thead class="bg-surface/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                ФИО
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Email
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Роли
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Дата создания
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="user in users.data"
              :key="user.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-medium text-primary">{{ GET_INITIALS(user.name) }}</span>
                  </div>
                  <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                    {{ user.name }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-foreground">{{ user.email }}</span>
                <span v-if="user.email_verified_at" class="ml-1 text-success" title="Email подтвержден">✓</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="role in user.roles"
                    :key="role.id"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary"
                  >
                    {{ role.name }}
                  </span>
                  <span v-if="!user.roles || user.roles.length === 0" class="text-xs text-muted-foreground-1">—</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(user.created_at, 'short') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.users.edit', user.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="CONFIRM_AND_DELETE(user, 'dashboard.users.destroy', {
                      message: 'Удалить пользователя «' + user.name + '»?'
                    })"
                    class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>
              </td>
            </tr>

            <!-- Empty State -->
            <EmptyState
              v-if="users.data.length === 0"
              :columns="5"
              title="Пользователи не найдены"
              description="Создайте первого пользователя или измените параметры поиска"
              :action-url="route('dashboard.users.create')"
              action-text="Создать пользователя"
              icon-path="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="users" />
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import Breadcrumbs from '../Components/shared/Breadcrumbs.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import DataFilters from '../Components/shared/DataFilters.vue';
import SearchInput from '../Components/shared/SearchInput.vue';
import SelectFilter from '../Components/shared/SelectFilter.vue';
import EmptyState from '../Components/shared/EmptyState.vue';
import Pagination from '../Components/shared/Pagination.vue';

export default {
  name: 'UserIndex',
  components: {
    DashboardLayout,
    DashboardIcon,
    Breadcrumbs,
    FlashMessages,
    DataFilters,
    SearchInput,
    SelectFilter,
    EmptyState,
    Pagination
  },

  props: {
    users: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        search: '',
        role_id: ''
      })
    },
    roles: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      roleQuery: this.filters?.role_id || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Пользователи');
  },

  methods: {
    search() {
      this.INERTIA_FILTER('dashboard.users.index', {
        search: this.searchQuery,
        role_id: this.roleQuery
      });
    },

    filterByRole() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'roleQuery'],
        'dashboard.users.index'
      );
    },

    refreshPage() {
      this.$inertia.get(route('dashboard.users.index'), {
        search: this.searchQuery,
        role_id: this.roleQuery
      }, {
        preserveState: true
      });
    }
  }
}
</script>
