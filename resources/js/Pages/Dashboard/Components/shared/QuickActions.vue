<template>
  <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-5">
    <h3 class="text-sm font-semibold text-foreground mb-4">Быстрые действия</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <Link
        v-for="action in actions"
        :key="action.route"
        :href="route(action.route)"
        class="group flex items-center gap-3 p-3 rounded-lg border border-transparent transition-all duration-150"
        :class="action.hoverClass"
      >
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors" :class="action.bgClass">
          <DashboardIcon :name="action.icon" size="4" :class="action.iconClass" />
        </div>
        <div>
          <p class="text-sm font-medium text-foreground transition-colors" :class="action.textClass">
            {{ action.label }}
          </p>
          <p class="text-xs text-muted-foreground-1">{{ action.desc }}</p>
        </div>
      </Link>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import DashboardIcon from '../DashboardIcon.vue';

export default {
  name: 'QuickActions',
  components: {
    Link,
    DashboardIcon,
  },
  data() {
    return {
      actions: [
        {
          route: 'dashboard.posts.create',
          label: 'Создать новость',
          desc: 'Новая публикация на сайт',
          icon: 'plus',
          bgClass: 'bg-blue-500/10 group-hover:bg-blue-500/20',
          iconClass: 'text-blue-600',
          textClass: 'group-hover:text-primary',
          hoverClass: 'hover:border-primary/20 hover:bg-primary/5',
        },
        {
          route: 'dashboard.schedules.upload.create',
          label: 'Загрузить расписание',
          desc: 'Массовая загрузка файлов',
          icon: 'arrow-up-tray',
          bgClass: 'bg-emerald-500/10 group-hover:bg-emerald-500/20',
          iconClass: 'text-emerald-600',
          textClass: 'group-hover:text-emerald-600',
          hoverClass: 'hover:border-emerald-500/20 hover:bg-emerald-500/5',
        },
        {
          route: 'dashboard.educational-groups.create',
          label: 'Добавить группу',
          desc: 'Учебная группа',
          icon: 'user-group',
          bgClass: 'bg-violet-500/10 group-hover:bg-violet-500/20',
          iconClass: 'text-violet-600',
          textClass: 'group-hover:text-violet-600',
          hoverClass: 'hover:border-violet-500/20 hover:bg-violet-500/5',
        },
        {
          route: 'dashboard.quick-upload.create',
          label: 'Быстрая загрузка',
          desc: 'Загрузка файлов',
          icon: 'paper-clip',
          bgClass: 'bg-amber-500/10 group-hover:bg-amber-500/20',
          iconClass: 'text-amber-600',
          textClass: 'group-hover:text-amber-600',
          hoverClass: 'hover:border-amber-500/20 hover:bg-amber-500/5',
        },
      ],
    };
  },
}
</script>
