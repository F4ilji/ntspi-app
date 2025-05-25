<template>
  <div
      class="relative w-full max-w-7xl mx-auto cursor-grab"
      ref="carouselContainer"
      @mousedown.prevent="onDragStart"
      @touchstart.prevent="onDragStart"
      :class="{ 'cursor-grabbing': isDragging }"
  >
    <div class="overflow-hidden h-full" ref="viewportElement">
      <div
          class="flex h-full gap-4" :class="{ 'transition-transform duration-500 ease-in-out': !isDragging }"
          :style="{ transform: `translateX(${currentSliderTranslateX}px)` }"
          ref="sliderElement"
      >
        <div
            v-for="(item, index) in items"
            :key="index"
            class="flex-shrink-0 h-full select-none"
            :style="{ width: `${itemOuterWidthPercentage}%` }"
        >
          <slot name="item" :item="item" :index="index"></slot>
        </div>
      </div>
    </div>

    <button
        @click.stop="prev"
        :disabled="currentIndex === 0"
        class="absolute top-1/2 left-0 transform -translate-y-1/2 -ml-4 md:-ml-10 bg-gray-700/70 text-white p-2 rounded-full hover:bg-gray-600/90 focus:outline-none z-20 disabled:opacity-30 disabled:cursor-not-allowed transition-opacity"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
    </button>
    <button
        @click.stop="next"
        :disabled="currentIndex >= maxScrollIndex"
        class="absolute top-1/2 right-0 transform -translate-y-1/2 -mr-4 md:-mr-10 bg-gray-700/70 text-white p-2 rounded-full hover:bg-gray-600/90 focus:outline-none z-20 disabled:opacity-30 disabled:cursor-not-allowed transition-opacity"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
    </button>

    <div
        v-if="numberOfDots > 1"
        class="absolute left-1/2 transform -translate-x-1/2 flex space-x-2 p-6 z-20"
    >
      <button
          v-for="dotIdx in numberOfDots"
          :key="dotIdx - 1"
          @click.stop="goToDot(dotIdx - 1)"
          class="w-2.5 h-2.5 rounded-full focus:outline-none transition-colors"
          :class="currentIndex === (dotIdx - 1) ? 'bg-gray-800' : 'bg-gray-400 hover:bg-gray-500'"
      ></button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  itemsToShowDesktop: {
    type: Number,
    default: 3,
  },
  itemsToShowMobile: {
    type: Number,
    default: 1,
  },
  mobileBreakpoint: {
    type: Number,
    default: 768,
  },
  dragThresholdFactor: {
    type: Number,
    default: 0.1,
  },
  gapSizePx: { // НОВЫЙ ПРОПС: Размер промежутка в пикселях
    type: Number,
    default: 16, // Соответствует TailwindCSS gap-4 (1rem = 16px)
  }
});

const carouselContainer = ref(null);
const viewportElement = ref(null);
const sliderElement = ref(null);

const currentIndex = ref(0);
const isDragging = ref(false);
const dragStartX = ref(0);
const dragCurrentX = ref(0);
const dragStartTranslateX = ref(0);

const currentItemsToShow = ref(props.itemsToShowDesktop);
let mediaQueryList = null;

const handleMediaQueryChange = (event) => {
  const newItemsToShowValue = event.matches ? props.itemsToShowMobile : props.itemsToShowDesktop;
  if (currentItemsToShow.value !== newItemsToShowValue) {
    currentItemsToShow.value = newItemsToShowValue;
  }
};

onMounted(() => {
  if (typeof window !== 'undefined') {
    mediaQueryList = window.matchMedia(`(max-width: ${props.mobileBreakpoint}px)`);
    handleMediaQueryChange(mediaQueryList);
    mediaQueryList.addEventListener('change', handleMediaQueryChange);
  }
});

onBeforeUnmount(() => {
  if (mediaQueryList) {
    mediaQueryList.removeEventListener('change', handleMediaQueryChange);
  }
  window.removeEventListener('mousemove', onDragMove);
  window.removeEventListener('mouseup', onDragEnd);
  window.removeEventListener('touchmove', onDragMove);
  window.removeEventListener('touchend', onDragEnd);
  window.removeEventListener('touchcancel', onDragEnd);
});

const totalItems = computed(() => props.items.length);

const itemOuterWidthPercentage = computed(() => {
  if (currentItemsToShow.value <= 0 || !viewportElement.value) return 0;

  const viewportWidth = viewportElement.value.offsetWidth;
  if (viewportWidth === 0) return 0;

  // Общая ширина промежутков, которую нужно вычесть из 100%
  // Промежутков на 1 меньше, чем элементов
  const totalGapWidthPx = (currentItemsToShow.value - 1) * props.gapSizePx;
  const totalGapWidthPercentage = (totalGapWidthPx / viewportWidth) * 100;

  // Ширина, доступная для самих элементов (без промежутков)
  const availableWidthForItemsPercentage = 100 - totalGapWidthPercentage;

  // Ширина каждого элемента как процент от общей ширины вьюпорта
  return availableWidthForItemsPercentage / currentItemsToShow.value;
});


const itemPixelWidth = computed(() => {
  if (viewportElement.value && currentItemsToShow.value > 0) {
    const viewportWidth = viewportElement.value.offsetWidth;
    const totalGapWidthPx = (currentItemsToShow.value - 1) * props.gapSizePx;

    // Ширина каждого элемента без учета промежутка
    const widthOfSingleItemPx = (viewportWidth - totalGapWidthPx) / currentItemsToShow.value;

    // Для расчета смещения `translateX` нам нужно смещаться на ширину элемента
    // плюс ширину одного промежутка, если он есть.
    // Если мы на последнем элементе, который не имеет промежутка справа,
    // но при переходе к следующему (если он есть), смещение все равно должно учитывать "отправную точку" следующего элемента.
    // Поэтому всегда добавляем gapSizePx.
    return widthOfSingleItemPx + props.gapSizePx;
  }
  return 0;
});


