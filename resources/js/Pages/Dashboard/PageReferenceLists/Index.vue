<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="rectangle-stack" size="5" class="text-primary" />
    </template>
    <template #header-title>Списки ресурсов</template>
    <template #header-subtitle>Управление списками ресурсов</template>
    <template #header-actions>
      <a
        :href="route('dashboard.page-reference-lists.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать список
      </a>
    </template>

    <FlashMessages />

    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название..."
        @search="search"
      />
      <SelectFilter
        v-model="activeQuery"
        label="Статус"
        placeholder="Все статусы"
        @change="filterByActive"
      >
        <option value="1">Активные</option>
        <option value="0">Неактивные</option>
      </SelectFilter>
    </DataFilters>

    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ lists.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ lists.data.length }} на странице
            </span>
          </div>
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

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-line-2">
          <thead class="bg-surface/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">Название</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">Slug</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">Статус</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">Дата создания</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">Действия</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="list in lists.data"
              :key="list.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-muted-foreground-1">{{ list.id }}</span>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ list.title }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <code class="text-xs bg-muted px-2 py-1 rounded text-foreground">{{ list.slug }}</code>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="STATUS_BADGE_CLASS(list.is_active)">
                  {{ list.is_active ? 'Активен' : 'Неактивен' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(list.created_at, 'full') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.page-reference-lists.edit', list.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="confirmDeleteList(list)"
                    class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>
              </td>
            </tr>

            <EmptyState
              v-if="lists.data.length === 0"
              :columns="6"
              title="Списки ресурсов не найдены"
              description="Создайте первый список или измените параметры поиска"
              :action-url="route('dashboard.page-reference-lists.create')"
              action-text="Создать список"
              icon-path="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"
            />
          </tbody>
        </table>
      </div>

      <Pagination :data="lists" />
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
  name: 'PageReferenceListsIndex',
  components: {
    DashboardLayout, DashboardIcon, FlashMessages, DataFilters,
    SearchInput, SelectFilter, EmptyState, Pagination
  },

  props: {
    lists: { type: Object, required: true },
    filters: { type: Object, default: () => ({ search: '', is_active: '' }) }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      activeQuery: this.filters?.is_active || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Списки ресурсов');
  },

  methods: {
    confirmDeleteList(list) {
      this.CONFIRM_AND_DELETE(list, 'dashboard.page-reference-lists.destroy', {
        message: `Удалить список ресурсов "${list.title}"?`
      });
    },

    search() {
      this.INERTIA_FILTER('dashboard.page-reference-lists.index', {
        search: this.searchQuery,
        is_active: this.activeQuery
      });
    },

    filterByActive() { this.search(); },

    resetFilters() {
      this.RESET_FILTERS(['searchQuery', 'activeQuery'], 'dashboard.page-reference-lists.index');
    },

    refreshPage() {
      this.INERTIA_FILTER('dashboard.page-reference-lists.index', {
        search: this.searchQuery,
        is_active: this.activeQuery
      });
    }
  }
}
</script>
