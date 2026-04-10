<template>
  <div class="min-h-screen bg-background-2">
    <!-- Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.sub-sections.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="pencil-square" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Редактирование подраздела</h1>
              <p class="text-xs text-muted-foreground-1">{{ subSection.title }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Flash Messages -->
      <FlashMessages />

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Card -->
        <div class="lg:col-span-1">
          <div class="bg-layer border border-layer-line rounded-lg shadow-xs sticky top-24">
            <div class="p-6 border-b border-line-2">
              <div class="flex items-center gap-2">
                <DashboardIcon name="document-text" size="5" class="text-primary" />
                <h2 class="text-base font-medium text-foreground">Основная информация</h2>
              </div>
            </div>

            <form @submit.prevent="submit" class="p-6">
              <div class="space-y-6">
                <!-- Title -->
                <div>
                  <label for="title" class="block text-sm font-medium text-foreground mb-2">
                    Название подраздела <span class="text-rose-500">*</span>
                  </label>
                  <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    @input="generateSlug"
                    :class="[
                      'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                      errors.title ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                    ]"
                  />
                  <p v-if="errors.title" class="mt-1.5 text-xs text-rose-500">{{ errors.title }}</p>
                </div>

                <!-- Slug -->
                <div>
                  <label for="slug" class="block text-sm font-medium text-foreground mb-2">
                    Текстовый идентификатор <span class="text-rose-500">*</span>
                  </label>
                  <input
                    id="slug"
                    v-model="form.slug"
                    type="text"
                    :class="[
                      'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                      errors.slug ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                    ]"
                  />
                  <p v-if="errors.slug" class="mt-1.5 text-xs text-rose-500">{{ errors.slug }}</p>
                </div>

                <!-- Main Section -->
                <div>
                  <label for="main_section_id" class="block text-sm font-medium text-foreground mb-2">
                    Главный раздел
                  </label>
                  <select
                    id="main_section_id"
                    v-model="form.main_section_id"
                    :class="[
                      'w-full px-4 py-2.5 bg-surface border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all',
                      errors.main_section_id ? 'border-rose-500' : 'border-layer-line focus:border-primary'
                    ]"
                  >
                    <option value="">Без главного раздела</option>
                    <option v-for="(title, id) in mainSections" :key="id" :value="id">
                      {{ title }}
                    </option>
                  </select>
                  <p v-if="errors.main_section_id" class="mt-1.5 text-xs text-rose-500">{{ errors.main_section_id }}</p>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
                <a
                  :href="route('dashboard.sub-sections.index')"
                  class="inline-flex items-center gap-2 px-4 py-2.5 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all"
                >
                  Отмена
                </a>
                <button
                  type="submit"
                  :disabled="processing"
                  class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                  </svg>
                  <DashboardIcon v-else name="check" size="4" />
                  {{ processing ? 'Сохранение...' : 'Сохранить' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Pages Table -->
        <div class="lg:col-span-2">
          <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <DashboardIcon name="document-text" size="5" class="text-primary" />
                  <h3 class="text-base font-medium text-foreground">Страницы</h3>
                  <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
                    {{ pages.length }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Attach Page Form -->
            <div class="px-6 py-4 border-b border-line-2 bg-surface/30">
              <form @submit.prevent="attachPage" class="flex items-center gap-3">
                <select
                  v-model="selectedPageId"
                  class="flex-1 px-4 py-2.5 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                >
                  <option value="">Выберите страницу для прикрепления</option>
                  <option v-for="(title, id) in availablePages" :key="id" :value="id">
                    {{ title }}
                  </option>
                </select>
                <button
                  type="submit"
                  :disabled="!selectedPageId || attachingPage"
                  class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <DashboardIcon name="plus" size="4" />
                  Прикрепить
                </button>
              </form>
            </div>

            <div class="overflow-x-auto" :class="{ 'select-none': dragIndex !== null }">
              <table class="min-w-full divide-y divide-line-2">
                <thead class="bg-surface/50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider w-10">
                      <!-- Drag handle column -->
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                      Заголовок страницы
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                      Slug
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                      Действия
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-line-2">
                  <tr
                    v-for="(page, index) in pages"
                    :key="page.id"
                    :data-index="index"
                    :draggable="true"
                    @dragstart="onDragStart($event, index)"
                    @dragover.prevent="onDragOver($event, index)"
                    @dragenter.prevent="onDragEnter($event, index)"
                    @drop.prevent="onDrop($event, index)"
                    @dragend="onDragEnd"
                    :class="[
                      'group hover:bg-muted-hover/50 transition-all duration-200',
                      dragIndex === index ? 'opacity-40 bg-primary/10 border-y-2 border-primary/30' : '',
                      dropIndex === index && dragIndex !== index ? 'border-t-2 border-primary bg-primary/5' : ''
                    ]"
                  >
                    <td class="px-6 py-4">
                      <div class="cursor-move text-muted-foreground-1 hover:text-foreground transition-colors">
                        <DashboardIcon name="bars-3" size="4" />
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm font-medium text-foreground">
                        {{ page.title }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <code class="text-xs bg-muted px-2 py-1 rounded text-foreground">
                        {{ page.slug }}
                      </code>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                      <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <Link
                          :href="route('dashboard.pages.edit', page.id)"
                          class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                          title="Редактировать"
                        >
                          <DashboardIcon name="pencil-square" size="4" />
                        </Link>
                        <button
                          @click.prevent="detachPage(page)"
                          class="p-2 text-muted-foreground-1 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all"
                          title="Открепить"
                        >
                          <DashboardIcon name="x-mark" size="4" />
                        </button>
                      </div>
                    </td>
                  </tr>

                  <!-- Empty State -->
                  <EmptyState
                    v-if="pages.length === 0"
                    :columns="4"
                    title="Страницы не найдены"
                    description="Прикрепите страницы к этому подразделу"
                    icon-path="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </tbody>
              </table>
            </div>

            <!-- Save Order Button (shown when order changed) -->
            <div v-if="orderChanged" class="px-6 py-4 border-t border-line-2 bg-surface/30">
              <div class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground-1">
                  <DashboardIcon name="information-circle" size="4" class="inline mr-1" />
                  Порядок страниц изменен. Сохраните изменения.
                </p>
                <button
                  @click="saveOrder"
                  :disabled="savingOrder"
                  class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="savingOrder" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                  </svg>
                  <DashboardIcon v-else name="check" size="4" />
                  {{ savingOrder ? 'Сохранение...' : 'Сохранить порядок' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import EmptyState from '../Components/shared/EmptyState.vue';

export default {
  name: 'SubSectionEdit',
  components: {
    Link,
    DashboardIcon,
    FlashMessages,
    EmptyState
  },

  props: {
    subSection: {
      type: Object,
      required: true
    },
    mainSections: {
      type: Object,
      required: true
    },
    availablePages: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      form: {
        title: this.subSection.title,
        slug: this.subSection.slug,
        main_section_id: this.subSection.main_section_id || '',
        page_ids: this.subSection.pages?.map(p => p.id) || []
      },
      selectedPageId: '',
      errors: {},
      processing: false,
      attachingPage: false,
      // Drag and drop state
      dragIndex: null,
      dropIndex: null,
      localPages: [],
      orderChanged: false,
      savingOrder: false
    }
  },

  computed: {
    pages() {
      return this.orderChanged ? this.localPages : (this.subSection.pages || []);
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование: ${this.subSection.title}`);
  },

  methods: {
    generateSlug() {
      if (!this.form.slug || this.form.slug === this.subSection.slug) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },

    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.put(route('dashboard.sub-sections.update', this.subSection.id), this.form, {
        onFinish: () => {
          this.processing = false;
        },
        onError: (errors) => {
          this.errors = errors;
        }
      });
    },

    attachPage() {
      if (!this.selectedPageId) return;

      this.attachingPage = true;

      this.$inertia.post(
        route('dashboard.sub-sections.pages.attach', this.subSection.id),
        { page_id: this.selectedPageId },
        {
          preserveScroll: true,
          onSuccess: () => {
            this.attachingPage = false;
            this.selectedPageId = '';
            // Reset local changes if page is attached
            if (this.orderChanged) {
              this.orderChanged = false;
              this.localPages = [];
            }
          },
          onError: () => {
            this.attachingPage = false;
          }
        }
      );
    },

    detachPage(page) {
      if (confirm(`Открепить страницу "${page.title}"?`)) {
        // If we have local changes, remove from local state first
        if (this.orderChanged) {
          const index = this.localPages.findIndex(p => p.id === page.id);
          if (index !== -1) {
            this.localPages.splice(index, 1);
          }
        }

        this.$inertia.delete(route('dashboard.sub-sections.pages.detach', {
          subSection: this.subSection.id,
          page: page.id
        }), {
          preserveScroll: true,
          onSuccess: () => {
            // Reset local changes if page is detached
            if (this.orderChanged && this.localPages.length === 0) {
              this.orderChanged = false;
            }
          }
        });
      }
    },

    // Drag and Drop methods
    onDragStart(event, index) {
      this.dragIndex = index;
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', index.toString());
    },

    onDragOver(event, index) {
      event.dataTransfer.dropEffect = 'move';
    },

    onDragEnter(event, index) {
      this.dropIndex = index;
    },

    onDrop(event, targetIndex) {
      const sourceIndex = this.dragIndex;

      if (sourceIndex === null || sourceIndex === targetIndex) {
        this.dragIndex = null;
        this.dropIndex = null;
        return;
      }

      // Initialize localPages if not already done
      if (!this.orderChanged) {
        this.localPages = [...this.pages];
      }

      // Move the item in the array
      const item = this.localPages.splice(sourceIndex, 1)[0];
      this.localPages.splice(targetIndex, 0, item);

      this.orderChanged = true;
      this.dragIndex = null;
      this.dropIndex = null;
    },

    onDragEnd() {
      this.dragIndex = null;
      this.dropIndex = null;
    },

    saveOrder() {
      if (!this.orderChanged) return;

      this.savingOrder = true;

      const pageIds = this.localPages.map(page => page.id);

      this.$inertia.post(
        route('dashboard.sub-sections.pages.reorder', this.subSection.id),
        { page_ids: pageIds },
        {
          preserveScroll: true,
          onSuccess: () => {
            this.savingOrder = false;
            this.orderChanged = false;
            this.localPages = [];
          },
          onError: () => {
            this.savingOrder = false;
          }
        }
      );
    }
  }
}
</script>
