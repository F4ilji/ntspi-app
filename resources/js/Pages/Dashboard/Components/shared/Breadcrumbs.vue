<template>
  <nav aria-label="Навигация" class="mb-4">
    <ol class="flex items-center gap-1.5 text-sm text-muted-foreground-1 flex-wrap">
      <li>
        <Link
          :href="route('dashboard.index')"
          class="inline-flex items-center gap-1 text-muted-foreground-1 hover:text-primary transition-colors"
        >
          <DashboardIcon name="home" size="4" />
          <span>Главная</span>
        </Link>
      </li>
      <li v-for="(crumb, index) in crumbs" :key="index" class="flex items-center gap-1.5">
        <DashboardIcon name="chevron-right" size="3" class="text-muted-foreground-2" />
        <template v-if="crumb.href && index < crumbs.length - 1">
          <Link
            :href="crumb.href"
            class="text-muted-foreground-1 hover:text-primary transition-colors"
          >
            {{ crumb.label }}
          </Link>
        </template>
        <template v-else>
          <span class="text-foreground font-medium">{{ crumb.label }}</span>
        </template>
      </li>
    </ol>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import DashboardIcon from '../DashboardIcon.vue';

export default {
  name: 'Breadcrumbs',
  components: {
    Link,
    DashboardIcon,
  },
  props: {
    crumbs: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
