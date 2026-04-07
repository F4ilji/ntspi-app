<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="squares-plus" size="5" class="text-primary" />
    </template>
    <template #header-title>Пользовательские формы</template>
    <template #header-subtitle>Управление пользовательскими формами</template>
    <template #header-actions>
      <a
        :href="route('dashboard.custom-forms.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать форму
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по названию"
        placeholder="Введите название формы..."
        @search="search"
      />

      <SelectFilter
        v-model="statusQuery"
        label="Статус"
        placeholder="Все статусы"
        @change="filterByStatus"
      >
        <option v-for="status in statuses" :key="status.value" :value="status.value">
          {{ status.label }}
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
              Всего: <span class="font-medium">{{ forms.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ forms.data.length }} на странице
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
                Form ID
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
              v-for="form in forms.data"
              :key="form.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-muted-foreground-1">{{ form.id }}</span>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ form.title }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <code class="text-xs bg-muted px-2 py-1 rounded text-foreground">
                  {{ form.form_id }}
                </code>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusBadgeClass(form.status)">
                  {{ getStatusLabel(form.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(form.created_at, 'full') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.custom-forms.responses.index', form.id)"
                    class="p-2 text-muted-foreground-1 hover:text-amber-600 hover:bg-amber-500/10 rounded-lg transition-all"
                    title="Ответы"
                  >
                    <DashboardIcon name="chat-bubble-left-ellipsis" size="4" />
                  </a>
                  <a
                    :href="route('dashboard.custom-forms.edit', form.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="confirmDeleteForm(form)"
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
              v-if="forms.data.length === 0"
              :columns="6"
              title="Пользовательские формы не найдены"
              description="Создайте первую форму или измените параметры поиска"
              :action-url="route('dashboard.custom-forms.create')"
              action-text="Создать форму"
              icon-path="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="forms" />
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
  name: 'CustomFormsIndex',
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
    forms: {
      type: Object,
      required: true
    },
    statuses: {
      type: Array,
      default: () => []
    },
    filters: {
      type: Object,
      default: () => ({
        search: '',
        status: ''
      })
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      statusQuery: this.filters?.status || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Пользовательские формы');
  },

  methods: {
    getStatusBadgeClass(status) {
      const classes = {
        'published': 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-success/10 text-success border border-success/20',
        'hidden': 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-muted text-muted-foreground-1 border border-layer-line'
      };
      return classes[status] || classes['hidden'];
    },

    getStatusLabel(status) {
      const labels = {
        'published': 'Опубликовано',
        'hidden': 'Скрыто'
      };
      return labels[status] || status;
    },

    confirmDeleteForm(form) {
      this.CONFIRM_AND_DELETE(form, 'dashboard.custom-forms.destroy', {
        message: `Удалить пользовательскую форму "${form.title}"?`
      });
    },

    search() {
      this.INERTIA_FILTER('dashboard.custom-forms.index', {
        search: this.searchQuery,
        status: this.statusQuery
      });
    },

    filterByStatus() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'statusQuery'],
        'dashboard.custom-forms.index'
      );
    },

    refreshPage() {
      this.INERTIA_FILTER('dashboard.custom-forms.index', {
        search: this.searchQuery,
        status: this.statusQuery
      });
    }
  }
}
</script>
