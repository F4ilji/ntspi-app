<template>
  <!-- Simple link (no children) -->
  <Link
    v-if="!item.children"
    :href="route(item.route)"
    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group"
    :class="linkClass"
    :aria-current="isActiveItem ? 'page' : null"
  >
    <DashboardIcon :name="iconComponent" size="5" class="flex-shrink-0" />
    <span>{{ item.label }}</span>
  </Link>

  <!-- Collapsible section -->
  <div v-else class="space-y-1">
    <button
      @click="toggle"
      class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group"
      :class="linkClass"
      :aria-expanded="isExpanded"
      :aria-controls="`sidebar-section-${item.key}`"
    >
      <DashboardIcon :name="iconComponent" size="5" class="flex-shrink-0" />
      <span class="flex-1 text-left">{{ item.label }}</span>
      <DashboardIcon
        name="chevron-down"
        size="4"
        class="transition-transform duration-200"
        :class="{ 'rotate-180': isExpanded }"
        aria-hidden="true"
      />
    </button>

    <div v-show="isExpanded" :id="`sidebar-section-${item.key}`" class="ml-8 space-y-1">
      <Link
        v-for="child in item.children"
        :key="child.route"
        :href="route(child.route)"
        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-200"
        :class="getChildClass(child.route)"
        @click="onChildClick"
      >
        <span class="w-1.5 h-1.5 rounded-full bg-current" aria-hidden="true"></span>
        {{ child.label }}
      </Link>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import DashboardIcon from './DashboardIcon.vue';

const props = defineProps({
  item: { type: Object, required: true },
  mobile: { type: Boolean, default: false },
  isExpanded: { type: Boolean, default: false },
});

const emit = defineEmits(['child-click', 'toggle']);

const iconMap = {
  home: 'home',
  document: 'document-text',
  book: 'book-open',
  calendar: 'calendar',
  image: 'photo',
  building: 'building-office',
  cog: 'cog-6-tooth',
  upload: 'document-text',
  folder: 'folder',
  beaker: 'beaker',
  'user-circle': 'user-circle',
  'rectangle-stack': 'rectangle-stack',
  'arrow-path': 'arrow-path',
  'academic-cap': 'academic-cap',
  'clipboard-document-check': 'clipboard-document-check',
};

const iconComponent = computed(() => iconMap[props.item.icon] || 'home');

const isActiveItem = computed(() => {
  if (!props.item.route) return false;
  return route().current(props.item.route);
});

const isSectionActive = computed(() => {
  if (!props.item.activePrefixes) return false;
  return props.item.activePrefixes.some((prefix) => route().current(prefix + '*'));
});

// Локальное состояние для ручного переключения
const localExpanded = ref(props.isExpanded);

// При изменении пропса обновляем локальное состояние
watch(() => props.isExpanded, (val) => {
  localExpanded.value = val;
});

// При смене роута сбрасываем на значение пропса (только активный аккордеон)
watch(() => route().current(), () => {
  localExpanded.value = props.isExpanded;
});

const toggle = () => {
  localExpanded.value = !localExpanded.value;
};

// Используем локальное состояние в template
const isExpanded = computed(() => localExpanded.value);

const linkClass = computed(() => {
  const active = isActiveItem.value || isSectionActive.value;
  const hasChildren = !!props.item.children;
  return active
    ? (hasChildren ? 'bg-primary/5 text-primary' : 'bg-primary/10 text-primary')
    : 'text-foreground hover:bg-primary-50 hover:text-primary';
});

const getChildClass = (childRoute) => {
  return route().current(childRoute)
    ? 'bg-primary/10 text-primary font-medium'
    : 'text-muted-foreground-1 hover:bg-primary-50 hover:text-primary';
};

const onChildClick = () => {
  emit('child-click');
};
</script>
