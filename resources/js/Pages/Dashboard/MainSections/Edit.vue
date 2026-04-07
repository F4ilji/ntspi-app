<template>
  <div class="min-h-screen bg-background-2">
    <!-- Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a
            :href="route('dashboard.main-sections.index')"
            class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
          >
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="pencil-square" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Редактирование раздела</h1>
              <p class="text-xs text-muted-foreground-1">{{ mainSection.title }}</p>
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
                    Название раздела <span class="text-rose-500">*</span>
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
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
                <a
                  :href="route('dashboard.main-sections.index')"
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

        <!-- SubSections Table -->
        <div class="lg:col-span-2">
          <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <DashboardIcon name="folder-open" size="5" class="text-primary" />
                  <h3 class="text-base font-medium text-foreground">Подразделы</h3>
                  <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
                    {{ subSections.length }}
                  </span>
                </div>
                <a
                  :href="route('dashboard.sub-sections.create')"
                  class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-lg hover:bg-primary-hover transition-all"
                >
                  <DashboardIcon name="plus" size="4" />
                  Добавить
                </a>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-line-2">
                <thead class="bg-surface/50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                      Название подраздела
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                      Slug
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                      Страницы
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
                      Действия
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-line-2">
                  <tr
                    v-for="subSection in subSections"
                    :key="subSection.id"
                    class="group hover:bg-muted-hover/50 transition-all duration-200"
                  >
                    <td class="px-6 py-4">
                      <div class="text-sm font-medium text-foreground">
                        {{ subSection.title }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <code class="text-xs bg-muted px-2 py-1 rounded text-foreground">
                        {{ subSection.slug }}
                      </code>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="text-sm text-foreground">
                        {{ subSection.pages?.length || 0 }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                      <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a
                          :href="route('dashboard.sub-sections.edit', subSection.id)"
                          class="p-2 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                          title="Редактировать"
                        >
                          <DashboardIcon name="pencil-square" size="4" />
                        </a>
                        <button
                          @click.prevent="detachSubSection(subSection)"
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
                    v-if="subSections.length === 0"
                    :columns="4"
                    title="Подразделы не найдены"
                    description="Добавьте подразделы к этому главному разделу"
                    :action-url="route('dashboard.sub-sections.create')"
                    action-text="Создать подраздел"
                    icon-path="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                  />
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import EmptyState from '../Components/shared/EmptyState.vue';

export default {
  name: 'MainSectionEdit',
  components: {
    DashboardIcon,
    FlashMessages,
    EmptyState
  },

  props: {
    mainSection: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      form: {
        title: this.mainSection.title,
        slug: this.mainSection.slug
      },
      errors: {},
      processing: false
    }
  },

  computed: {
    subSections() {
      return this.mainSection.sub_sections || [];
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование: ${this.mainSection.title}`);
  },

  methods: {
    generateSlug() {
      if (!this.form.slug || this.form.slug === this.mainSection.slug) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },

    submit() {
      this.processing = true;
      this.errors = {};

      this.$inertia.put(route('dashboard.main-sections.update', this.mainSection.id), this.form, {
        onFinish: () => {
          this.processing = false;
        },
        onError: (errors) => {
          this.errors = errors;
        }
      });
    },

    detachSubSection(subSection) {
      if (confirm(`Открепить подраздел "${subSection.title}"?`)) {
        this.$inertia.post(route('dashboard.sub-sections.detach-from-main-section', subSection.id), {}, {
          preserveScroll: true
        });
      }
    }
  }
}
</script>
