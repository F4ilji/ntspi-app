<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="clipboard-document-check" size="5" class="text-primary" />
    </template>
    <template #header-title>Приемные кампании</template>
    <template #header-subtitle>Управление приемными кампаниями</template>
    <template #header-actions>
      <a
        :href="route('dashboard.admission-campaigns.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать кампанию
      </a>
    </template>

    <FlashMessages />

    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск"
        placeholder="Введите название кампании..."
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

      <SelectFilter
        v-model="academicYearQuery"
        label="Учебный год"
        placeholder="Все годы"
        @change="filterByAcademicYear"
      >
        <option v-for="year in academicYears" :key="year" :value="year">
          {{ year }}
        </option>
      </SelectFilter>
    </DataFilters>

    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ campaigns.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ campaigns.data.length }} на странице
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
                Статус
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Уровней образования
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="campaign in campaigns.data"
              :key="campaign.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ TEXT_LIMIT(campaign.name, 50) }}
                </div>
                <div class="text-xs text-muted-foreground-1 mt-0.5">
                  {{ campaign.academic_year }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusBadgeClass(campaign.status)">
                  {{ getStatusLabel(campaign.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-surface-muted text-foreground">
                  {{ getInfoCount(campaign) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.admission-campaigns.edit', campaign.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="deleteCampaign(campaign)"
                    class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>
              </td>
            </tr>

            <EmptyState
              v-if="campaigns.data.length === 0"
              :columns="4"
              title="Приемные кампании не найдены"
              description="Создайте первую приемную кампанию или измените параметры поиска"
              :action-url="route('dashboard.admission-campaigns.create')"
              action-text="Создать кампанию"
              icon-path="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"
            />
          </tbody>
        </table>
      </div>

      <Pagination :data="campaigns" />
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
  name: 'AdmissionCampaignIndex',
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
    campaigns: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        status: '',
        academic_year: '',
        search: ''
      })
    },
    statuses: {
      type: Array,
      required: true
    },
    academicYears: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      statusQuery: this.filters?.status || '',
      academicYearQuery: this.filters?.academic_year || ''
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Приемные кампании');
  },

  methods: {
    getStatusBadgeClass(status) {
      const statusMap = {
        1: 'bg-success/10 text-success border-success/20',
        2: 'bg-gray-500/10 text-gray-600 border-gray-500/20',
        3: 'bg-danger/10 text-danger border-danger/20'
      };
      const baseClasses = 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border';
      return `${baseClasses} ${statusMap[status] || statusMap[1]}`;
    },

    getStatusLabel(status) {
      const statusObj = this.statuses.find(s => s.value === status);
      return statusObj ? statusObj.label : '—';
    },

    getInfoCount(campaign) {
      return Array.isArray(campaign.info) ? campaign.info.length : 0;
    },

    search() {
      this.INERTIA_FILTER('dashboard.admission-campaigns.index', {
        search: this.searchQuery,
        status: this.statusQuery,
        academic_year: this.academicYearQuery
      });
    },

    filterByStatus() {
      this.search();
    },

    filterByAcademicYear() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'statusQuery', 'academicYearQuery'],
        'dashboard.admission-campaigns.index'
      );
    },

    refreshPage() {
      this.search();
    },

    deleteCampaign(campaign) {
      this.CONFIRM_AND_DELETE(campaign, 'dashboard.admission-campaigns.destroy', {
        message: 'Удалить кампанию "' + campaign.name + '"?'
      });
    }
  }
}
</script>
