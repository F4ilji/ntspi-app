<template>
  <div class="space-y-4">
    <!-- Toolbar -->
    <div class="flex items-center justify-between px-4 py-3 bg-white border border-layer-line rounded-lg">
      <h3 class="text-sm font-medium text-foreground">
        {{ label || 'Контент' }}
      </h3>
      <button
        type="button"
        @click="showBlockPicker = true"
        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-all"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Добавить блок
      </button>
    </div>

    <!-- Blocks List -->
    <div class="space-y-3">
      <div
        v-for="(block, index) in blocks"
        :key="block._uid"
        class="group relative bg-white border border-layer-line rounded-lg"
      >
        <!-- Block Header -->
        <div class="flex items-center gap-2 px-4 py-3 bg-muted/30 border-b border-layer-line">
          <!-- Drag Handle -->
          <button
            type="button"
            class="cursor-move text-muted-foreground-1 hover:text-foreground transition-colors"
            @mousedown="startDrag(index, $event)"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" />
            </svg>
          </button>

          <!-- Block Icon & Label -->
          <div class="flex items-center gap-2 flex-1">
            <component :is="getBlockIcon(block.type)" class="w-4 h-4 text-muted-foreground-1" />
            <span class="text-sm font-medium text-foreground">{{ getBlockLabel(block.type) }}</span>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button
              type="button"
              @click="duplicateBlock(index)"
              class="p-1.5 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded transition-all"
              title="Клонировать"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            </button>
            <button
              type="button"
              @click="toggleBlock(index)"
              class="p-1.5 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded transition-all"
              :title="collapsedBlocks.includes(index) ? 'Развернуть' : 'Свернуть'"
            >
              <svg
                class="w-4 h-4 transition-transform"
                :class="{ 'rotate-180': !collapsedBlocks.includes(index) }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <button
              type="button"
              @click="removeBlock(index)"
              class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
              title="Удалить"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Block Content -->
        <div v-show="!collapsedBlocks.includes(index)" class="p-4">
          <component
            :is="getBlockComponent(block.type)"
            v-model="block.data"
            :all-blocks="blocks"
            @update="emitChange"
          />
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="blocks.length === 0"
        class="flex flex-col items-center justify-center py-12 px-4 border-2 border-dashed border-layer-line rounded-lg bg-muted/20"
      >
        <svg class="w-12 h-12 text-muted-foreground-1 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-sm font-medium text-foreground mb-1">Нет блоков контента</p>
        <p class="text-xs text-muted-foreground-1 text-center">Нажмите "Добавить блок" чтобы начать</p>
      </div>
    </div>

    <!-- Block Picker Modal -->
    <div
      v-if="showBlockPicker"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
      @click.self="showBlockPicker = false"
    >
      <div class="bg-layer border border-layer-line rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-layer-line flex items-center justify-between">
          <h3 class="text-lg font-medium text-foreground">Выберите тип блока</h3>
          <button
            type="button"
            @click="showBlockPicker = false"
            class="p-1.5 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[60vh]">
          <div class="grid grid-cols-3 gap-3">
            <button
              v-for="blockType in availableBlocks"
              :key="blockType.type"
              type="button"
              @click="addBlock(blockType.type)"
              class="flex flex-col items-center gap-2 p-4 border border-layer-line rounded-lg hover:border-primary hover:bg-primary/5 transition-all group"
            >
              <component :is="getBlockIcon(blockType.type)" class="w-8 h-8 text-muted-foreground-1 group-hover:text-primary transition-colors" />
              <span class="text-xs font-medium text-foreground text-center group-hover:text-primary transition-colors">
                {{ getBlockLabel(blockType.type) }}
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';

// Block Components
import HeadingBlock from './blocks/HeadingBlock.vue';
import ParagraphBlock from './blocks/ParagraphBlock.vue';
import ImageBlock from './blocks/ImageBlock.vue';
import ImagesBlock from './blocks/ImagesBlock.vue';
import FilesBlock from './blocks/FilesBlock.vue';
import FastFilesBlock from './blocks/FastFilesBlock.vue';
import VideoBlock from './blocks/VideoBlock.vue';
import PersonBlock from './blocks/PersonBlock.vue';
import StepperBlock from './blocks/StepperBlock.vue';
import TabBlock from './blocks/TabBlock.vue';
import SliderBlock from './blocks/SliderBlock.vue';
import PostItemBlock from './blocks/PostItemBlock.vue';
import PostListBlock from './blocks/PostListBlock.vue';
import PageItemBlock from './blocks/PageItemBlock.vue';
import PageResourceListBlock from './blocks/PageResourceListBlock.vue';
import ContactBlock from './blocks/ContactBlock.vue';
import CustomFormBlock from './blocks/CustomFormBlock.vue';

