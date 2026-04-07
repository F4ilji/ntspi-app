<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="document-text" size="5" class="text-primary" />
    </template>
    <template #header-title>Страницы</template>
    <template #header-subtitle>Управление страницами сайта</template>
    <template #header-actions>
      <a
        :href="route('dashboard.pages.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать страницу
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название страницы..."
        @search="search"
      />

      <SelectFilter
        v-model="tabQuery"
        label="Тип страниц"
        placeholder="Все страницы"
        @change="filterByTab"
      >
        <option value="is_created">Созданные страницы</option>
        <option value="is_registered">Зарезервированные страницы</option>
        <option value="is_url">Внешние ссылки</option>
      </SelectFilter>

      <SelectFilter
        v-model="subSectionQuery"
        label="Подраздел"
        placeholder="Все подразделы"
        @change="filterBySubSection"
      >
        <option v-for="subSection in subSections" :key="subSection.id" :value="subSection.id">
          {{ subSection.title }}
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
              Всего: <span class="font-medium">{{ pages.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ pages.data.length }} на странице
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
                Заголовок страницы
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Путь
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Подраздел
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Код
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
              v-for="page in pages.data"
              :key="page.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ page.title }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <code class="text-xs bg-muted px-2 py-1 rounded text-foreground">
                  {{ page.path }}
                </code>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-foreground">
                  {{ page.section?.title || '—' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getCodeBadgeClass(page.code)">
                  {{ page.code }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(page.created_at, 'full') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.pages.edit', page.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="confirmDeletePage(page)"
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
              v-if="pages.data.length === 0"
              :columns="6"
              title="Страницы не найдены"
              description="Создайте первую страницу или измените параметры поиска"
              :action-url="route('dashboard.pages.create')"
              action-text="Создать страницу"
              icon-path="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="pages" />
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
  name: 'PagesIndex',
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
    pages: {
      type: Object,
      required: true
    },
    subSections: {
      type: Array,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        search: '',
        tab: 'is_created',
        sub_section_id: ''
      })
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      tabQuery: this.filters?.tab || 'is_created',
      subSectionQuery: this.filters?.sub_section_id || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Страницы');
  },

  methods: {
    getCodeBadgeClass(code) {
      const classes = {
        '200': 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-success/10 text-success border border-success/20',
        '404': 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-warning/10 text-warning border border-warning/20',
        '500': 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-danger/10 text-danger border border-danger/20'
      };
      return classes[code] || classes['200'];
    },

    confirmDeletePage(page) {
      this.CONFIRM_AND_DELETE(page, 'dashboard.pages.destroy', {
        message: `Удалить страницу "${page.title}"?`
      });
    },

    search() {
      this.INERTIA_FILTER('dashboard.pages.index', {
        search: this.searchQuery,
        tab: this.tabQuery,
        sub_section_id: this.subSectionQuery
      });
    },

    filterByTab() {
      this.search();
    },

    filterBySubSection() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'tabQuery', 'subSectionQuery'],
        'dashboard.pages.index'
      );
    },

    refreshPage() {
      this.INERTIA_FILTER('dashboard.pages.index', {
        search: this.searchQuery,
        tab: this.tabQuery,
        sub_section_id: this.subSectionQuery
      });
    }
  }
}
</script>
