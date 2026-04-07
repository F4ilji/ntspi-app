<template>
  <div class="border-t border-layer-line px-3 py-4">
    <div class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-primary-50 transition-all duration-200 cursor-pointer group">
      <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
        <DashboardIcon name="user" size="5" class="text-primary" />
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-foreground truncate group-hover:text-primary transition-colors">
          {{ userName }}
        </p>
        <p class="text-xs text-muted-foreground-1">
          {{ userEmail }}
        </p>
      </div>
      <button
        @click="logout"
        class="p-1.5 rounded-lg hover:bg-rose-50 text-muted-foreground-1 hover:text-rose-600 transition-all duration-200 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100"
        title="Выйти"
        aria-label="Выйти из системы"
      >
        <DashboardIcon name="arrow-right-on-rectangle" size="4" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import DashboardIcon from './DashboardIcon.vue';

const page = usePage();

const userName = computed(() => page.props.auth?.user?.name || 'Пользователь');
const userEmail = computed(() => page.props.auth?.user?.email || 'user@ntspi.ru');

const logout = () => {
  if (confirm('Вы уверены, что хотите выйти?')) {
    router.post(route('logout'), {}, {
      onSuccess: () => {
        // Redirect to login page after logout
      },
    });
  }
};
</script>
