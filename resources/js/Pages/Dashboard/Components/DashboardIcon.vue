<template>
  <BasicIcon
    v-if="iconName"
    :name="iconName"
    :class="iconClass"
    :color="color"
  />
</template>

<script>
import BasicIcon from '../../../componentss/ui/icons/BasicIcon.vue';

/**
 * DashboardIcon - стандартизированный компонент иконок для Dashboard
 * 
 * Использует SVG sprite систему через BasicIcon.
 * Все Heroicons доступны из папки resources/js/assets/icons/svg/
 * 
 * @example
 * // Базовое использование
 * <DashboardIcon name="building-office" />
 * 
 * @example
 * // С размером и цветом
 * <DashboardIcon name="plus" size="5" color="text-primary" />
 * 
 * @example
 * // С кастомными классами
 * <DashboardIcon name="trash" size="4" class="hover:text-danger" />
 */
export default {
  name: 'DashboardIcon',
  components: {
    BasicIcon,
  },
  props: {
    /**
     * Имя иконки (без префикса heroicon-)
     * Примеры: 'building-office', 'plus', 'trash', 'pencil-square'
     */
    name: {
      type: String,
      required: true,
    },
    /**
     * Размер иконки (в единицах Tailwind: 3, 4, 5, 6 и т.д.)
     * @default '5'
     */
    size: {
      type: [String, Number],
      default: '5',
    },
    /**
     * Цвет иконки (Tailwind класс)
     * @default 'currentColor'
     */
    color: {
      type: String,
      default: 'currentColor',
    },
    /**
     * Дополнительные CSS классы
     */
    class: {
      type: String,
      default: '',
    },
  },
  computed: {
    iconName() {
      if (!this.name) return '';

      // Добавляем префикс heroicon-o- (outline) по умолчанию
      // Если имя уже содержит префикс, используем как есть
      if (this.name.startsWith('heroicon-')) {
        return this.name;
      }
      return `heroicon-o-${this.name}`;
    },
    iconClass() {
      const sizeMap = {
        '3': 'w-3 h-3',
        '4': 'w-4 h-4',
        '5': 'w-5 h-5',
        '6': 'w-6 h-6',
        '7': 'w-7 h-7',
        '8': 'w-8 h-8',
        '10': 'w-10 h-10',
        '12': 'w-12 h-12',
      };
      const sizeClass = sizeMap[String(this.size)] || 'w-5 h-5';
      return `${sizeClass} ${this.class}`.trim();
    },
  },
}
</script>
