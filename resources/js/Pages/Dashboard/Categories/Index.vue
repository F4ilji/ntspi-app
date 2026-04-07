<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="tag" size="5" class="text-primary" />
    </template>
    <template #header-title>Категории новостей</template>
    <template #header-subtitle>Управление категориями публикаций</template>
    <template #header-actions>
      <a
        :href="route('dashboard.categories.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать категорию
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название категории..."
        @search="search"
      />

      <SelectFilter
        v-model="statusQuery"
        label="Статус"
        placeholder="Все статусы"
        @change="search"
      >
        <option value="">Все статусы</option>
        <option value="1">Активные</option>
        <option value="0">Неактивные</option>
      </SelectFilter>
    </DataFilters>

    <!-- Categories Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-layer-line bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ categories.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ categories.data.length }} на странице
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
        <table class="min-w-full divide-y divide-layer-line">
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
          <tbody class="divide-y divide-layer-line">
            <tr
              v-for="category in categories.data"
              :key="category.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm text-muted-foreground-1">
                  {{ category.id }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ category.title }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-muted-foreground-1 font-mono">
                  {{ category.slug }}
                </div>
              </td>
              <td class="px-6 py-4">
                <span :class="STATUS_BADGE_CLASS(category.is_active)">
                  {{ category.is_active ? 'Активна' : 'Неактивна' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(category.created_at, 'short') }}</div>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.categories.edit', category.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="CONFIRM_AND_DELETE(category, 'dashboard.categories.destroy')"
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
              v-if="categories.data.length === 0"
              :columns="6"
              title="Категории не найдены"
              description="Создайте первую категорию или измените параметры поиска"
              :action-url="route('dashboard.categories.create')"
              action-text="Создать категорию"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="categories" />
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
  name: 'CategoryIndex',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
    DataFilters,
    SearchInput,
    SelectFilter,
    EmptyState,
    Pagination,
  },
  props: {
    categories: {
      type: Object,
      required: true,
    },
    filters: {
      type: Object,
      default: () => ({
        search: '',
        is_active: '',
      }),
    },
  },
  data() {
    return {
      searchQuery: this.filters?.search || '',
      statusQuery: this.filters?.is_active || '',
    };
  },
  mounted() {
    this.SET_DOCUMENT_TITLE('Категории новостей');
  },
  methods: {
    search() {
      this.INERTIA_FILTER('dashboard.categories.index', {
        search: this.searchQuery,
        is_active: this.statusQuery,
      });
    },
    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'statusQuery'],
        'dashboard.categories.index'
      );
    },
    refreshPage() {
      this.$inertia.get(route('dashboard.categories.index'), {
        search: this.searchQuery,
        is_active: this.statusQuery,
      }, {
        preserveState: true,
      });
    },
  },
}
</script>
