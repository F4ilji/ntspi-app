<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="folder-open" size="5" class="text-primary" />
    </template>
    <template #header-title>Подразделы</template>
    <template #header-subtitle>Управление подразделами структуры</template>
    <template #header-actions>
      <a
        :href="route('dashboard.sub-sections.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать подраздел
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название подраздела..."
        @search="search"
      />

      <SelectFilter
        v-model="mainSectionQuery"
        label="Главный раздел"
        placeholder="Все главные разделы"
        @change="filterByMainSection"
      >
        <option v-for="(title, id) in mainSections" :key="id" :value="id">
          {{ title }}
        </option>
      </SelectFilter>
    </DataFilters>

    <!-- Table Card -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ subSections.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ subSections.data.length }} на странице
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
                Название подраздела
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Текстовый идентификатор
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Главный раздел
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Страницы
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
              v-for="subSection in subSections.data"
              :key="subSection.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ subSection.title }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <code class="text-xs bg-muted px-2 py-1 rounded text-foreground">
                  {{ subSection.slug }}
                </code>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-foreground">
                  {{ subSection.main_section?.title || '—' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-foreground">
                  {{ subSection.pages?.length || 0 }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(subSection.created_at, 'full') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.sub-sections.edit', subSection.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="confirmDeleteSection(subSection)"
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
              v-if="subSections.data.length === 0"
              :columns="6"
              title="Подразделы не найдены"
              description="Создайте первый подраздел или измените параметры поиска"
              :action-url="route('dashboard.sub-sections.create')"
              action-text="Создать подраздел"
              icon-path="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="subSections" />
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
  name: 'SubSectionIndex',
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
    subSections: {
      type: Object,
      required: true
    },
    mainSections: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        search: '',
        main_section_id: ''
      })
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      mainSectionQuery: this.filters?.main_section_id || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Подразделы');
  },

  methods: {
    confirmDeleteSection(subSection) {
      this.CONFIRM_AND_DELETE(subSection, 'dashboard.sub-sections.destroy', {
        message: `Удалить подраздел "${subSection.title}"?`
      });
    },

    search() {
      this.INERTIA_FILTER('dashboard.sub-sections.index', {
        search: this.searchQuery,
        main_section_id: this.mainSectionQuery
      });
    },

    filterByMainSection() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'mainSectionQuery'],
        'dashboard.sub-sections.index'
      );
    },

    refreshPage() {
      this.INERTIA_FILTER('dashboard.sub-sections.index', {
        search: this.searchQuery,
        main_section_id: this.mainSectionQuery
      });
    }
  }
}
</script>
