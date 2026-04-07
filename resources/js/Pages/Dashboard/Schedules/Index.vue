<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="calendar" size="5" class="text-primary" />
    </template>
    <template #header-title>Расписания</template>
    <template #header-subtitle>Управление расписаниями занятий</template>
    <template #header-actions>
      <a
        :href="route('dashboard.schedules.upload.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all duration-200 mr-2"
      >
        <DashboardIcon name="cloud-arrow-up" size="4" />
        Быстрая загрузка
      </a>
      <a
        :href="route('dashboard.schedules.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Добавить расписание
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput 
        v-model="searchQuery" 
        label="Поиск по группе"
        placeholder="Введите название группы..."
        @search="search"
      />
      
      <SelectFilter
        v-model="educationalGroupQuery"
        label="Учебная группа"
        placeholder="Все группы"
        @change="filterByEducationalGroup"
      >
        <option v-for="group in educationalGroups" :key="group.id" :value="group.id">
          {{ group.title }}
        </option>
      </SelectFilter>
      
      <SelectFilter
        v-model="educationFormQuery"
        label="Форма обучения"
        placeholder="Все формы"
        @change="filterByEducationForm"
      >
        <option v-for="form in educationForms" :key="form.value" :value="form.value">
          {{ EducationForm.fromValue(form.value)?.label || getEducationFormLabel(form.value) }}
        </option>
      </SelectFilter>
    </DataFilters>

      <!-- Schedules Table -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
        <!-- Table Header Stats -->
        <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <span class="text-sm text-foreground">
                Всего: <span class="font-medium">{{ schedules.total }}</span>
              </span>
              <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
                {{ schedules.data.length }} на странице
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
                  Название
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                  Учебная группа
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                  Факультет
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                  Форма обучения
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                  Обновлено
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                  Действия
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-line-2">
              <tr
                v-for="schedule in schedules.data"
                :key="schedule.id"
                class="group hover:bg-muted-hover/50 transition-all duration-200"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex-shrink-0 rounded-lg border border-layer-line bg-surface flex items-center justify-center">
                      <DashboardIcon name="document" size="5" class="text-muted-foreground-2" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                        {{ schedule.file?.[0]?.title || 'Без названия' }}
                      </div>
                      <div class="text-xs text-muted-foreground-2">
                        PDF документ
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm text-foreground">
                    {{ schedule.educational_group?.title || '—' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm text-foreground">
                    {{ schedule.educational_group?.faculty?.title || '—' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getEducationFormBadgeClass(schedule.educational_group?.education_form_id)">
                    {{ EducationForm.fromValue(schedule.educational_group?.education_form_id)?.label || getEducationFormLabel(schedule.educational_group?.education_form_id) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-foreground">{{ formatDate(schedule.updated_at) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                  <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a
                      :href="getPdfUrl(schedule.file?.[0]?.path)"
                      target="_blank"
                      class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                      title="Просмотреть PDF"
                    >
                      <DashboardIcon name="eye" size="4" />
                    </a>
                    <a
                      :href="route('dashboard.schedules.edit', schedule.id)"
                      class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                      title="Редактировать"
                    >
                      <DashboardIcon name="pencil-square" size="4" />
                    </a>
                    <button
                      @click.prevent="confirmDelete(schedule)"
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
                v-if="schedules.data.length === 0"
                :columns="6"
                title="Расписания не найдены"
                description="Добавьте первое расписание или измените параметры поиска"
                :action-url="route('dashboard.schedules.create')"
                action-text="Добавить расписание"
                icon-path="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <Pagination :data="schedules" />
      </div>
  </DashboardLayout>
</template>

<script>
import EducationForm from '@/Enum/EducationForm.js';
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import DataFilters from '../Components/shared/DataFilters.vue';
import SearchInput from '../Components/shared/SearchInput.vue';
import SelectFilter from '../Components/shared/SelectFilter.vue';
import EmptyState from '../Components/shared/EmptyState.vue';
import Pagination from '../Components/shared/Pagination.vue';

export default {
  name: 'ScheduleIndex',
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
    schedules: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        educational_group_id: '',
        education_form_id: '',
        search: ''
      })
    },
    educationalGroups: {
      type: Array,
      required: true
    },
    educationForms: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      educationalGroupQuery: this.filters?.educational_group_id || '',
      educationFormQuery: this.filters?.education_form_id || '',
      deletingSchedule: null,
      EducationForm
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Расписание');
  },

  methods: {
    getEducationFormBadgeClass(formId) {
      const eduForm = EducationForm.fromValue(formId);
      const colorClasses = {
        1: 'bg-info/10 text-info border-info/20',
        2: 'bg-success/10 text-success border-success/20',
        3: 'bg-primary/10 text-primary border-primary/20'
      };
      return `inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border ${colorClasses[formId] || colorClasses[1]}`;
    },

    getEducationFormLabel(formId) {
      const eduForm = EducationForm.fromValue(formId);
      if (eduForm) {
        return eduForm.label;
      }
      const labels = {
        1: 'Очное обучение',
        2: 'Очно-заочное обучение',
        3: 'Заочное обучение'
      };
      return labels[formId] || '—';
    },

    formatDate(date) {
      if (!date) return '—';
      return new Date(date).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    getPdfUrl(path) {
      if (!path) return '#';
      return `/storage/${path}`;
    },

    search() {
      this.$inertia.get(route('dashboard.schedules.index'), {
        search: this.searchQuery,
        educational_group_id: this.educationalGroupQuery,
        education_form_id: this.educationFormQuery
      }, {
        preserveState: true
      });
    },

    filterByEducationalGroup() {
      this.search();
    },

    filterByEducationForm() {
      this.search();
    },

    resetFilters() {
      this.searchQuery = '';
      this.educationalGroupQuery = '';
      this.educationFormQuery = '';
      this.$inertia.get(route('dashboard.schedules.index'), {}, {
        preserveState: true
      });
    },

    confirmDelete(schedule) {
      if (confirm(`Вы уверены, что хотите удалить расписание "${schedule.file?.[0]?.title}"?`)) {
        this.deleteSchedule(schedule);
      }
    },

    deleteSchedule(schedule) {
      this.$inertia.delete(route('dashboard.schedules.destroy', schedule.id), {
        preserveScroll: true
      });
    },

    refreshPage() {
      this.$inertia.get(route('dashboard.schedules.index'), {
        search: this.searchQuery,
        educational_group_id: this.educationalGroupQuery,
        education_form_id: this.educationFormQuery
      }, {
        preserveState: true
      });
    },

    route(name, params) {
      return route(name, params);
    }
  }
}
</script>
