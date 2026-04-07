<template>
  <div>
    <!-- Desktop Sidebar (always visible on lg+) -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-layer border-r border-layer-line z-30">
      <div class="flex flex-col h-full">
        <SidebarLogo />
        <SidebarNav :mobile="false" @child-click="noop" />
        <SidebarUser />
      </div>
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <transition
      enter-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        @click="closeSidebar"
        aria-hidden="true"
      ></div>
    </transition>

    <!-- Mobile Sidebar -->
    <transition
      enter-active-class="transition-transform duration-200 ease-out"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-150 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <div
        v-if="isOpen"
        class="fixed inset-y-0 left-0 w-72 bg-layer border-r border-layer-line z-50 flex flex-col lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="Навигация"
        @keydown.escape="closeSidebar"
      >
        <!-- Mobile Header -->
        <div class="flex items-center justify-between px-4 py-4 border-b border-layer-line">
          <SidebarLogo />
          <button
            @click="closeSidebar"
            class="p-2 rounded-lg hover:bg-muted-hover transition-colors focus:outline-none focus:ring-2 focus:ring-primary/50"
            aria-label="Закрыть меню"
          >
            <DashboardIcon name="x-mark" size="5" class="text-foreground" />
          </button>
        </div>

        <nav ref="mobileNavRef" class="flex-1 overflow-y-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50" tabindex="-1">
          <SidebarNav :mobile="true" @child-click="closeSidebar" />
        </nav>
        <SidebarUser />
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onUnmounted } from 'vue';
import SidebarLogo from './SidebarLogo.vue';
import SidebarNav from './SidebarNav.vue';
import SidebarUser from './SidebarUser.vue';
import DashboardIcon from './DashboardIcon.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close']);

const mobileNavRef = ref(null);
const previousActiveElement = ref(null);

const closeSidebar = () => {
  emit('close');
};

const noop = () => {};

// Блокировка скролла и управление фокусом
watch(() => props.isOpen, async (isOpen) => {
  if (isOpen) {
    // Сохраняем предыдущий активный элемент
    previousActiveElement.value = document.activeElement;
    // Блокируем скролл body
    document.body.style.overflow = 'hidden';
    // Фокус на первый интерактивный элемент внутри модалки
    await nextTick();
    const focusableElement = mobileNavRef.value?.querySelector('a, button');
    if (focusableElement) {
      focusableElement.focus();
    }
  } else {
    // Восстанавливаем скролл
    document.body.style.overflow = '';
    // Возвращаем фокус на предыдущий элемент
    if (previousActiveElement.value && document.body.contains(previousActiveElement.value)) {
      previousActiveElement.value.focus();
    }
  }
});

// Cleanup при unmount — предотвращаем утечку scroll lock
onUnmounted(() => {
  document.body.style.overflow = '';
});
</script>
