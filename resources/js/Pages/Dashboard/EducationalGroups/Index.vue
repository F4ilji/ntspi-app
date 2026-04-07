<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="academic-cap" size="5" class="text-primary" />
    </template>
    <template #header-title>Учебные группы</template>
    <template #header-subtitle>Управление учебными группами</template>
    <template #header-actions>
      <a
        :href="route('dashboard.educational-groups.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать группу
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput 
        v-model="searchQuery" 
        label="Поиск по названию"
        placeholder="Введите название группы..."
        @search="search"
      />
      
      <SelectFilter
        v-model="facultyQuery"
        label="Факультет"
        placeholder="Все факультеты"
        @change="filterByFaculty"
      >
        <option v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
          {{ faculty.title }}
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

      <!-- Groups Table -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
        <!-- Table Header Stats -->
        <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <span class="text-sm text-foreground">
                Всего: <span class="font-medium">{{ groups.total }}</span>
              </span>
              <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
                {{ groups.data.length }} на странице
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
                  Название группы
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                  Факультет
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                  Форма обучения
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
                v-for="group in groups.data"
                :key="group.id"
                class="group hover:bg-muted-hover/50 transition-all duration-200"
              >
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                    {{ group.title }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm text-foreground">
                    {{ group.faculty?.title || '—' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getEducationFormBadgeClass(group.education_form_id)">
                    {{ EducationForm.fromValue(group.education_form_id)?.label || getEducationFormLabel(group.education_form_id) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-foreground">{{ formatDate(group.created_at) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                  <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a
                      :href="route('dashboard.educational-groups.edit', group.id)"
                      class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                      title="Редактировать"
                    >
                      <DashboardIcon name="pencil-square" size="4" />
                    </a>
                    <button
                      @click.prevent="confirmDelete(group)"
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
                v-if="groups.data.length === 0"
                :columns="5"
                title="Учебные группы не найдены"
                description="Создайте первую учебную группу или измените параметры поиска"
                :action-url="route('dashboard.educational-groups.create')"
                action-text="Создать группу"
                icon-path="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <Pagination :data="groups" />
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
  name: 'EducationalGroupIndex',
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
    groups: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        faculty_id: '',
        education_form_id: '',
        search: ''
      })
    },
    faculties: {
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
      facultyQuery: this.filters?.faculty_id || '',
      educationFormQuery: this.filters?.education_form_id || '',
      deletingGroup: null,
      EducationForm
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Учебные группы');
  },

  methods: {
    getEducationFormBadgeClass(formId) {
      const eduForm = EducationForm.fromValue(formId);
      const colorClasses = {
        1: 'bg-info/10 text-info border-info/20',
        2: 'bg-success/10 text-success border-success/20',
        3: 'bg-primary/10 text-primary border-primary/20'
      };
      const color = eduForm?.color || 'info';
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
        year: 'numeric'
      });
    },

    search() {
      this.$inertia.get(route('dashboard.educational-groups.index'), {
        search: this.searchQuery,
        faculty_id: this.facultyQuery,
        education_form_id: this.educationFormQuery
      }, {
        preserveState: true
      });
    },

    filterByFaculty() {
      this.search();
    },

    filterByEducationForm() {
      this.search();
    },

    resetFilters() {
      this.searchQuery = '';
      this.facultyQuery = '';
      this.educationFormQuery = '';
      this.$inertia.get(route('dashboard.educational-groups.index'), {}, {
        preserveState: true
      });
    },

    confirmDelete(group) {
      if (confirm(`Вы уверены, что хотите удалить группу "${group.title}"?`)) {
        this.deleteGroup(group);
      }
    },

    deleteGroup(group) {
      this.$inertia.delete(route('dashboard.educational-groups.destroy', group.id), {
        preserveScroll: true
      });
    },

    refreshPage() {
      this.$inertia.get(route('dashboard.educational-groups.index'), {
        search: this.searchQuery,
        faculty_id: this.facultyQuery,
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
