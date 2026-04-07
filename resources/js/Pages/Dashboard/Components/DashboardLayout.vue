<template>
  <div class="min-h-screen bg-background-2">
    <!-- Sidebar -->
    <DashboardSidebar :is-open="sidebarOpen" @close="closeSidebar" />

    <!-- Mobile Menu Button -->
    <button
      @click="openSidebar"
      class="lg:hidden fixed top-4 left-4 z-30 p-2 rounded-lg bg-layer border border-layer-line shadow-sm hover:bg-primary-50 transition-all"
      aria-label="Открыть меню"
    >
      <DashboardIcon name="bars-3" size="5" class="text-foreground" />
    </button>

    <!-- Main Content (with sidebar offset on desktop) -->
    <div class="lg:pl-64">
      <!-- Header -->
      <header class="bg-layer/95 backdrop-blur-md border-b border-layer-line sticky top-0 z-20 h-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
          <div class="flex items-center h-full justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                <slot name="header-icon">
                  <DashboardIcon name="document-text" size="5" class="text-primary" />
                </slot>
              </div>
              <div>
                <h1 class="text-sm font-medium text-foreground leading-tight">
                  <slot name="header-title">Панель управления</slot>
                </h1>
                <p class="text-xs text-muted-foreground-1 leading-tight">
                  <slot name="header-subtitle"></slot>
                </p>
              </div>
            </div>
            <div class="flex-shrink-0">
              <slot name="header-actions"></slot>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Breadcrumbs -->
        <slot name="breadcrumbs"></slot>
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import DashboardSidebar from './DashboardSidebar.vue';
import DashboardIcon from './DashboardIcon.vue';

const sidebarOpen = ref(false);

const openSidebar = () => {
  sidebarOpen.value = true;
};

const closeSidebar = () => {
  sidebarOpen.value = false;
};
</script>
