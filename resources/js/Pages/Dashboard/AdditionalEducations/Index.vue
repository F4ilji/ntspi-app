<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="book-open" size="5" class="text-primary" />
    </template>
    <template #header-title>Дополнительное образование</template>
    <template #header-subtitle>Управление программами ДПО</template>
    <template #header-actions>
      <a
        :href="route('dashboard.additional-educations.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Добавить программу
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск"
        placeholder="Введите название или целевую аудиторию..."
        @search="search"
      />

      <SelectFilter
        v-model="categoryQuery"
        label="Категория"
        placeholder="Все категории"
        @change="filterByCategory"
      >
        <option v-for="category in categories" :key="category.id" :value="category.id">
          {{ category.title }}
        </option>
      </SelectFilter>

      <SelectFilter
        v-model="formEducationQuery"
        label="Форма обучения"
        placeholder="Все формы"
        @change="filterByFormEducation"
      >
        <option v-for="form in educationForms" :key="form.value" :value="form.value">
          {{ EducationForm.fromValue(form.value)?.label || getFormEducationLabel(form.value) }}
        </option>
      </SelectFilter>

      <SelectFilter
        v-model="isActiveQuery"
        label="Статус"
        placeholder="Все"
        @change="filterByActive"
      >
        <option value="">Все</option>
        <option value="1">Активные</option>
        <option value="0">Неактивные</option>
      </SelectFilter>
    </DataFilters>

    <!-- Programs Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ educations.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ educations.data.length }} на странице
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
                Категория
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Стоимость
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Часов
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Форма обучения
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Статус
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="education in educations.data"
              :key="education.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                  {{ TEXT_LIMIT(education.title, 50) }}
                </div>
                <div class="text-xs text-muted-foreground-1 mt-0.5">
                  {{ TEXT_LIMIT(education.target_group, 40) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border bg-primary/10 text-primary border-primary/20">
                  {{ education.category?.title || '—' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-foreground">{{ formatPrice(education.price) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm text-foreground">{{ education.learning_time }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getFormEducationBadgeClass(education.form_education)">
                  {{ EducationForm.fromValue(education.form_education)?.label || getFormEducationLabel(education.form_education) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="STATUS_BADGE_CLASS(education.is_active)">
                  {{ education.is_active ? 'Активна' : 'Неактивна' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.additional-educations.edit', education.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="deleteEducation(education)"
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
              v-if="educations.data.length === 0"
              :columns="7"
              title="Программы не найдены"
              description="Создайте первую программу дополнительного образования или измените параметры поиска"
              :action-url="route('dashboard.additional-educations.create')"
              action-text="Добавить программу"
              icon-path="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="educations" />
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
  name: 'AdditionalEducationIndex',
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
    educations: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        category_id: '',
        form_education: '',
        is_active: '',
        search: ''
      })
    },
    categories: {
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
      categoryQuery: this.filters?.category_id || '',
      formEducationQuery: this.filters?.form_education || '',
      isActiveQuery: this.filters?.is_active || '',
      EducationForm
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Дополнительное образование');
  },

  methods: {
    getFormEducationBadgeClass(formId) {
      const eduForm = EducationForm.fromValue(formId);
      const colorClasses = {
        1: 'bg-success/10 text-success border-success/20',
        2: 'bg-info/10 text-info border-info/20',
        3: 'bg-warning/10 text-warning border-warning/20'
      };
      const baseClasses = 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border';
      return `${baseClasses} ${colorClasses[formId] || colorClasses[1]}`;
    },

    getFormEducationLabel(formId) {
      const eduForm = EducationForm.fromValue(formId);
      return eduForm ? eduForm.label : '—';
    },

    formatPrice(price) {
      if (!price) return '—';
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      }).format(price);
    },

    search() {
      this.INERTIA_FILTER('dashboard.additional-educations.index', {
        search: this.searchQuery,
        category_id: this.categoryQuery,
        form_education: this.formEducationQuery,
        is_active: this.isActiveQuery
      });
    },

    filterByCategory() {
      this.search();
    },

    filterByFormEducation() {
      this.search();
    },

    filterByActive() {
      this.search();
    },

    resetFilters() {
      this.RESET_FILTERS(
        ['searchQuery', 'categoryQuery', 'formEducationQuery', 'isActiveQuery'],
        'dashboard.additional-educations.index'
      );
    },

    refreshPage() {
      this.search();
    },

    deleteEducation(education) {
      this.CONFIRM_AND_DELETE(education, 'dashboard.additional-educations.destroy', {
        message: 'Удалить программу "' + education.title + '"?'
      });
    }
  }
}
</script>
