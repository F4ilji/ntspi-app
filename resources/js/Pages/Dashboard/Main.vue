<template>
  <DashboardLayout>
    <template #header-title>Панель управления</template>
    <template #header-subtitle>Добро пожаловать в систему управления контентом NTSPI</template>

    <!-- Flash Messages (shared component) -->
    <FlashMessages />

    <!-- Deploy Button -->
    <div v-if="isProduction" class="mb-6 bg-gradient-to-r from-primary/10 to-info/10 border border-primary/20 rounded-lg p-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-primary/20 flex items-center justify-center">
            <DashboardIcon name="arrow-path" size="5" class="text-primary" />
          </div>
          <div>
            <h3 class="text-sm font-semibold text-foreground">Обновление сайта</h3>
            <p class="text-xs text-muted-foreground-1">Запуск скрипта деплоя и пересборка сервера</p>
          </div>
        </div>
        <button
          type="button"
          @click="deploySite"
          :disabled="deploying"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md"
        >
          <svg v-if="deploying" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <DashboardIcon v-else name="rocket-launch" size="4" />
          {{ deploying ? 'Обновление...' : 'Обновить сайт' }}
        </button>
      </div>

      <!-- Deploy Output -->
      <div v-if="deployOutput" class="mt-4 p-3 bg-surface border border-layer-line rounded-lg">
        <div class="flex items-start gap-2 mb-2">
          <DashboardIcon :name="deploySuccess ? 'check-circle' : 'exclamation-circle'" size="4" :class="deploySuccess ? 'text-success' : 'text-rose-500'" />
          <span class="text-sm font-medium" :class="deploySuccess ? 'text-success' : 'text-rose-500'">{{ deployMessage }}</span>
        </div>
        <pre v-if="deployOutput" class="mt-2 p-2 bg-muted/30 rounded text-xs text-foreground overflow-auto max-h-48">{{ deployOutput }}</pre>
      </div>
    </div>

    <!-- Stats Overview -->
    <StatsOverview :stats="stats" class="mb-6" />

    <!-- Quick Actions -->
    <QuickActions class="mb-6" />

    <!-- Recent Activity (full width) -->
    <RecentActivity :recent-activity="recentActivity" class="mb-6" />

    <!-- AI Prepared Posts -->
    <AiPreparedWidget :posts="aiPreparedPosts" class="mb-6" />

    <!-- Domain Navigation Cards -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-5">
      <h3 class="text-sm font-semibold text-foreground mb-4">Разделы системы</h3>

      <div v-for="section in domainSections" :key="section.title" class="mb-6 last:mb-0">
        <h4 class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide mb-3">{{ section.title }}</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <a
            v-for="item in section.items"
            :key="item.route"
            :href="route(item.route)"
            class="group flex items-center gap-3 p-3 rounded-lg border border-layer-line hover:border-opacity-30 hover:bg-opacity-5 transition-all duration-150"
            :class="getHoverClasses(item.color)"
          >
            <div
              class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-opacity-20 transition-colors"
              :class="getBgClass(item.color)"
            >
              <DashboardIcon :name="item.icon" size="5" :class="getTextClass(item.color)" />
            </div>
            <div>
              <p class="text-sm font-medium text-foreground group-hover:text-opacity-80 transition-colors" :class="getTextClass(item.color)">
                {{ item.label }}
              </p>
              <p class="text-xs text-muted-foreground-1">{{ item.desc }}</p>
            </div>
          </a>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from './Components/DashboardLayout.vue';
import DashboardIcon from './Components/DashboardIcon.vue';
import FlashMessages from './Components/shared/FlashMessages.vue';
import StatsOverview from './Components/shared/StatsOverview.vue';
import QuickActions from './Components/shared/QuickActions.vue';
import RecentActivity from './Components/shared/RecentActivity.vue';
import AiPreparedWidget from './Components/shared/AiPreparedWidget.vue';

