<template>
  <DashboardLayout>
    <template #header-title>Панель управления</template>
    <template #header-subtitle>Добро пожаловать в систему управления контентом NTSPI</template>

    <!-- Flash Messages (shared component) -->
    <FlashMessages />

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
  methods: {
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
