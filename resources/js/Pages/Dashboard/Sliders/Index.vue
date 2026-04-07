<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="photo" size="5" class="text-primary" />
    </template>
    <template #header-title>Слайдеры</template>
    <template #header-subtitle>Управление слайдерами</template>
    <template #header-actions>
      <a
        :href="route('dashboard.sliders.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать слайдер
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Filters -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <div class="col-span-2">
        <SearchInput 
          v-model="search" 
          label="Поиск"
          placeholder="Поиск по названию или slug..."
          id="slider_search"
          @search="applyFilters"
        />
      </div>
      
      <div>
        <label for="is_active_filter" class="block text-xs font-medium text-muted-foreground-1 mb-1.5">
          Статус
        </label>
        <select
          id="is_active_filter"
          v-model="isActive"
          class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
          @change="applyFilters"
        >
          <option value="">Все статусы</option>
          <option value="1">Активные</option>
          <option value="0">Неактивные</option>
        </select>
      </div>
    </DataFilters>

    <!-- Sliders Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <table class="min-w-full divide-y divide-line-2">
        <thead class="bg-surface/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Название
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Кол-во слайдов
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Статус
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Действия
            </th>
          </tr>
        </thead>
        <tbody class="bg-layer divide-y divide-line-2">
          <tr v-for="slider in sliders.data" :key="slider.id" class="hover:bg-muted-hover transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div>
                  <div class="text-sm font-medium text-foreground">{{ slider.title }}</div>
                  <div class="text-xs text-muted-foreground-1">{{ slider.slug }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  slider.slides_count > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]"
              >
                {{ slider.slides_count }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  slider.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                ]"
              >
                {{ slider.is_active ? 'Активен' : 'Неактивен' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <a
                  :href="route('dashboard.sliders.edit', slider.id)"
                  class="text-blue-600 hover:text-blue-900 transition-colors"
                  title="Редактировать"
                >
                  <DashboardIcon name="pencil-square" size="5" />
                </a>
                <button
                  @click="confirmDelete(slider)"
                  class="text-red-600 hover:text-red-900 transition-colors"
                  title="Удалить"
                >
                  <DashboardIcon name="trash" size="5" />
                </button>
              </div>
            </td>
          </tr>
          <EmptyState 
            v-if="sliders.data.length === 0"
            :columns="4"
            title="Слайдеры не найдены"
            description="Создайте первый слайдер или измените параметры поиска"
            :action-url="route('dashboard.sliders.create')"
            action-text="Создать слайдер"
          />
        </tbody>
      </table>

      <!-- Pagination -->
      <Pagination :data="sliders" />
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-layer border border-layer-line rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-foreground mb-4">Подтверждение удаления</h3>
        <p class="text-sm text-muted-foreground-1 mb-6">
          Вы уверены, что хотите удалить слайдер "{{ sliderToDelete?.title }}"? Все слайды будут удалены.
        </p>
        <div class="flex gap-3 justify-end">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-colors"
          >
            Отмена
          </button>
          <button
            @click="deleteSlider"
            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors"
          >
            Удалить
          </button>
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
import EmptyState from '../Components/shared/EmptyState.vue';
import Pagination from '../Components/shared/Pagination.vue';

export default {
  name: 'SlidersIndex',
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
    sliders: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      search: this.filters.search || '',
      isActive: this.filters.is_active || '',
      showDeleteModal: false,
      sliderToDelete: null,
      searchTimeout: null
    }
  },
  mounted() {
    this.SET_DOCUMENT_TITLE('Слайдеры');
  },
  methods: {
    resetFilters() {
      this.search = ''
      this.isActive = ''
      this.$inertia.get(route('dashboard.sliders.index'), {}, {
        preserveState: true,
        replace: true
      })
    },
    debounceSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(this.applyFilters, 300)
    },
    applyFilters() {
      this.$inertia.get(route('dashboard.sliders.index'), {
        search: this.search,
        is_active: this.isActive
      }, {
        preserveState: true,
        replace: true
      })
    },
    changePage(url) {
      if (url) {
        this.$inertia.get(url, {
          search: this.search,
          is_active: this.isActive
        }, {
          preserveState: true
        })
      }
    },
    confirmDelete(slider) {
      this.sliderToDelete = slider
      this.showDeleteModal = true
    },
    deleteSlider() {
      this.$inertia.delete(route('dashboard.sliders.destroy', this.sliderToDelete.id), {
        onSuccess: () => {
          this.showDeleteModal = false
          this.sliderToDelete = null
        }
      })
    }
  }
}
</script>
