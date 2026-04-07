<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="building-office" size="5" class="text-primary" />
    </template>
    <template #header-title>Факультеты</template>
    <template #header-subtitle>Управление факультетами института</template>
    <template #header-actions>
      <a
        :href="route('dashboard.faculties.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать факультет
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название факультета..."
        @search="search"
      />

      <SelectFilter
        v-model="isActiveQuery"
        label="Статус"
        placeholder="Все статусы"
        @change="filterByStatus"
      >
        <option value="1">Активные</option>
        <option value="0">Неактивные</option>
      </SelectFilter>
    </DataFilters>

    <!-- Faculties Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-white/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ faculties.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ faculties.data.length }} на странице
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
                Название
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Аббревиатура
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Статус
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Дата обновления
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="faculty in faculties.data"
              :key="faculty.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ faculty.title }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-primary/10 text-primary border border-primary/20">
                  {{ faculty.abbreviation }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusBadgeClass(faculty.is_active)">
                  <DashboardIcon v-if="faculty.is_active" name="check-circle" size="3" class="text-success" />
                  <DashboardIcon v-else name="x-circle" size="3" class="text-danger" />
                  {{ faculty.is_active ? 'Активный' : 'Неактивный' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ formatDate(faculty.updated_at) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.faculties.edit', faculty.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="confirmDelete(faculty)"
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
              v-if="faculties.data.length === 0"
              :columns="5"
              title="Факультеты не найдены"
              description="Создайте первый факультет или измените параметры поиска"
              :action-url="route('dashboard.faculties.create')"
              action-text="Создать факультет"
              icon-path="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="faculties" />
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import DataFilters from '../Components/shared/DataFilters.vue';
import SearchInput from '../Components/shared/SearchInput.vue';
import SelectFilter from '../Components/shared/SelectFilter.vue';
import EmptyState from '../Components/shared/EmptyState.vue';
import Pagination from '../Components/shared/Pagination.vue';

export default {
  name: 'FacultyIndex',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
    DataFilters,
    SearchInput,
    SelectFilter,
    EmptyState,
    Pagination
  },

  props: {
    faculties: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        is_active: '',
        search: ''
      })
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      isActiveQuery: this.filters?.is_active || '',
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Факультеты');
  },

  methods: {
    getStatusBadgeClass(isActive) {
      return isActive
        ? 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-success/10 text-success border border-success/20'
        : 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-danger/10 text-danger border border-danger/20';
    },

    formatDate(date) {
      if (!date) return '—';
      return new Date(date).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    search() {
      this.$inertia.get(route('dashboard.faculties.index'), {
        search: this.searchQuery,
        is_active: this.isActiveQuery
      }, {
        preserveState: true
      });
    },

    filterByStatus() {
      this.search();
    },

    resetFilters() {
      this.searchQuery = '';
      this.isActiveQuery = '';
      this.$inertia.get(route('dashboard.faculties.index'), {}, {
        preserveState: true
      });
    },

    confirmDelete(faculty) {
      if (confirm(`Вы уверены, что хотите удалить факультет "${faculty.title}"?`)) {
        this.deleteFaculty(faculty);
      }
    },

    deleteFaculty(faculty) {
      this.$inertia.delete(route('dashboard.faculties.destroy', faculty.id), {
        preserveScroll: true
      });
    },

    refreshPage() {
      this.$inertia.get(route('dashboard.faculties.index'), {
        search: this.searchQuery,
        is_active: this.isActiveQuery
      }, {
        preserveState: true
      });
    },

    route(name, params) {
      return route(name, params);
    }
  }
}
</script>
