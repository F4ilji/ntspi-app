<template>
  <transition
    enter-active-class="transition-opacity duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="visible"
      class="fixed bottom-0 inset-x-0 z-50 p-4 sm:p-6"
    >
      <div class="max-w-3xl mx-auto bg-layer border border-layer-line rounded-lg shadow-lg p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="flex-1">
            <h3 class="text-sm font-semibold text-foreground mb-1">Cookie и аналитика</h3>
            <p class="text-xs text-muted-foreground-1 leading-relaxed">
              Мы собираем анонимную статистику посещений для улучшения качества сайта.
              Данные не передаются третьим лицам.
              <a :href="route('page.view', { path: 'privacy-policy' })" class="underline hover:text-primary">
                Подробнее
              </a>
            </p>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            <button
              @click="decline"
              class="px-3 py-1.5 text-xs font-medium text-muted-foreground-1 hover:text-foreground border border-layer-line rounded-md transition-colors"
            >
              Отклонить
            </button>
            <button
              @click="accept"
              class="px-3 py-1.5 text-xs font-medium text-white bg-primary hover:bg-primary/90 rounded-md transition-colors"
            >
              Принять
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { getConsent } from '@/services/analytics.js';

export default {
  name: 'ConsentBanner',
  data() {
    return {
      visible: false,
    };
  },
  mounted() {
    const consent = getConsent();
    if (!consent) {
      this.visible = true;
    }
  },
  methods: {
    accept() {
      localStorage.setItem('analytics_consent', 'granted');
      this.visible = false;
    },
    decline() {
      localStorage.setItem('analytics_consent', 'denied');
      this.visible = false;
    },
  },
};
</script>
