<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="rectangle-stack" size="5" class="text-primary" />
    </template>
    <template #header-title>Планы приема</template>
    <template #header-subtitle>Управление планами приема</template>
    <template #header-actions>
      <a
        :href="route('dashboard.admission-plans.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Добавить план
      </a>
    </template>

    <FlashMessages />

    <DataFilters title="Фильтры" @reset="resetFilters">
      <SelectFilter
        v-model="campaignQuery"
        label="Приемная кампания"
        placeholder="Все кампании"
        @change="filterByCampaign"
      >
        <option v-for="campaign in admissionCampaigns" :key="campaign.id" :value="campaign.id">
          {{ campaign.name }} ({{ campaign.academic_year }})
        </option>
      </SelectFilter>

      <SelectFilter
        v-model="programQuery"
        label="Образовательная программа"
        placeholder="Все программы"
        @change="filterByProgram"
      >
        <option v-for="program in educationalPrograms" :key="program.id" :value="program.id">
          {{ program.name }}
        </option>
      </SelectFilter>
    </DataFilters>

    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ plans.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ plans.data.length }} на странице
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
                Программа
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Приемная кампания
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="plan in plans.data"
              :key="plan.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ TEXT_LIMIT(plan.educational_program?.name || '—', 60) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-foreground">
                  {{ plan.admission_campaign?.academic_year || '—' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.admission-plans.edit', plan.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="deletePlan(plan)"
                    class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                </div>
              </td>
            </tr>

            <EmptyState
              v-if="plans.data.length === 0"
              :columns="3"
              title="Планы приема не найдены"
              description="Создайте первый план приема или измените параметры поиска"
              :action-url="route('dashboard.admission-plans.create')"
              action-text="Добавить план"
              icon-path="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375v13.5c0 1.036.84 1.875 1.875 1.875h16.5c1.036 0 1.875-.84 1.875-1.875V6.375c0-1.036-.84-1.875-1.875-1.875H3.375z"
            />
          </tbody>
        </table>
      </div>

      <Pagination :data="plans" />
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import DataFilters from '../Components/shared/DataFilters.vue';
import SelectFilter from '../Components/shared/SelectFilter.vue';
import EmptyState from '../Components/shared/EmptyState.vue';
import Pagination from '../Components/shared/Pagination.vue';

export default {
  name: 'AdmissionPlanIndex',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
    DataFilters,
    SelectFilter,
    EmptyState,
    Pagination
  },

  props: {
    plans: { type: Object, required: true },
    filters: {
      type: Object,
      default: () => ({ admission_campaigns_id: '', educational_programs_id: '' })
    },
    admissionCampaigns: { type: Array, required: true },
    educationalPrograms: { type: Array, required: true }
  },

  data() {
    return {
      campaignQuery: this.filters?.admission_campaigns_id || '',
      programQuery: this.filters?.educational_programs_id || ''
    }
  },

  mounted() { this.SET_DOCUMENT_TITLE('Планы приема'); },

  methods: {
    search() {
      this.INERTIA_FILTER('dashboard.admission-plans.index', {
        admission_campaigns_id: this.campaignQuery,
        educational_programs_id: this.programQuery
      });
    },

    filterByCampaign() { this.search(); },
    filterByProgram() { this.search(); },

    resetFilters() {
      this.RESET_FILTERS(['campaignQuery', 'programQuery'], 'dashboard.admission-plans.index');
    },

    refreshPage() { this.search(); },

    deletePlan(plan) {
      this.CONFIRM_AND_DELETE(plan, 'dashboard.admission-plans.destroy', {
        message: 'Удалить план приема для "' + (plan.educational_program?.name || '') + '"?'
      });
    }
  }
}
</script>
