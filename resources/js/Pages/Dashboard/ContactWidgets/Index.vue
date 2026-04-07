<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="phone" size="5" class="text-primary" />
    </template>
    <template #header-title>Контактные виджеты</template>
    <template #header-subtitle>Управление контактной информацией</template>
    <template #header-actions>
      <a
        :href="route('dashboard.contact-widgets.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать виджет
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название виджета..."
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

    <!-- Table Card -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ widgets.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ widgets.data.length }} на странице
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
                ID
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Название
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Slug
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
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="widget in widgets.data"
              :key="widget.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-muted-foreground-1">{{ widget.id }}</span>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ widget.title }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <code class="text-xs bg-muted px-2 py-1 rounded text-foreground">
                  {{ widget.slug }}
                </code>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="STATUS_BADGE_CLASS(widget.is_active)">
                  {{ widget.is_active ? 'Активен' : 'Неактивен' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(widget.created_at, 'full') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.contact-widgets.edit', widget.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="confirmDeleteWidget(widget)"
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
              v-if="widgets.data.length === 0"
              :columns="6"
              title="Контактные виджеты не найдены"
              description="Создайте первый виджет или измените параметры поиска"
              :action-url="route('dashboard.contact-widgets.create')"
              action-text="Создать виджет"
              icon-path="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="widgets" />
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
  name: 'ContactWidgetsIndex',
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
    widgets: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        search: '',
        is_active: ''
      })
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      activeQuery: this.filters?.is_active || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Контактные виджеты');
  },

  methods: {
    confirmDeleteWidget(widget) {
      this.CONFIRM_AND_DELETE(widget, 'dashboard.contact-widgets.destroy', {
        message: `Удалить контактный виджет "${widget.title}"?`
      });
    },

    search() {
      this.INERTIA_FILTER('dashboard.contact-widgets.index', {
        search: this.searchQuery,
        is_active: this.activeQuery
      });
    },

    filterByActive() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'activeQuery'],
        'dashboard.contact-widgets.index'
      );
    },

    refreshPage() {
      this.INERTIA_FILTER('dashboard.contact-widgets.index', {
        search: this.searchQuery,
        is_active: this.activeQuery
      });
    }
  }
}
</script>
