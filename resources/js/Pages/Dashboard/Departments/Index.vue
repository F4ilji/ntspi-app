<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="building-office" class="text-primary" />
    </template>
    <template #header-title>Кафедры</template>
    <template #header-subtitle>Управление кафедрами института</template>
    <template #header-actions>
      <a
        :href="route('dashboard.departments.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" color="currentColor" />
        Создать кафедру
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название кафедры..."
        @search="search"
      />

      <SelectFilter
        v-model="facultyQuery"
        label="Факультет"
        placeholder="Все факультеты"
        @change="filterByFaculty"
      >
        <option
          v-for="faculty in faculties"
          :key="faculty.id"
          :value="faculty.id"
        >
          {{ faculty.title }}
        </option>
      </SelectFilter>

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

    <!-- Departments Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-white/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ departments.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ departments.data.length }} на странице
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
                Факультет
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Статус
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Обновлено
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="department in departments.data"
              :key="department.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ department.title }}
                </div>
                <div v-if="department.faculty" class="text-xs text-muted-foreground-1 mt-0.5">
                  {{ department.faculty.abbreviation || department.faculty.title }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-primary/10 text-primary border border-primary/20">
                  {{ department.faculty?.title || '—' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="STATUS_BADGE_CLASS(department.is_active)">
                  <DashboardIcon
                    :name="department.is_active ? 'check-circle' : 'x-circle'"
                    size="3"
                    class="mr-1"
                  />
                  {{ department.is_active ? 'Активная' : 'Неактивная' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(department.updated_at, 'full') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.departments.edit', department.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="CONFIRM_AND_DELETE(department, 'dashboard.departments.destroy')"
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
              v-if="departments.data.length === 0"
              :columns="5"
              title="Кафедры не найдены"
              description="Создайте первую кафедру или измените параметры поиска"
              :action-url="route('dashboard.departments.create')"
              action-text="Создать кафедру"
              icon-name="building-office"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="departments" />
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import DataFilters from '../Components/shared/DataFilters.vue';
import SearchInput from '../Components/shared/SearchInput.vue';
import SelectFilter from '../Components/shared/SelectFilter.vue';
import EmptyState from '../Components/shared/EmptyState.vue';
import Pagination from '../Components/shared/Pagination.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';

export default {
  name: 'DepartmentIndex',
  components: {
    DashboardLayout,
    FlashMessages,
    DataFilters,
    SearchInput,
    SelectFilter,
    EmptyState,
    Pagination,
    DashboardIcon,
  },

  props: {
    departments: {
      type: Object,
      required: true
    },
    faculties: {
      type: Array,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        faculty_id: '',
        is_active: '',
        search: ''
      })
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      facultyQuery: this.filters?.faculty_id || '',
      isActiveQuery: this.filters?.is_active || '',
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Кафедры');
  },

  methods: {
    search() {
      this.INERTIA_FILTER('dashboard.departments.index', {
        search: this.searchQuery,
        faculty_id: this.facultyQuery,
        is_active: this.isActiveQuery
      });
    },

    filterByFaculty() {
      this.search();
    },

    filterByStatus() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'facultyQuery', 'isActiveQuery'],
        'dashboard.departments.index'
      );
    },

    refreshPage() {
      this.INERTIA_FILTER('dashboard.departments.index', {
        search: this.searchQuery,
        faculty_id: this.facultyQuery,
        is_active: this.isActiveQuery
      });
    }
  }
}
</script>
