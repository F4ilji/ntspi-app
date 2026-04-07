<template>
  <nav class="flex-1 overflow-y-auto px-3 py-4">
    <div class="space-y-1">
      <SidebarNavItem
        v-for="item in menuItems"
        :key="item.key"
        :item="item"
        :mobile="mobile"
        :is-expanded="expandedKey === item.key"
        @child-click="$emit('child-click')"
      />
    </div>

    <!-- Divider -->
    <div class="my-4 border-t border-layer-line"></div>

    <!-- Quick Actions Section -->
    <div>
      <h3 class="px-3 mb-2 text-xs font-semibold text-muted-foreground-1 uppercase tracking-wide">
        Быстрые действия
      </h3>
      <div class="space-y-1">
        <a
          v-for="action in quickActions"
          :key="action.label"
          :href="action.external ? action.href : route(action.route)"
          :target="action.external ? '_blank' : null"
          :rel="action.external ? 'noopener noreferrer' : null"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-foreground hover:bg-primary-50 hover:text-primary transition-all duration-200 group"
        >
          <component :is="iconMap[action.icon]" class="w-5 h-5 flex-shrink-0" />
          <span>{{ action.label }}</span>
          <DashboardIcon
            v-if="action.external"
            name="arrow-up-right"
            size="4"
            class="ml-auto text-muted-foreground-2 group-hover:text-primary"
          />
        </a>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue';
import { menuItems } from './menuConfig';
import { quickActions } from './quickActionsConfig';
import SidebarNavItem from './SidebarNavItem.vue';
import DashboardIcon from './DashboardIcon.vue';

defineProps({
  mobile: { type: Boolean, default: false },
});

defineEmits(['child-click']);

const iconMap = {
  cog: DashboardIcon,
};

// Открываем только аккордеон активной страницы
const expandedKey = computed(() => {
  const currentRoute = route().current();

  for (const item of menuItems) {
    if (item.activePrefixes && item.activePrefixes.some(prefix => currentRoute.startsWith(prefix))) {
      return item.key;
    }
  }

  return null;
});
</script>