export default {
  name: 'Main',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
    StatsOverview,
    QuickActions,
    RecentActivity,
    AiPreparedWidget,
  },
  props: {
    stats: {
      type: Object,
      required: true,
    },
    aiPreparedPosts: {
      type: Array,
      required: true,
    },
    recentActivity: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      deploying: false,
      deployOutput: null,
      deploySuccess: false,
      deployStatus: null,
      deployPolling: null,
      domainSections: [
        {
          title: 'Контент сайта',
          items: [
            { route: 'dashboard.posts.index', label: 'Новости', desc: 'Управление публикациями', icon: 'document', color: 'primary' },
            { route: 'dashboard.categories.index', label: 'Категории', desc: 'Категории новостей', icon: 'tag', color: 'info' },
            { route: 'dashboard.sliders.index', label: 'Слайдеры', desc: 'Управление слайдерами', icon: 'photo', color: 'warning' },
            { route: 'dashboard.pages.index', label: 'Страницы', desc: 'Структура сайта', icon: 'document-duplicate', color: 'primary' },
          ],
        },
        {
          title: 'Образование',
          items: [
            { route: 'dashboard.educational-groups.index', label: 'Учебные группы', desc: 'Управление группами', icon: 'academic-cap', color: 'success' },
            { route: 'dashboard.additional-educations.index', label: 'Доп. образование', desc: 'Программы ДПО', icon: 'book-open', color: 'info' },
            { route: 'dashboard.admission-campaigns.index', label: 'Приёмная кампания', desc: 'Планы и наборы', icon: 'user-plus', color: 'warning' },
          ],
        },
        {
          title: 'Инфраструктура',
          items: [
            { route: 'dashboard.schedules.index', label: 'Расписание', desc: 'Управление расписанием', icon: 'calendar', color: 'success' },
            { route: 'dashboard.faculties.index', label: 'Факультеты', desc: 'Структура института', icon: 'building-office', color: 'primary' },
            { route: 'dashboard.departments.index', label: 'Кафедры', desc: 'Преподаватели и программы', icon: 'users', color: 'info' },
          ],
        },
        {
          title: 'Пользователи',
          items: [
            { route: 'dashboard.users.index', label: 'Пользователи', desc: 'Управление и права', icon: 'user-group', color: 'warning' },
          ],
        },
      ],
    };
  },
  mounted() {
    this.SET_DOCUMENT_TITLE('Главная');
  },
  beforeUnmount() {
    if (this.deployPolling) {
      clearInterval(this.deployPolling);
    }
  },
  computed: {
    isProduction() {
      return this.$page.props.app?.env === 'production';
    },
  },
  methods: {
    async deploySite() {
      if (!confirm('Вы уверены, что хотите обновить сайт? Это запустит скрипт деплоя.')) {
        return;
      }

      this.deploying = true;
      this.deployOutput = null;
      this.deploySuccess = false;

      try {
        const response = await fetch(route('dashboard.deploy'), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
        });

        const result = await response.json();

        this.deploySuccess = result.success;
        this.deployOutput = result.message;

        // Если деплой запущен — начинаем polling статуса
        if (result.success) {
          this.startDeployPolling();
        }
      } catch (error) {
        this.deploySuccess = false;
        this.deployOutput = 'Ошибка при выполнении запроса: ' + error.message;
      } finally {
        this.deploying = false;
      }
    },

    startDeployPolling() {
      // Проверяем статус каждые 3 секунды
      this.deployPolling = setInterval(async () => {
        try {
          const response = await fetch(route('dashboard.deploy.status'));
          const status = await response.json();

          this.deployStatus = status;

          if (status.status === 'completed' || status.status === 'failed' || status.status === 'idle') {
            clearInterval(this.deployPolling);
            this.deployPolling = null;
            this.deployOutput = status.message || 'Деплой завершён';
            this.deploySuccess = status.status === 'completed';

            if (status.log) {
              this.deployOutput += '\n\n' + status.log;
            }
          }
        } catch (error) {
          console.error('Error polling deploy status:', error);
        }
      }, 3000);
    },
    get deployMessage() {
      if (!this.deployOutput) return '';
      return this.deploySuccess ? 'Сайт успешно обновлён!' : 'Ошибка при обновлении сайта';
    },
    getBgClass(color) {
      const map = {
        primary: 'bg-primary/10',
        success: 'bg-success/10',
        warning: 'bg-warning/10',
        info: 'bg-info/10',
      };
      return map[color] || 'bg-primary/10';
    },
    getTextClass(color) {
      const map = {
        primary: 'text-primary',
        success: 'text-success',
        warning: 'text-warning',
        info: 'text-info',
      };
      return map[color] || 'text-primary';
    },
    getHoverClasses(color) {
      const map = {
        primary: 'hover:border-primary/30 hover:bg-primary/5',
        success: 'hover:border-success/30 hover:bg-success/5',
        warning: 'hover:border-warning/30 hover:bg-warning/5',
        info: 'hover:border-info/30 hover:bg-info/5',
      };
      return map[color] || 'hover:border-primary/30 hover:bg-primary/5';
    },
  },
}
</script>
