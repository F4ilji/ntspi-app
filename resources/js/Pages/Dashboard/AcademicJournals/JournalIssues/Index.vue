<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="document-text" size="5" class="text-primary" />
    </template>
    <template #header-title>Выпуски журнала</template>
    <template #header-subtitle>{{ journal.title }}</template>
    <template #header-actions>
      <a
        :href="route('dashboard.academic-journals.index')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-surface text-foreground text-sm font-medium rounded-lg border border-layer-line hover:bg-muted-hover transition-all duration-200"
      >
        <DashboardIcon name="arrow-left" size="4" />
        Назад к журналам
      </a>
      <a
        :href="route('dashboard.academic-journals.issues.create', journal.id)"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать выпуск
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название выпуска..."
        @search="search"
      />

      <SelectFilter
        v-model="yearQuery"
        label="Год выпуска"
        placeholder="Все годы"
        @change="filterByYear"
      >
        <option v-for="year in years" :key="year" :value="year">
          {{ year }}
        </option>
      </SelectFilter>

      <SelectFilter
        v-model="activeQuery"
        label="Статус"
        placeholder="Все статусы"
        @change="filterByActive"
      >
        <option value="">Все</option>
        <option value="1">Активные</option>
        <option value="0">Неактивные</option>
      </SelectFilter>
    </DataFilters>

    <!-- Issues Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ issues.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ issues.data.length }} на странице
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
                Название выпуска
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Год
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Статус
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Дата добавления
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="issue in issues.data"
              :key="issue.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ issue.title }}
                </div>
                <div class="text-xs text-muted-foreground-1 mt-0.5">
                  {{ issue.path_file }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-foreground">{{ issue.year_publication }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="STATUS_BADGE_CLASS(issue.is_active)">
                  {{ issue.is_active ? 'Активный' : 'Неактивный' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(issue.created_at, 'full') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.academic-journals.issues.edit', { academicJournal: journal.id, issue: issue.id })"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <a
                    :href="RESOLVE_ASSET_URL(issue.path_file)"
                    target="_blank"
                    class="p-2 text-muted-foreground-1 hover:text-blue-600 hover:bg-blue-500/10 rounded-lg transition-all"
                    title="Открыть файл"
                  >
                    <DashboardIcon name="eye" size="4" />
                  </a>
                  <button
                    @click.prevent="CONFIRM_AND_DELETE(issue, 'dashboard.academic-journals.issues.destroy', {
                      message: 'Удалить выпуск «' + issue.title + '»?'
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
              v-if="issues.data.length === 0"
              :columns="5"
              title="Выпуски не найдены"
              description="Создайте первый выпуск журнала или измените параметры поиска"
              :action-url="route('dashboard.academic-journals.issues.create', journal.id)"
              action-text="Создать выпуск"
              icon-path="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="issues" />
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
  name: 'JournalIssueIndex',
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
    journal: {
      type: Object,
      required: true
    },
    issues: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        search: '',
        year_publication: '',
        is_active: ''
      })
    },
    years: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      yearQuery: this.filters?.year_publication || '',
      activeQuery: this.filters?.is_active || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Выпуски журнала - ' + this.journal.title);
  },

  methods: {
    search() {
      this.INERTIA_FILTER('dashboard.academic-journals.issues.index', {
        academicJournal: this.journal.id,
        search: this.searchQuery,
        year_publication: this.yearQuery,
        is_active: this.activeQuery
      });
    },

    filterByYear() {
      this.search();
    },

    filterByActive() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'yearQuery', 'activeQuery'],
        'dashboard.academic-journals.issues.index'
      );
    },

    refreshPage() {
      this.$inertia.get(route('dashboard.academic-journals.issues.index', this.journal.id), {
        search: this.searchQuery,
        year_publication: this.yearQuery,
        is_active: this.activeQuery
      }, {
        preserveState: true
      });
    }
  }
}
</script>
