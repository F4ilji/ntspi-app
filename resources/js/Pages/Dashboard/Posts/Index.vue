<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="document-text" size="5" class="text-primary" />
    </template>
    <template #header-title>Новости</template>
    <template #header-subtitle>Управление публикациями</template>
    <template #header-actions>
      <a
        :href="route('dashboard.posts.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
      >
        <DashboardIcon name="plus" size="4" />
        Создать новость
      </a>
    </template>

    <template #breadcrumbs>
      <Breadcrumbs :crumbs="[{ label: 'Новости', href: route('dashboard.posts.index') }]" />
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Created Post Notification -->
    <transition
      enter-active-class="transition duration-300"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-200"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div v-if="$page.props.flash?.created_post" class="mb-6 bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
        <div class="bg-primary-50 px-4 py-3 border-b border-primary-100">
          <div class="flex items-center gap-2">
            <DashboardIcon name="document-text" size="5" class="text-primary" />
            <h3 class="text-sm font-medium text-primary-foreground">Новость создана</h3>
          </div>
        </div>
        <div class="p-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <div>
                <p class="text-xs text-muted-foreground-1 mb-1">Заголовок</p>
                <p class="text-sm font-medium text-foreground">{{ $page.props.flash.created_post.title }}</p>
              </div>
              <div class="flex items-center gap-3">
                <div>
                  <p class="text-xs text-muted-foreground-1 mb-1">Статус</p>
                  <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $page.props.flash.created_post.status }}
                  </span>
                </div>
                <div v-if="$page.props.flash.created_post.category">
                  <p class="text-xs text-muted-foreground-1 mb-1">Категория</p>
                  <p class="text-sm font-medium text-foreground">{{ $page.props.flash.created_post.category.title }}</p>
                </div>
              </div>
            </div>
            <div v-if="$page.props.flash.created_post.preview" class="flex justify-center md:justify-end">
              <img
                :src="`/storage/${$page.props.flash.created_post.preview}`"
                alt="Preview"
                class="h-28 w-auto rounded-lg border border-layer-line object-cover"
              />
            </div>
          </div>

          <div class="mt-4 flex flex-wrap gap-2">
            <a
              :href="`/admin/posts/${$page.props.flash.created_post.id}/edit`"
              target="_blank" rel="external"
              class="inline-flex items-center px-3 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all"
            >
              <DashboardIcon name="pencil-square" size="4" class="mr-1.5" />
              Редактировать
            </a>
            <button
              @click.prevent="openPostModal($page.props.flash.created_post)"
              class="inline-flex items-center px-3 py-2 border border-layer-line text-sm font-medium rounded-lg text-layer-foreground hover:bg-primary-50 transition-all"
            >
              <DashboardIcon name="eye" size="4" class="mr-1.5" />
              Просмотр
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Filters Card -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput 
        v-model="searchQuery" 
        label="Поиск по заголовку"
        placeholder="Введите название..."
        @search="search"
      />
      
      <SelectFilter
        v-model="statusQuery"
        label="Статус"
        placeholder="Все статусы"
        @change="filterByStatus"
      >
        <option v-for="status in [PostStatus.PUBLISHED, PostStatus.VERIFICATION, PostStatus.REJECTED]"
                :key="status.value"
                :value="status.value">
          {{ status.label }}
        </option>
      </SelectFilter>
    </DataFilters>

    <!-- Posts Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Bulk Actions Bar -->
      <transition
        enter-active-class="transition duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="selectedPosts.length > 0" class="px-6 py-3 bg-primary/5 border-b border-primary/20 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-primary">{{ selectedPosts.length }} выбрано</span>
            <button
              @click="selectAllOnPage"
              class="text-xs text-muted-foreground-1 hover:text-primary transition-colors"
            >
              Выбрать все на странице
            </button>
          </div>
          <div class="flex items-center gap-2">
            <button
              @click="bulkPublish"
              :disabled="bulkProcessing"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <DashboardIcon name="check-circle" size="4" />
              Опубликовать
            </button>
            <button
              @click="bulkSetVerification"
              :disabled="bulkProcessing"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 text-white text-xs font-medium rounded-lg hover:bg-amber-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <DashboardIcon name="clock" size="4" />
              На модерацию
            </button>
            <button
              @click="bulkDelete"
              :disabled="bulkProcessing"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 text-white text-xs font-medium rounded-lg hover:bg-rose-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <DashboardIcon name="trash" size="4" />
              Удалить
            </button>
            <button
              @click="clearSelection"
              class="p-1.5 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
              title="Снять выделение"
            >
              <DashboardIcon name="x-mark" size="4" />
            </button>
          </div>
        </div>
      </transition>

      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ posts.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ posts.data.length }} на странице
            </span>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-line-2">
          <thead class="bg-surface/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider w-10">
                <input
                  type="checkbox"
                  :checked="isAllSelected"
                  :indeterminate.prop="isIndeterminate"
                  @change="toggleSelectAll"
                  class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20 cursor-pointer"
                />
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Материал
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Категория
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Статус
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Дата публикации
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Автор
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr
              v-for="post in posts.data"
              :key="post.id"
              class="group hover:bg-muted-hover/50 transition-all duration-200"
              :class="{ 'bg-primary/5': selectedPosts.includes(post.id) }"
            >
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  :value="post.id"
                  v-model="selectedPosts"
                  class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20 cursor-pointer"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div v-if="post.preview" class="w-14 h-14 flex-shrink-0 overflow-hidden rounded-lg border border-layer-line">
                    <img
                      :src="`/storage/${post.preview}`"
                      :alt="post.title"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                    />
                  </div>
                  <div v-else class="w-14 h-14 flex-shrink-0 rounded-lg border border-layer-line bg-surface flex items-center justify-center">
                    <DashboardIcon name="photo" size="6" class="text-muted-foreground-2" />
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="text-sm font-medium text-foreground truncate group-hover:text-primary transition-colors" :title="post.title">
                      {{ TEXT_LIMIT(post.title, 60) }}
                    </div>
                    <div class="text-xs text-muted-foreground-2 font-mono mt-0.5">
                      {{ post.slug }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  v-if="post.category"
                  class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-primary/10 text-primary border border-primary/20"
                >
                  {{ post.category.title }}
                </span>
                <span v-else class="text-xs text-muted-foreground-2">—</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusBadgeClass(post.status)">
                  <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="getStatusDotClass(post.status)"></span>
                  {{ getStatusLabel(post.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-foreground">{{ formatDate(post.publish_at) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="text-xs font-medium text-primary">
                      {{ getAuthorInitials(post.author?.name) }}
                    </span>
                  </div>
                  <span class="text-sm text-foreground">
                    {{ post.author?.name || '—' }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a
                    :href="route('dashboard.posts.edit', post.id)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Редактировать"
                  >
                    <DashboardIcon name="pencil-square" size="4" />
                  </a>
                  <button
                    @click.prevent="confirmDelete(post)"
                    class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                    title="Удалить"
                  >
                    <DashboardIcon name="trash" size="4" />
                  </button>
                  <button
                    @click.prevent="openPostModal(post)"
                    class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                    title="Просмотр"
                  >
                    <DashboardIcon name="eye" size="4" />
                  </button>
                </div>
              </td>
            </tr>

            <!-- Empty State -->
            <EmptyState 
              v-if="posts.data.length === 0"
              :columns="6"
              title="Новости не найдены"
              description="Создайте первую новость или измените параметры поиска"
              :action-url="route('dashboard.posts.create')"
              action-text="Создать новость"
            />
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :data="posts" />
    </div>

    <!-- Post Modal -->
    <transition
      enter-active-class="transition duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="selectedPost"
        class="fixed inset-0 bg-slate-900/70 backdrop-blur-md z-50 flex items-center justify-center p-4"
        @click.self="closePostModal"
      >
        <div class="bg-white border border-layer-line rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
          <!-- Header -->
          <div class="px-4 py-3 border-b border-card-line flex items-center justify-between">
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <div class="w-9 h-9 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                <DashboardIcon name="document-text" size="5" class="text-primary" />
              </div>
              <h3 class="text-sm font-medium text-foreground truncate">{{ selectedPost.title }}</h3>
            </div>
            <button
              @click="closePostModal"
              class="ml-3 text-muted-foreground-2 hover:text-foreground hover:bg-layer rounded-lg p-1.5 transition-all"
            >
              <DashboardIcon name="x-mark" size="5" />
            </button>
          </div>

          <!-- Content -->
          <div class="p-4 overflow-y-auto flex-1">
            <!-- Meta -->
            <div class="flex flex-wrap gap-2 mb-4">
              <span class="inline-flex items-center px-2.5 py-1.5 bg-layer text-muted-foreground-1 rounded-md text-xs font-medium border border-layer-line">
                ID: {{ selectedPost.id }}
              </span>
              <span v-if="selectedPost.category" class="inline-flex items-center px-2.5 py-1.5 bg-primary-500/10 text-primary-700 rounded-md text-xs font-medium border border-primary-200">
                <DashboardIcon name="tag" size="4" class="mr-1.5" />
                {{ selectedPost.category.title }}
              </span>
              <span
                v-if="selectedPost.status"
                class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium uppercase border"
                :class="{
                  'bg-amber-50 text-amber-700 border-amber-200': selectedPost.status === 'verification',
                  'bg-rose-50 text-rose-700 border-rose-200': selectedPost.status === 'rejected',
                  'bg-emerald-50 text-emerald-700 border-emerald-200': selectedPost.status === 'published'
                }"
              >
                <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="{
                  'bg-amber-500': selectedPost.status === 'verification',
                  'bg-rose-500': selectedPost.status === 'rejected',
                  'bg-emerald-500': selectedPost.status === 'published'
                }"></span>
                {{ selectedPost.status === 'verification' ? 'На рассмотрении' : selectedPost.status === 'rejected' ? 'Отклонено' : selectedPost.status }}
              </span>
            </div>

            <!-- Preview -->
            <div v-if="selectedPost.preview" class="mb-4">
              <p class="text-xs font-medium text-muted-foreground-1 mb-2">Главное изображение</p>
              <div class="rounded-lg overflow-hidden border border-layer-line">
                <img
                  :src="`/storage/${selectedPost.preview}`"
                  alt="Preview"
                  class="w-full h-auto max-h-80 object-cover"
                />
              </div>
            </div>

            <!-- Gallery -->
            <div v-if="selectedPost.images && selectedPost.images.length > 0" class="mb-4">
              <p class="text-xs font-medium text-muted-foreground-1 mb-2">Галерея ({{ selectedPost.images.length }})</p>
              <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                <div
                  v-for="(image, index) in selectedPost.images"
                  :key="index"
                  @click="viewImage(image)"
                  class="group relative rounded-lg overflow-hidden border border-layer-line aspect-square cursor-pointer hover:border-primary transition-all"
                >
                  <img
                    :src="`/storage/${image}`"
                    alt="Gallery"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                  />
                </div>
              </div>
            </div>

            <!-- Authors -->
            <div v-if="selectedPost.authors" class="mb-4 p-3 bg-surface rounded-lg border border-layer-line">
              <p class="text-xs font-medium text-muted-foreground-1 mb-1">Автор(ы)</p>
              <p class="text-sm text-foreground">
                {{ Array.isArray(selectedPost.authors) ? selectedPost.authors.join(', ') : selectedPost.authors }}
              </p>
            </div>

            <!-- Preview Text -->
            <div v-if="selectedPost.preview_text" class="mb-4">
              <p class="text-xs font-medium text-muted-foreground-1 mb-2">Превью</p>
              <p class="text-sm text-foreground leading-relaxed">{{ selectedPost.preview_text }}</p>
            </div>

            <!-- Content -->
            <div class="mb-4">
              <p class="text-xs font-medium text-muted-foreground-1 mb-2">Полный текст</p>
              <div class="prose prose-sm max-w-none bg-surface p-4 rounded-lg border border-layer-line">
                <div v-html="renderContent(selectedPost.content)" class="text-foreground"></div>
              </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-line-2 pt-3">
              <div class="flex flex-wrap justify-between gap-3 text-xs text-muted-foreground-2">
                <div class="flex flex-wrap gap-3">
                  <span class="flex items-center gap-1">
                    <DashboardIcon name="clock" size="4" />
                    {{ new Date(selectedPost.created_at).toLocaleString('ru-RU') }}
                  </span>
                  <span class="flex items-center gap-1">
                    <DashboardIcon name="arrow-path" size="4" />
                    {{ new Date(selectedPost.updated_at).toLocaleString('ru-RU') }}
                  </span>
                </div>
                <span v-if="selectedPost.reading_time" class="flex items-center gap-1">
                  <DashboardIcon name="book-open" size="4" />
                  {{ selectedPost.reading_time }} мин
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="px-4 py-3 border-t border-line-2 bg-surface flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-2">
            <div class="flex items-center gap-3 flex-wrap">
              <!-- Edit -->
              <a
                :href="`/admin/posts/${selectedPost.id}/edit`"
                class="inline-flex items-center px-3 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all"
              >
                <DashboardIcon name="pencil-square" size="4" class="mr-1.5" />
                Редактировать
              </a>
            </div>
            <button
              @click="closePostModal"
              class="w-full sm:w-auto px-3 py-2 border border-layer-line text-sm font-medium rounded-lg text-layer-foreground hover:bg-layer transition-all"
            >
              Закрыть
            </button>
          </div>
        </div>
      </div>
    </transition>
  </DashboardLayout>
</template>

<script>
import PostStatus from '@/Enum/PostStatus.js';
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import Breadcrumbs from '../Components/shared/Breadcrumbs.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import DataFilters from '../Components/shared/DataFilters.vue';
import SearchInput from '../Components/shared/SearchInput.vue';
import SelectFilter from '../Components/shared/SelectFilter.vue';
import EmptyState from '../Components/shared/EmptyState.vue';
import Pagination from '../Components/shared/Pagination.vue';

export default {
  name: 'PostIndex',
  components: {
    DashboardLayout,
    DashboardIcon,
    Breadcrumbs,
    FlashMessages,
    DataFilters,
    SearchInput,
    SelectFilter,
    EmptyState,
    Pagination
  },

  props: {
    posts: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        status: '',
        search: ''
      })
    }
  },

  data() {
    return {
      searchQuery: this.filters?.search || '',
      statusQuery: this.filters?.status || '',
      deletingPost: null,
      selectedPost: null,
      selectedPosts: [],
      bulkProcessing: false,
      PostStatus,
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Все новости');
  },

  computed: {
    isAllSelected() {
      return this.posts.data.length > 0 && this.selectedPosts.length === this.posts.data.length;
    },
    isIndeterminate() {
      return this.selectedPosts.length > 0 && this.selectedPosts.length < this.posts.data.length;
    },
  },

  methods: {
    getStatusBadgeClass(status) {
      const statusValue = status?.value ?? status;
      const statusObj = PostStatus.fromValue(statusValue);
      const colorClasses = {
        gray: 'bg-stone-500/10 text-stone-700 border-stone-500/20',
        success: 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20',
        warning: 'bg-amber-500/10 text-amber-700 border-amber-500/20',
        danger: 'bg-rose-500/10 text-rose-700 border-rose-500/20'
      };
      const color = statusObj ? statusObj.color : 'gray';
      return `inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border ${colorClasses[color]}`;
    },

    getStatusDotClass(status) {
      const statusValue = status?.value ?? status;
      const statusObj = PostStatus.fromValue(statusValue);
      const colorClasses = {
        gray: 'bg-stone-500',
        success: 'bg-emerald-500',
        warning: 'bg-amber-500',
        danger: 'bg-rose-500'
      };
      const color = statusObj ? statusObj.color : 'gray';
      return colorClasses[color];
    },

    getStatusLabel(status) {
      const statusValue = status?.value ?? status;
      return PostStatus.getLabel(statusValue) || status;
    },

    formatDate(date) {
      return this.FORMAT_DATE(date, 'full');
    },

    getAuthorInitials(name) {
      return this.GET_INITIALS(name) || '?';
    },

    search() {
      this.INERTIA_FILTER('dashboard.posts.index', {
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
        'dashboard.posts.index'
      );
    },

    confirmDelete(post) {
      this.CONFIRM_AND_DELETE(post, 'dashboard.posts.destroy', {
        message: `Вы уверены, что хотите удалить новость "${post.title}"?`
      });
    },

    openPostModal(post) {
      this.selectedPost = post;
    },

    closePostModal() {
      this.selectedPost = null;
    },

    renderContent(content) {
      if (!content) return '';
      if (Array.isArray(content)) {
        return content.map(block => {
          if (typeof block === 'string') return block;
          if (block.data?.content) return block.data.content;
          return '';
        }).join('');
      }
      return content;
    },

    viewImage(imagePath) {
      window.open(this.GET_BASE_STORAGE_URL() + imagePath);
    },

    toggleSelectAll(event) {
      if (event.target.checked) {
        this.selectedPosts = [...new Set([...this.selectedPosts, ...this.posts.data.map(p => p.id)])];
      } else {
        const pageIds = new Set(this.posts.data.map(p => p.id));
        this.selectedPosts = this.selectedPosts.filter(id => !pageIds.has(id));
      }
    },

    selectAllOnPage() {
      this.selectedPosts = [...new Set([...this.selectedPosts, ...this.posts.data.map(p => p.id)])];
    },

    clearSelection() {
      this.selectedPosts = [];
    },

    bulkDelete() {
      if (!confirm(`Удалить ${this.selectedPosts.length} новостей?`)) return;
      this.bulkProcessing = true;
      this.$inertia.delete(route('dashboard.posts.bulk-destroy'), {
        data: { ids: this.selectedPosts },
        preserveScroll: true,
        onSuccess: () => {
          this.selectedPosts = [];
          this.bulkProcessing = false;
        },
        onError: () => {
          this.bulkProcessing = false;
        },
      });
    },

    bulkPublish() {
      if (!confirm(`Опубликовать ${this.selectedPosts.length} новостей?`)) return;
      this.bulkProcessing = true;
      this.$inertia.post(route('dashboard.posts.bulk-publish'), {
        ids: this.selectedPosts,
      }, {
        preserveScroll: true,
        onSuccess: () => {
          this.selectedPosts = [];
          this.bulkProcessing = false;
        },
        onError: () => {
          this.bulkProcessing = false;
        },
      });
    },

    bulkSetVerification() {
      if (!confirm(`Перевести ${this.selectedPosts.length} новостей на модерацию?`)) return;
      this.bulkProcessing = true;
      this.$inertia.post(route('dashboard.posts.bulk-verification'), {
        ids: this.selectedPosts,
      }, {
        preserveScroll: true,
        onSuccess: () => {
          this.selectedPosts = [];
          this.bulkProcessing = false;
        },
        onError: () => {
          this.bulkProcessing = false;
        },
      });
    },
  }
}
</script>
