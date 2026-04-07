<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="arrow-trending-up" size="5" class="text-primary" />
    </template>
    <template #header-title>Направления ДПО</template>
    <template #header-subtitle>Управление направлениями дополнительного образования</template>
    <template #header-actions>
      <a
        :href="route('dashboard.additional-educations.directions.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Добавить направление
      </a>
    </template>

    <FlashMessages />

    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск"
        placeholder="Введите название направления..."
        @search="search"
      />

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
              Всего: <span class="font-medium">{{ directions.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ directions.data.length }} на странице
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

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-line-2">
          <thead class="bg-surface/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Название
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
              v-for="direction in directions.data"
              :key="direction.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ TEXT_LIMIT(direction.title, 60) }}
                </div>
                <div class="text-xs text-muted-foreground-1 mt-0.5">
                  /{{ direction.slug }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="STATUS_BADGE_CLASS(direction.is_active)">
                  {{ direction.is_active ? 'Активно' : 'Неактивно' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.additional-educations.directions.edit', direction.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="deleteDirection(direction)"
                    class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>
              </td>
            </tr>

            <EmptyState
              v-if="directions.data.length === 0"
              :columns="3"
              title="Направления не найдены"
              description="Создайте первое направление дополнительного образования или измените параметры поиска"
              :action-url="route('dashboard.additional-educations.directions.create')"
              action-text="Добавить направление"
              icon-path="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z"
            />
          </tbody>
        </table>
      </div>

      <Pagination :data="directions" />
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
  name: 'DirectionIndex',
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
    directions: {
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
      isActiveQuery: this.filters?.is_active || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Направления ДПО');
  },

  methods: {
    search() {
      this.INERTIA_FILTER('dashboard.additional-educations.directions.index', {
        search: this.searchQuery,
        is_active: this.isActiveQuery
      });
    },

    filterByActive() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'isActiveQuery'],
        'dashboard.additional-educations.directions.index'
      );
    },

    refreshPage() {
      this.search();
    },

    deleteDirection(direction) {
      this.CONFIRM_AND_DELETE(direction, 'dashboard.additional-educations.directions.destroy', {
        message: 'Удалить направление "' + direction.title + '"?'
      });
    }
  }
}
</script>
