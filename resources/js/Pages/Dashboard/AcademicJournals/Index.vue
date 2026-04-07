<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="beaker" size="5" class="text-primary" />
    </template>
    <template #header-title>Научные журналы</template>
    <template #header-subtitle>Управление научными журналами</template>
    <template #header-actions>
      <a
        :href="route('dashboard.academic-journals.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать журнал
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название журнала..."
        @search="search"
      />
    </DataFilters>

    <!-- Journals Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ journals.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ journals.data.length }} на странице
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
                Название журнала
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                URL
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
              v-for="journal in journals.data"
              :key="journal.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ journal.title }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-muted-foreground-1 font-mono">
                  {{ journal.slug }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(journal.created_at, 'full') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.academic-journals.edit', journal.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <a
                    :href="route('dashboard.academic-journals.issues.index', journal.id)"
                    class="p-2 text-muted-foreground-1 hover:text-blue-600 hover:bg-blue-500/10 rounded-lg transition-all"
                    title="Выпуски журнала"
                  >
                    <DashboardIcon name="document-text" size="4" />
                  </a>
                  <button
                    @click.prevent="CONFIRM_AND_DELETE(journal, 'dashboard.academic-journals.destroy', {
                      message: 'Удалить журнал «' + journal.title + '»?'
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
              v-if="journals.data.length === 0"
              :columns="4"
              title="Научные журналы не найдены"
              description="Создайте первый научный журнал или измените параметры поиска"
              :action-url="route('dashboard.academic-journals.create')"
              action-text="Создать журнал"
              icon-path="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="journals" />
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import DataFilters from '../Components/shared/DataFilters.vue';
import SearchInput from '../Components/shared/SearchInput.vue';
import EmptyState from '../Components/shared/EmptyState.vue';
import Pagination from '../Components/shared/Pagination.vue';

export default {
  name: 'AcademicJournalIndex',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
    DataFilters,
    SearchInput,
    EmptyState,
    Pagination
  },

  props: {
    journals: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        search: ''
      })
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Научные журналы');
  },

  methods: {
    search() {
      this.INERTIA_FILTER('dashboard.academic-journals.index', {
        search: this.searchQuery
      });
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery'],
        'dashboard.academic-journals.index'
      );
    },

    refreshPage() {
      this.$inertia.get(route('dashboard.academic-journals.index'), {
        search: this.searchQuery
      }, {
        preserveState: true
      });
    }
  }
}
</script>
