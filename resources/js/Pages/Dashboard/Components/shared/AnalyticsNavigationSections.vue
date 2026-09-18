<template>
  <div class="space-y-0.5">
    <div v-for="ms in filteredSections" :key="ms.id">
      <!-- Home item -->
      <a
        v-if="ms.type === 'home'"
        :href="sectionUrl('/')"
        class="relative flex items-center justify-between py-2 px-2 rounded-md overflow-hidden hover:bg-muted-hover transition-colors group-link"
      >
        <div
          class="absolute inset-y-0 left-0 bg-primary/5 rounded-md"
          :style="{ width: getWidth(ms.views) + '%' }"
        ></div>
        <div class="flex items-center gap-2 relative">
          <DashboardIcon name="home" size="5" class="text-primary" />
          <span class="text-sm font-medium text-foreground">{{ ms.title }}</span>
        </div>
        <div class="flex items-center gap-1.5 relative">
          <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-muted-hover text-[11px] text-muted-foreground-1">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            {{ ms.views }}
          </span>
          <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-muted-hover text-[11px] text-muted-foreground-1">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            {{ ms.visitors }}
          </span>
        </div>
      </a>

      <!-- Regular section -->
      <template v-else>
        <a
          :href="sectionUrl('/' + ms.slug)"
          class="relative flex items-center justify-between py-2 px-2 rounded-md overflow-hidden hover:bg-muted-hover transition-colors group-link"
        >
          <div
            class="absolute inset-y-0 left-0 bg-primary/5 rounded-md"
            :style="{ width: getWidth(ms.views) + '%' }"
          ></div>
          <div class="flex items-center gap-2 relative min-w-0">
            <button
              v-if="ms.sub_sections && ms.sub_sections.length"
              @click.stop.prevent="toggle(ms.id)"
              class="p-0.5 rounded hover:bg-muted-hover transition-colors"
            >
              <svg
                class="w-3 h-3 text-muted-foreground-2 transition-transform"
                :class="{ 'rotate-90': expanded[ms.id] }"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </button>
            <span v-else class="w-3"></span>
            <span class="text-sm font-medium text-foreground truncate">{{ ms.title }}</span>
          </div>
          <div class="flex items-center gap-1.5 relative flex-shrink-0">
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-muted-hover text-[11px] text-muted-foreground-1">
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
              {{ ms.views }}
            </span>
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-muted-hover text-[11px] text-muted-foreground-1">
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
              {{ ms.visitors }}
            </span>
          </div>
        </a>

        <!-- Sub Sections (collapsible) -->
        <div v-if="expanded[ms.id] && ms.sub_sections" class="ml-6 mt-1 space-y-0.5">
          <div v-for="ss in ms.sub_sections" :key="ss.id">
            <a
              :href="sectionUrl('/' + ms.slug + '/' + ss.slug)"
              class="relative flex items-center justify-between py-1.5 px-2 rounded-md overflow-hidden hover:bg-muted-hover transition-colors group-link"
            >
              <div
                class="absolute inset-y-0 left-0 bg-primary/5 rounded-md"
                :style="{ width: getWidth(ss.views) + '%' }"
              ></div>
              <div class="flex items-center gap-2 relative min-w-0">
                <button
                  v-if="ss.pages && ss.pages.length"
                  @click.stop.prevent="toggleMs(ms.id, ss.id)"
                  class="p-0.5 rounded hover:bg-muted-hover transition-colors"
                >
                  <svg
                    class="w-2.5 h-2.5 text-muted-foreground-2 transition-transform"
                    :class="{ 'rotate-90': expandedMs[ms.id + '_' + ss.id] }"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
                <span v-else class="w-2.5"></span>
                <span class="text-xs font-medium text-foreground truncate">{{ ss.title }}</span>
              </div>
              <div class="flex items-center gap-1.5 relative flex-shrink-0">
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-muted-hover text-[11px] text-muted-foreground-1">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                  {{ ss.views }}
                </span>
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-muted-hover text-[11px] text-muted-foreground-1">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                  {{ ss.visitors }}
                </span>
              </div>
            </a>

            <!-- Pages (collapsible) -->
            <div v-if="expandedMs[ms.id + '_' + ss.id] && ss.pages" class="ml-5 mt-0.5 space-y-0.5">
              <a
                v-for="page in ss.pages"
                :key="page.id"
                :href="`/dashboard/analytics/section?prefix=${encodeURIComponent(page.url)}&days=${period}`"
                class="relative flex items-center justify-between py-1 px-2 rounded-md overflow-hidden hover:bg-muted-hover transition-colors group-link"
              >
                <div
                  class="absolute inset-y-0 left-0 bg-primary/5 rounded-md"
                  :style="{ width: getWidth(page.views) + '%' }"
                ></div>
                <div class="flex items-center gap-1.5 relative min-w-0">
                  <DashboardIcon name="document" size="4" class="text-muted-foreground-2 flex-shrink-0" />
                  <span class="text-[11px] text-foreground truncate">{{ page.title }}</span>
                </div>
                <div class="flex items-center gap-1.5 relative flex-shrink-0">
                  <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-muted-hover text-[11px] text-muted-foreground-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    {{ page.views }}
                  </span>
                </div>
              </a>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../DashboardIcon.vue';

export default {
  name: 'AnalyticsNavigationSections',
  components: { DashboardIcon },
  props: {
    navigation: { type: Object, required: true },
    period: { type: Number, default: 30 },
    group: { type: String, default: null },
  },
  data() {
    return {
      expanded: {},
      expandedMs: {},
    };
  },
  computed: {
    filteredSections() {
      if (!this.group) return this.navigation.sections;
      return this.navigation.sections.filter(s => s.group === this.group);
    },
    maxViews() {
      const views = this.filteredSections.map(s => s.views);
      return Math.max(...views, 1);
    },
  },
  methods: {
    getWidth(views) {
      return Math.max((views / this.maxViews) * 100, 2);
    },
    toggle(id) {
      this.expanded[id] = !this.expanded[id];
    },
    toggleMs(msId, ssId) {
      const key = msId + '_' + ssId;
      this.expandedMs[key] = !this.expandedMs[key];
    },
    sectionUrl(prefix) {
      return route('dashboard.analytics.section', { prefix, days: this.period });
    },
  },
};
</script>
