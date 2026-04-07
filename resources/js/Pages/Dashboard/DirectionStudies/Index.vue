<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="academic-cap" size="5" class="text-primary" />
    </template>
    <template #header-title>Направления подготовки</template>
    <template #header-subtitle>Управление направлениями подготовки</template>
    <template #header-actions>
      <a
        :href="route('dashboard.direction-studies.create')"
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
        placeholder="Введите код или название направления..."
        @search="search"
      />

      <SelectFilter
        v-model="levelEduQuery"
        label="Уровень образования"
        placeholder="Все уровни"
        @change="filterByLevelEdu"
      >
        <option v-for="level in educationLevels" :key="level.value" :value="level.value">
          {{ level.label }}
        </option>
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
                Код
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Уровень образования
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
                  {{ direction.code }}
                </div>
                <div class="text-xs text-muted-foreground-1 mt-0.5">
                  {{ TEXT_LIMIT(direction.name, 50) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getLevelEduBadgeClass(direction.lvl_edu)">
                  {{ getLevelEduLabel(direction.lvl_edu) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.direction-studies.edit', direction.id)"
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
              title="Направления подготовки не найдены"
              description="Создайте первое направление подготовки или измените параметры поиска"
              :action-url="route('dashboard.direction-studies.create')"
              action-text="Добавить направление"
              icon-path="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"
            />
          </tbody>
        </table>
      </div>

      <Pagination :data="directions" />
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
  name: 'DirectionStudyIndex',
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
        lvl_edu: '',
        search: ''
      })
    },
    educationLevels: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      levelEduQuery: this.filters?.lvl_edu || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Направления подготовки');
  },

  methods: {
    getLevelEduBadgeClass(levelEdu) {
      const level = this.educationLevels.find(l => l.value === levelEdu);
      const color = level?.color || 'info';
      const colorClasses = {
        info: 'bg-info/10 text-info border-info/20',
        primary: 'bg-primary/10 text-primary border-primary/20',
        success: 'bg-success/10 text-success border-success/20',
        warning: 'bg-warning/10 text-warning border-warning/20',
        danger: 'bg-danger/10 text-danger border-danger/20',
        gray: 'bg-gray-500/10 text-gray-600 border-gray-500/20',
        secondary: 'bg-secondary/10 text-secondary border-secondary/20',
        light: 'bg-surface-muted text-muted-foreground-1 border-layer-line'
      };
      const baseClasses = 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border';
      return `${baseClasses} ${colorClasses[color] || colorClasses.info}`;
    },

    getLevelEduLabel(levelEdu) {
      const level = this.educationLevels.find(l => l.value === levelEdu);
      return level ? level.label : '—';
    },

    search() {
      this.INERTIA_FILTER('dashboard.direction-studies.index', {
        search: this.searchQuery,
        lvl_edu: this.levelEduQuery
      });
    },

    filterByLevelEdu() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'levelEduQuery'],
        'dashboard.direction-studies.index'
      );
    },

    refreshPage() {
      this.search();
    },

    deleteDirection(direction) {
      this.CONFIRM_AND_DELETE(direction, 'dashboard.direction-studies.destroy', {
        message: 'Удалить направление "' + direction.name + '"?'
      });
    }
  }
}
</script>
