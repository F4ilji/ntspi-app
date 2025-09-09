<template>
  <div
      class="relative w-full max-w-7xl mx-auto cursor-grab"
      ref="carouselContainer"
      @mousedown.prevent="onDragStart"
      @touchstart="onDragStart"
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
        class="absolute top-1/2 left-0 transform -translate-y-1/2 md:-ml-10 bg-gray-700/70 text-white p-2 rounded-full hover:bg-gray-600/90 focus:outline-none z-10 disabled:opacity-30 disabled:cursor-not-allowed transition-opacity"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
    </button>
    <button
        @click.stop="next"
        :disabled="currentIndex >= maxScrollIndex"
        class="absolute top-1/2 right-0 transform -translate-y-1/2 md:-mr-10 bg-gray-700/70 text-white p-2 rounded-full hover:bg-gray-600/90 focus:outline-none z-10 disabled:opacity-30 disabled:cursor-not-allowed transition-opacity"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
    </button>

    <div
        v-if="numberOfDots > 1"
        class="absolute -bottom-15 left-1/2 transform -translate-x-1/2 flex space-x-2 p-4 z-10"
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
  gapSizePx: {
    type: Number,
    default: 16,
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
  removeDragListeners();
});

const totalItems = computed(() => props.items.length);

const itemOuterWidthPercentage = computed(() => {
  if (currentItemsToShow.value <= 0 || !viewportElement.value) return 0;
  const viewportWidth = viewportElement.value.offsetWidth;
  if (viewportWidth === 0) return 0;
  const totalGapWidthPx = (currentItemsToShow.value - 1) * props.gapSizePx;
  const totalGapWidthPercentage = (totalGapWidthPx / viewportWidth) * 100;
  const availableWidthForItemsPercentage = 100 - totalGapWidthPercentage;
  return availableWidthForItemsPercentage / currentItemsToShow.value;
});

const itemPixelWidth = computed(() => {
  if (viewportElement.value && currentItemsToShow.value > 0) {
    const viewportWidth = viewportElement.value.offsetWidth;
    const totalGapWidthPx = (currentItemsToShow.value - 1) * props.gapSizePx;
    const widthOfSingleItemPx = (viewportWidth - totalGapWidthPx) / currentItemsToShow.value;
    return widthOfSingleItemPx + props.gapSizePx;
  }
  return 0;
});

const maxScrollIndex = computed(() => {
  if (!totalItems.value || totalItems.value <= currentItemsToShow.value) return 0;
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
    const effectiveItemWidth = (viewportElement.value.offsetWidth - (currentItemsToShow.value - 1) * props.gapSizePx) / currentItemsToShow.value;
    return -(clampedIndex * effectiveItemWidth + clampedIndex * props.gapSizePx);
  }
  return 0;
});

const numberOfDots = computed(() => {
  if (totalItems.value <= currentItemsToShow.value) return 0;
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
  if (event.target.closest('button')) {
    return;
  }

  if (totalItems.value <= currentItemsToShow.value || itemPixelWidth.value === 0) return;

  isDragging.value = true;
  const touch = event.type === 'touchstart' ? event.touches[0] : event;
  dragStartX.value = touch.clientX;
  dragCurrentX.value = touch.clientX;

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

  if (event.cancelable) {
    event.preventDefault();
  }

  const touch = event.type === 'touchmove' ? event.touches[0] : event;
  dragCurrentX.value = touch.clientX;
};

const onDragEnd = () => {
  if (!isDragging.value) {
    removeDragListeners();
    return;
  }
  isDragging.value = false;

  const draggedDistance = dragCurrentX.value - dragStartX.value;
  const effectiveItemWidthWithGap = (viewportElement.value.offsetWidth - (currentItemsToShow.value - 1) * props.gapSizePx) / currentItemsToShow.value + props.gapSizePx;

  if (effectiveItemWidthWithGap === 0) {
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