<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="document-text" size="5" class="text-primary" />
    </template>
    <template #header-title>Ответы на форму</template>
    <template #header-subtitle>{{ form.title }}</template>
    <template #header-actions>
      <a
        :href="route('dashboard.custom-forms.edit', form.id)"
        class="inline-flex items-center gap-2 px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all"
      >
        <DashboardIcon name="pencil-square" size="4" />
        Редактировать форму
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput
        v-model="searchQuery"
        label="Поиск по ID"
        placeholder="Введите ID ответа..."
        @search="search"
      />

      <SelectFilter
        v-model="checkedQuery"
        label="Статус просмотра"
        placeholder="Все статусы"
        @change="filterByChecked"
      >
        <option value="1">Просмотренные</option>
        <option value="0">Непросмотренные</option>
      </SelectFilter>
    </DataFilters>

    <!-- Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ responses.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ responses.data.length }} на странице
            </span>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-line-2">
          <thead class="bg-surface/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                ID
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Просмотрен
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Дата
              </th>
              <th
                v-for="col in columns"
                :key="col.name"
                class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider"
              >
                {{ col.title }}
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="response in responses.data"
              :key="response.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-muted-foreground-1">{{ response.id }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button
                  @click="toggleChecked(response)"
                  class="h-5 w-5 rounded border transition-colors"
                  :class="response.checked ? 'bg-primary border-primary' : 'border-layer-line bg-white'"
                >
                  <svg v-if="response.checked" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ FORMAT_DATE(response.created_at, 'full') }}</div>
              </td>
              <td
                v-for="col in columns"
                :key="col.name"
                class="px-6 py-4 text-sm text-foreground max-w-xs truncate"
              >
                {{ formatAnswer(response.answers, col) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button
                    @click="viewResponse(response)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Просмотр"
                  >
                    <DashboardIcon name="eye" size="4" />
                  </button>
                  <button
                    @click="confirmDeleteResponse(response)"
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
              v-if="responses.data.length === 0"
              :columns="3 + columns.length"
              title="Ответы не найдены"
              description="Пока нет ответов на эту форму"
              icon-path="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="responses" />
    </div>

    <!-- View Response Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="showModal = false">
      <div class="bg-layer border border-layer-line rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-layer-line flex items-center justify-between">
          <h3 class="text-lg font-medium text-foreground">Ответ #{{ selectedResponse?.id }}</h3>
          <button @click="showModal = false" class="p-1.5 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded transition-all">
            <DashboardIcon name="x-mark" size="5" />
          </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh] space-y-4">
          <div v-for="col in columns" :key="col.name" class="border-b border-layer-line pb-3 last:border-0">
            <dt class="text-sm font-medium text-muted-foreground-1">{{ col.title }}</dt>
            <dd class="mt-1 text-sm text-foreground">{{ formatAnswer(selectedResponse?.answers || {}, col) }}</dd>
          </div>
          <div class="border-b border-layer-line pb-3 last:border-0">
            <dt class="text-sm font-medium text-muted-foreground-1">Дата отправки</dt>
            <dd class="mt-1 text-sm text-foreground">{{ FORMAT_DATE(selectedResponse?.created_at, 'full') }}</dd>
          </div>
        </div>
      </div>
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
  name: 'FormResponsesIndex',
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
    form: { type: Object, required: true },
    responses: { type: Object, required: true },
    columns: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({ search: '', checked: '' }) }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      checkedQuery: this.filters?.checked || '',
      showModal: false,
      selectedResponse: null
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Ответы: ${this.form.title}`);
  },

  methods: {
    formatAnswer(answers, col) {
      const value = answers?.[col.name];
      if (!value) return '—';

      // Для choice полей — маппим значения на лейблы
      if (col.options && Object.keys(col.options).length > 0) {
        if (Array.isArray(value)) {
          return value.map(v => col.options[v] || v).join(', ');
        }
        return col.options[value] || value;
      }

      if (Array.isArray(value)) {
        return value.join(', ');
      }

      return String(value);
    },

    toggleChecked(response) {
      this.$inertia.post(route('dashboard.custom-forms.responses.toggle-checked', {
        customForm: this.form.id,
        response: response.id
      }), {}, {
        preserveScroll: true
      });
    },

    viewResponse(response) {
      this.selectedResponse = response;
      this.showModal = true;
    },

    confirmDeleteResponse(response) {
      this.CONFIRM_AND_DELETE(response, 'dashboard.custom-forms.responses.destroy', {
        customForm: this.form.id,
        message: `Удалить ответ #${response.id}?`
      });
    },

    search() {
      this.$inertia.get(route('dashboard.custom-forms.responses.index', this.form.id), {
        search: this.searchQuery,
        checked: this.checkedQuery
      }, { preserveState: true, preserveScroll: true });
    },

    filterByChecked() {
      this.search();
    },

    resetFilters() {
      this.searchQuery = '';
      this.checkedQuery = '';
      this.$inertia.get(route('dashboard.custom-forms.responses.index', this.form.id), {}, {
        preserveState: true,
        preserveScroll: true
      });
    }
  }
}
</script>
