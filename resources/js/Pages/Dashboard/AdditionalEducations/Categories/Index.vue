<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="tag" size="5" class="text-primary" />
    </template>
    <template #header-title>Категории ДПО</template>
    <template #header-subtitle>Управление категориями программ дополнительного образования</template>
    <template #header-actions>
      <a
        :href="route('dashboard.additional-educations.categories.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Добавить категорию
      </a>
    </template>

    <FlashMessages />

    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск"
        placeholder="Введите название категории..."
        @search="search"
      />

      <SelectFilter
        v-model="directionQuery"
        label="Направление"
        placeholder="Все направления"
        @change="filterByDirection"
      >
        <option v-for="direction in directions" :key="direction.id" :value="direction.id">
          {{ direction.title }}
        </option>
      </SelectFilter>

      <SelectFilter
        v-model="isActiveQuery"
        label="Статус"
        placeholder="Все"
        @change="filterByActive"
      >
        <option value="">Все</option>
        <option value="1">Активные</option>
        <option value="0">Неактивные</option>
      </SelectFilter>
    </DataFilters>

    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ categories.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ categories.data.length }} на странице
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
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Название
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Направление
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Статус
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="category in categories.data"
              :key="category.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ TEXT_LIMIT(category.title, 50) }}
                </div>
                <div class="text-xs text-muted-foreground-1 mt-0.5">
                  /{{ category.slug }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border bg-primary/10 text-primary border-primary/20">
                  {{ category.direction?.title || '—' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="STATUS_BADGE_CLASS(category.is_active)">
                  {{ category.is_active ? 'Активна' : 'Неактивна' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.additional-educations.categories.edit', category.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="deleteCategory(category)"
                    class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>
              </td>
            </tr>

            <EmptyState
              v-if="categories.data.length === 0"
              :columns="4"
              title="Категории не найдены"
              description="Создайте первую категорию дополнительного образования или измените параметры поиска"
              :action-url="route('dashboard.additional-educations.categories.create')"
              action-text="Добавить категорию"
              icon-path="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"
            />
          </tbody>
        </table>
      </div>

      <Pagination :data="categories" />
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../Components/DashboardLayout.vue';
import DashboardIcon from '../../Components/DashboardIcon.vue';
import FlashMessages from '../../Components/shared/FlashMessages.vue';
import DataFilters from '../../Components/shared/DataFilters.vue';
import SearchInput from '../../Components/shared/SearchInput.vue';
import SelectFilter from '../../Components/shared/SelectFilter.vue';
import EmptyState from '../../Components/shared/EmptyState.vue';
import Pagination from '../../Components/shared/Pagination.vue';

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
    Pagination
  },

  props: {
    categories: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        direction_id: '',
        is_active: '',
        search: ''
      })
    },
    directions: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      directionQuery: this.filters?.direction_id || '',
      isActiveQuery: this.filters?.is_active || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Категории ДПО');
  },

  methods: {
    search() {
      this.INERTIA_FILTER('dashboard.additional-educations.categories.index', {
        search: this.searchQuery,
        direction_id: this.directionQuery,
        is_active: this.isActiveQuery
      });
    },

    filterByDirection() {
      this.search();
    },

    filterByActive() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'directionQuery', 'isActiveQuery'],
        'dashboard.additional-educations.categories.index'
      );
    },

    refreshPage() {
      this.search();
    },

    deleteCategory(category) {
      this.CONFIRM_AND_DELETE(category, 'dashboard.additional-educations.categories.destroy', {
        message: 'Удалить категорию "' + category.title + '"?'
      });
    }
  }
}
</script>