const maxScrollIndex = computed(() => {
  if (!totalItems.value || totalItems.value <= currentItemsToShow.value) return 0;
  // Максимальный индекс, до которого можно прокрутить, чтобы последний видимый элемент
  // был последним элементом в массиве items.
  return totalItems.value - currentItemsToShow.value;
});

watch(maxScrollIndex, (newMaxIndex) => {
  if (currentIndex.value > newMaxIndex) {
    currentIndex.value = newMaxIndex;
  }
}, { immediate: true });

const currentSliderTranslateX = computed(() => {
  if (isDragging.value) {
    const diffX = dragCurrentX.value - dragStartX.value;
    return dragStartTranslateX.value + diffX;
  }
  if (itemPixelWidth.value > 0) {
    const clampedIndex = Math.min(currentIndex.value, maxScrollIndex.value);
    // При вычислении translate, мы должны смещаться на ширину элемента
    // плюс один промежуток между элементами.
    // Но последний промежуток на самом деле не должен быть частью смещения
    // при прокрутке до последнего элемента.
    //
    // Давайте переопределим `itemPixelWidth` для ясности:
    // `effectiveItemWidth` - это ширина одного элемента без учета его правого промежутка.
    const effectiveItemWidth = (viewportElement.value.offsetWidth - (currentItemsToShow.value - 1) * props.gapSizePx) / currentItemsToShow.value;

    // Смещение = (индекс * ширина_элемента) + (индекс * размер_промежутка)
    return -(clampedIndex * effectiveItemWidth + clampedIndex * props.gapSizePx);
  }
  return 0;
});


const numberOfDots = computed(() => {
  if (totalItems.value === 0) return 0;
  if (totalItems.value > 0 && totalItems.value <= currentItemsToShow.value) return 1;
  // Количество точек равно максимальному индексу прокрутки + 1
  return maxScrollIndex.value + 1;
});

const next = () => {
  if (currentIndex.value < maxScrollIndex.value) {
    currentIndex.value++;
  }
};

const prev = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--;
  }
};

const goToDot = (dotIndex) => {
  currentIndex.value = Math.max(0, Math.min(dotIndex, maxScrollIndex.value));
};

const onDragStart = (event) => {
  // Не перетаскивать, если элементов мало для прокрутки
  if (totalItems.value <= currentItemsToShow.value || itemPixelWidth.value === 0) return;

  isDragging.value = true;
  const touch = event.type === 'touchstart' ? event.touches[0] : event;
  dragStartX.value = touch.clientX;
  dragCurrentX.value = touch.clientX;

  // Рассчитываем dragStartTranslateX на основе текущего валидного индекса
  const currentValidIndex = Math.min(currentIndex.value, maxScrollIndex.value);
  const effectiveItemWidth = (viewportElement.value.offsetWidth - (currentItemsToShow.value - 1) * props.gapSizePx) / currentItemsToShow.value;
  dragStartTranslateX.value = -(currentValidIndex * effectiveItemWidth + currentValidIndex * props.gapSizePx);

  window.addEventListener('mousemove', onDragMove);
  window.addEventListener('mouseup', onDragEnd);
  window.addEventListener('touchmove', onDragMove, { passive: false });
  window.addEventListener('touchend', onDragEnd);
  window.addEventListener('touchcancel', onDragEnd);

  if (carouselContainer.value) {
    carouselContainer.value.style.userSelect = 'none';
  }
};

const onDragMove = (event) => {
  if (!isDragging.value) return;
  const touch = event.type === 'touchmove' ? event.touches[0] : event;
  dragCurrentX.value = touch.clientX;
};

const onDragEnd = (event) => {
  if (!isDragging.value) {
    removeDragListeners();
    return;
  }
  isDragging.value = false;

  const draggedDistance = dragCurrentX.value - dragStartX.value;

  // Здесь мы должны использовать ширину одного элемента, *включая* его промежуток для расчета смещения.
  const effectiveItemWidthWithGap = (viewportElement.value.offsetWidth - (currentItemsToShow.value - 1) * props.gapSizePx) / currentItemsToShow.value + props.gapSizePx;

  if (effectiveItemWidthWithGap === 0) { // Защита от деления на ноль
    removeDragListeners();
    if (carouselContainer.value) carouselContainer.value.style.userSelect = '';
    return;
  }

  const activationThreshold = effectiveItemWidthWithGap * props.dragThresholdFactor;

  if (Math.abs(draggedDistance) > activationThreshold) {
    const itemsScrolledFractionally = draggedDistance / effectiveItemWidthWithGap;
    const itemsToShift = Math.round(itemsScrolledFractionally);

    if (itemsToShift !== 0) {
      let newIndex = currentIndex.value - itemsToShift;
      currentIndex.value = Math.max(0, Math.min(newIndex, maxScrollIndex.value));
    }
  }

  removeDragListeners();
  if (carouselContainer.value) {
    carouselContainer.value.style.userSelect = '';
  }
};

const removeDragListeners = () => {
  window.removeEventListener('mousemove', onDragMove);
  window.removeEventListener('mouseup', onDragEnd);
  window.removeEventListener('touchmove', onDragMove);
  window.removeEventListener('touchend', onDragEnd);
  window.removeEventListener('touchcancel', onDragEnd);
}

</script>

<style scoped>
.cursor-grabbing {
  cursor: grabbing;
}
</style>