<template>
  <tr>
    <td :colspan="columns" class="px-6 py-16 text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-surface border border-layer-line flex items-center justify-center">
        <DashboardIcon
          v-if="iconName"
          :name="iconName"
          size="8"
          class="text-muted-foreground-2"
        />
        <svg v-else class="w-8 h-8 text-muted-foreground-2" fill="none" stroke="currentColor" :viewBox="iconViewBox">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath" />
        </svg>
      </div>
      <p class="text-foreground font-medium">{{ title }}</p>
      <p class="text-sm text-muted-foreground-1 mt-1 mb-4">{{ description }}</p>
      <a
        v-if="actionUrl"
        :href="actionUrl"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all"
      >
        <DashboardIcon name="plus" size="4" color="currentColor" />
        {{ actionText }}
      </a>
      <button
        v-else-if="onAction"
        @click="onAction"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all"
      >
        <DashboardIcon name="plus" size="4" color="currentColor" />
        {{ actionText }}
      </button>
    </td>
  </tr>
</template>

<script>
import DashboardIcon from '../DashboardIcon.vue';

export default {
  name: 'EmptyState',
  components: {
    DashboardIcon,
  },
  props: {
    columns: {
      type: Number,
      default: 6
    },
    title: {
      type: String,
      default: 'Ничего не найдено'
    },
    description: {
      type: String,
      default: 'Попробуйте изменить параметры поиска или создайте новую запись'
    },
    actionUrl: {
      type: String,
      default: null
    },
    actionText: {
      type: String,
      default: 'Создать'
    },
    onAction: {
      type: Function,
      default: null
    },
    iconName: {
      type: String,
      default: null
    },
    iconPath: {
      type: String,
      default: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'
    },
    iconViewBox: {
      type: String,
      default: '0 0 24 24'
    }
  }
}
</script>