// Icons
import IconHeading from './icons/IconHeading.vue';
import IconParagraph from './icons/IconParagraph.vue';
import IconImage from './icons/IconImage.vue';
import IconImages from './icons/IconImages.vue';
import IconFiles from './icons/IconFiles.vue';
import IconVideo from './icons/IconVideo.vue';
import IconPerson from './icons/IconPerson.vue';
import IconStepper from './icons/IconStepper.vue';
import IconTabs from './icons/IconTabs.vue';
import IconSlider from './icons/IconSlider.vue';
import IconPost from './icons/IconPost.vue';
import IconPage from './icons/IconPage.vue';
import IconContact from './icons/IconContact.vue';
import IconForm from './icons/IconForm.vue';
import IconArchive from './icons/IconArchive.vue';

export default {
  name: 'ContentBuilder',
  components: {
    HeadingBlock,
    ParagraphBlock,
    ImageBlock,
    ImagesBlock,
    FilesBlock,
    FastFilesBlock,
    VideoBlock,
    PersonBlock,
    StepperBlock,
    TabBlock,
    SliderBlock,
    PostItemBlock,
    PostListBlock,
    PageItemBlock,
    PageResourceListBlock,
    ContactBlock,
    CustomFormBlock,
  },

  props: {
    modelValue: {
      type: Array,
      default: () => []
    },
    label: {
      type: String,
      default: ''
    }
  },

  emits: ['update:modelValue'],

  setup(props, { emit }) {
    const blocks = ref([]);
    const collapsedBlocks = ref([]);
    const showBlockPicker = ref(false);
    let uidCounter = 0;

    const availableBlocks = [
      { type: 'heading' },
      { type: 'paragraph' },
      { type: 'image' },
      { type: 'images' },
      { type: 'files' },
      { type: 'fast_files' },
      { type: 'video' },
      { type: 'person' },
      { type: 'stepper' },
      { type: 'tabs' },
      { type: 'slider' },
      { type: 'postItem' },
      { type: 'postsList' },
      { type: 'pageItem' },
      { type: 'pageResourceList' },
      { type: 'contact' },
      { type: 'customForm' },
    ];

    const blockLabels = {
      heading: 'Заголовок',
      paragraph: 'Текст',
      image: 'Изображение',
      images: 'Слайдер изображений',
      files: 'Файлы',
      fast_files: 'Быстрая загрузка файлов',
      video: 'Видео',
      person: 'Персона',
      stepper: 'Этапы',
      tabs: 'Вкладки',
      slider: 'Слайдер',
      postItem: 'Конкретная новость',
      postsList: 'Список новостей',
      pageItem: 'Конкретная страница',
      pageResourceList: 'Ресурсы',
      contact: 'Контакты',
      customForm: 'Пользовательская форма',
    };

    const blockIcons = {
      heading: IconHeading,
      paragraph: IconParagraph,
      image: IconImage,
      images: IconImages,
      files: IconFiles,
      fast_files: IconFiles,
      video: IconVideo,
      person: IconPerson,
      stepper: IconStepper,
      tabs: IconTabs,
      slider: IconSlider,
      postItem: IconPost,
      postsList: IconPost,
      pageItem: IconPage,
      pageResourceList: IconArchive,
      contact: IconContact,
      customForm: IconForm,
    };

    const blockDefaults = {
      heading: () => ({ id: `anchor-${Date.now()}`, content: '' }),
      paragraph: () => ({ seo_active: true, content: '' }),
      image: () => ({ url: '', alt: '' }),
      images: () => ({ url: [], alt: '' }),
      files: () => ({ file: [] }),
      fast_files: () => ({ path: [] }),
      video: () => ({ mime: '', title: '', path: '' }),
      person: () => ({ name: '', photo: '', info: [{ column: '', content: '' }] }),
      stepper: () => ({ step_name: '', steps: [{ title: '', content: '' }] }),
      tabs: () => ({ settings: { is_accordion: false }, tab: [{ title: '', content: [] }] }),
      slider: () => ({ slider: '' }),
      postItem: () => ({ post: null }),
      postsList: () => ({ count: 5, category: null }),
      pageItem: () => ({ page: null }),
      pageResourceList: () => ({ resource: '' }),
      contact: () => ({ contact: '' }),
      customForm: () => ({ form: '', settings: { in_modal: false } }),
    };

    function generateUid() {
      return `block-${Date.now()}-${uidCounter++}`;
    }

    function addBlock(type) {
      blocks.value.push({
        _uid: generateUid(),
        type,
        data: blockDefaults[type]()
      });
      showBlockPicker.value = false;
      emitChange();
    }

    function removeBlock(index) {
      blocks.value.splice(index, 1);
      collapsedBlocks.value = collapsedBlocks.value.filter(i => i !== index);
      emitChange();
    }

    function duplicateBlock(index) {
      const original = blocks.value[index];
      blocks.value.splice(index + 1, 0, {
        _uid: generateUid(),
        type: original.type,
        data: JSON.parse(JSON.stringify(original.data))
      });
      emitChange();
    }

    function toggleBlock(index) {
      const idx = collapsedBlocks.value.indexOf(index);
      if (idx === -1) {
        collapsedBlocks.value.push(index);
      } else {
        collapsedBlocks.value.splice(idx, 1);
      }
    }

    function getBlockComponent(type) {
      const componentMap = {
        heading: 'HeadingBlock',
        paragraph: 'ParagraphBlock',
        image: 'ImageBlock',
        images: 'ImagesBlock',
        files: 'FilesBlock',
        fast_files: 'FastFilesBlock',
        video: 'VideoBlock',
        person: 'PersonBlock',
        stepper: 'StepperBlock',
        tabs: 'TabBlock',
        slider: 'SliderBlock',
        postItem: 'PostItemBlock',
        postsList: 'PostListBlock',
        pageItem: 'PageItemBlock',
        pageResourceList: 'PageResourceListBlock',
        contact: 'ContactBlock',
        customForm: 'CustomFormBlock',
      };
      return componentMap[type] || 'ParagraphBlock';
    }

    function getBlockIcon(type) {
      return blockIcons[type] || IconParagraph;
    }

    function getBlockLabel(type) {
      return blockLabels[type] || type;
    }

    function emitChange() {
      const output = blocks.value.map(({ _uid, ...block }) => block);
      emit('update:modelValue', output);
    }

    // Drag and Drop
    let dragIndex = null;

    function startDrag(index, event) {
      dragIndex = index;
      document.addEventListener('mousemove', onDrag);
      document.addEventListener('mouseup', stopDrag);
    }

    function onDrag(event) {
      // Simplified drag logic - can be enhanced with a library like vuedraggable
    }

    function stopDrag() {
      dragIndex = null;
      document.removeEventListener('mousemove', onDrag);
      document.removeEventListener('mouseup', stopDrag);
    }

    // Track if we've initialized from server data
    let initialized = false;

    // Initialize from modelValue
    onMounted(() => {
      if (props.modelValue && props.modelValue.length > 0) {
        blocks.value = props.modelValue.map(block => ({
          _uid: generateUid(),
          ...block
        }));
        initialized = true;
      }
    });

    // Watch for modelValue changes (e.g., when loading existing content)
    watch(
      () => props.modelValue,
      (newValue) => {
        if (newValue && newValue.length > 0 && !initialized) {
          // Only initialize once (initial load from server)
          blocks.value = newValue.map(block => ({
            _uid: generateUid(),
            ...block
          }));
          initialized = true;
        }
      },
      { deep: true }
    );

    return {
      blocks,
      collapsedBlocks,
      showBlockPicker,
      availableBlocks,
      addBlock,
      removeBlock,
      duplicateBlock,
      toggleBlock,
      getBlockComponent,
      getBlockIcon,
      getBlockLabel,
      emitChange,
      startDrag,
    };
  }
}
</script>
