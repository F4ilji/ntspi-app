<template>
  <div class="max-w-6xl mx-auto p-6 mt-10">

    <!-- БЛОК 1: Форма загрузки (смешанные файлы) -->
    <div class="bg-white rounded-lg shadow-md p-8">
      <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">Загрузка файлов</h2>

      <!-- Уведомление об успехе -->
      <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-md">
        {{ $page.props.flash.success }}
      </div>

      <!-- Уведомление об ошибке -->
      <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-md">
        {{ $page.props.flash.error }}
      </div>

      <!-- Информация о созданном посте -->
      <div v-if="$page.props.flash?.created_post" class="mb-6 p-4 bg-indigo-50 border-l-4 border-indigo-500 rounded-r-md">
        <h4 class="font-semibold text-indigo-800 mb-2">Создана новость:</h4>
        <div class="text-sm text-indigo-700 space-y-1">
          <p><strong>Заголовок:</strong> {{ $page.props.flash.created_post.title }}</p>
          <p><strong>Статус:</strong> {{ $page.props.flash.created_post.status }}</p>
          <p><strong>Категория:</strong> {{ $page.props.flash.created_post.category?.title || 'Не указана' }}</p>
          
          <!-- Превью изображения -->
          <div v-if="$page.props.flash.created_post.preview" class="mt-2">
            <img
              :src="`/storage/${$page.props.flash.created_post.preview}`"
              alt="Preview"
              class="h-24 w-auto rounded border border-indigo-200"
            />
          </div>
          
          <div class="mt-3 flex gap-2">
            <a
              :href="`/admin/posts/${$page.props.flash.created_post.id}/edit`"
              class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition"
            >
              Редактировать в админке
            </a>
            <button
              @click.prevent="openPostModal($page.props.flash.created_post)"
              class="inline-flex items-center px-3 py-1.5 bg-white text-indigo-600 border border-indigo-600 text-sm font-medium rounded hover:bg-indigo-50 transition"
            >
              Быстрый просмотр
            </button>
          </div>
        </div>
      </div>

      <form @submit.prevent="submitForm" class="space-y-6">

        <!-- Поле для загрузки всех файлов -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">
            Файлы <span class="text-gray-400 font-normal">(DOCX для текста новости + изображения, PDF, приложения)</span>
          </label>

          <!-- Зона Drag & Drop -->
          <div
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleDrop"
              @click="triggerFileInput"
              :class="[
                'mt-1 flex justify-center px-6 pt-8 pb-8 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-200',
                isDragging ? 'border-indigo-500 bg-indigo-50 scale-[1.01]' : 'border-gray-300 hover:border-indigo-400 hover:bg-gray-50'
              ]"
          >
            <div class="space-y-2 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="flex text-sm text-gray-600 justify-center">
                <span class="relative cursor-pointer bg-transparent rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                  Выберите файлы
                </span>
                <p class="pl-1">или перетащите их сюда</p>
              </div>
              <p class="text-xs text-gray-500">
                DOCX, PDF, XLSX, JPG, PNG, WEBP, ZIP (до 20MB каждый)
              </p>
              <p class="text-xs text-indigo-600 font-medium">
                Система автоматически определит файл с текстом новости по названию или содержанию
              </p>
              <p class="text-xs text-green-600 font-medium mt-2">
                💡 Можно загрузить ZIP-архив — система автоматически распакует его и обработает содержимое
              </p>
            </div>
          </div>

          <!-- Скрытый системный инпут -->
          <input
              type="file"
              id="filesInput"
              multiple
              ref="filesInput"
              class="hidden"
              @change="handleFileInput"
              accept=".doc,.docx,.pdf,.xls,.xlsx,.jpg,.jpeg,.png,.webp,.gif,.zip"
          />

          <!-- Ошибки валидации -->
          <div v-if="hasFileErrors" class="mt-2 text-sm text-red-600 font-medium">
            <p v-if="form.errors.files">{{ form.errors.files }}</p>
            <ul class="list-disc pl-5 mt-1">
              <li v-for="(error, key) in fileSpecificErrors" :key="key">
                {{ error }}
              </li>
            </ul>
          </div>

          <!-- Список выбранных файлов (Превью) -->
          <ul v-if="form.files.length > 0" class="mt-4 divide-y divide-gray-100 border border-gray-200 rounded-lg bg-gray-50 shadow-sm">
            <li v-for="(file, index) in form.files" :key="index" class="pl-4 pr-4 py-3 flex items-center justify-between text-sm transition hover:bg-white">
              <div class="w-0 flex-1 flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                </svg>
                <span class="ml-2 flex-1 w-0 truncate text-gray-700 font-medium">
                  {{ file.name }}
                  <span class="text-gray-400 text-xs ml-1 font-normal">({{ (file.size / 1024 / 1024).toFixed(2) }} MB)</span>
                </span>
              </div>
              <div class="ml-4 flex-shrink-0">
                <button type="button" @click.prevent="removeFile(index)" class="font-medium text-red-500 hover:text-red-700 bg-red-50 px-2 py-1 rounded transition">
                  Удалить
                </button>
              </div>
            </li>
          </ul>
        </div>

        <!-- Прогресс-бар загрузки -->
        <div v-if="form.progress" class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
          <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300 ease-out" :style="{ width: form.progress.percentage + '%' }"></div>
        </div>

        <!-- Кнопка сабмита -->
        <button
            type="submit"
            :disabled="form.processing || form.files.length === 0"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="form.processing">
            Обработка и загрузка файлов ({{ form.progress ? form.progress.percentage : 0 }}%)...
          </span>
          <span v-else>Отправить файлы на сервер</span>
        </button>
      </form>
    </div>

    <!-- БЛОК 0: Список черновиков -->
    <div v-if="draftPosts && draftPosts.length > 0" class="bg-white rounded-lg shadow-md p-8">
      <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">
        Черновики (без publish_at)
        <span class="text-sm font-normal text-gray-500 ml-2">{{ draftPosts.length }} шт.</span>
      </h2>

      <div class="grid gap-4">
        <div
          v-for="post in draftPosts"
          :key="post.id"
          class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
          @click="openPostModal(post)"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ post.title }}</h3>
              <div class="flex flex-wrap gap-2 text-sm text-gray-600">
                <span class="bg-gray-100 px-2 py-1 rounded">ID: {{ post.id }}</span>
                <span v-if="post.category" class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded">
                  {{ post.category.title }}
                </span>
                <span v-if="post.authors" class="bg-gray-100 px-2 py-1 rounded">
                  Автор: {{ Array.isArray(post.authors) ? post.authors.join(', ') : post.authors }}
                </span>
              </div>
              <p v-if="post.preview_text" class="mt-3 text-sm text-gray-600 line-clamp-2">
                {{ post.preview_text }}
              </p>
              <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
                <span>Создано: {{ new Date(post.created_at).toLocaleDateString('ru-RU') }}</span>
                <span>Обновлено: {{ new Date(post.updated_at).toLocaleDateString('ru-RU') }}</span>
              </div>
            </div>
            <div class="ml-4 flex flex-col gap-2 items-end">
              <!-- Переключатель публикации в VK -->
              <label class="flex items-center gap-2 cursor-pointer mb-2" @click.stop>
                <input
                  v-model="postPublishSettings[post.id]"
                  type="checkbox"
                  class="sr-only peer"
                >
                <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                <span class="text-xs font-medium text-gray-600">VK</span>
              </label>
              
              <button
                @click.stop="publishPost(post)"
                :disabled="publishProcessing"
                class="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Опубликовать
              </button>
              <span class="text-indigo-600 hover:text-indigo-800 text-xs font-medium text-center">
                Подробнее →
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно просмотра новости -->
    <div
      v-if="selectedPost"
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
      @click.self="closePostModal"
    >
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-lg">
          <h3 class="text-xl font-bold text-gray-800">{{ selectedPost.title }}</h3>
          <button
            @click="closePostModal"
            class="text-gray-400 hover:text-gray-600 transition"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6">
          <!-- Мета информация -->
          <div class="flex flex-wrap gap-2 mb-4 text-sm">
            <span class="bg-gray-100 px-3 py-1 rounded-full">ID: {{ selectedPost.id }}</span>
            <span v-if="selectedPost.category" class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full">
              {{ selectedPost.category.title }}
            </span>
            <span v-if="selectedPost.status" class="bg-green-50 text-green-700 px-3 py-1 rounded-full">
              {{ selectedPost.status }}
            </span>
          </div>

          <!-- Главное изображение (Preview) -->
          <div v-if="selectedPost.preview" class="mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-2">Главное изображение:</p>
            <div class="rounded-lg overflow-hidden border border-gray-200">
              <img
                :src="`/storage/${selectedPost.preview}`"
                alt="Preview"
                class="w-full h-auto max-h-96 object-cover"
              />
            </div>
          </div>

          <!-- Галерея изображений -->
          <div v-if="selectedPost.images && selectedPost.images.length > 0" class="mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-2">Галерея ({{ selectedPost.images.length }}):</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
              <div
                v-for="(image, index) in selectedPost.images"
                :key="index"
                class="rounded-lg overflow-hidden border border-gray-200 aspect-square"
              >
                <img
                  :src="`/storage/${image}`"
                  alt="Gallery image {{ index + 1 }}"
                  class="w-full h-full object-cover cursor-pointer hover:opacity-80 transition"
                  @click="viewImage(image)"
                />
              </div>
            </div>
          </div>

          <!-- Авторы -->
          <div v-if="selectedPost.authors" class="mb-4 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm font-semibold text-gray-700 mb-2">Автор(ы):</p>
            <p class="text-sm text-gray-600">
              {{ Array.isArray(selectedPost.authors) ? selectedPost.authors.join(', ') : selectedPost.authors }}
            </p>
          </div>

          <!-- Превью -->
          <div v-if="selectedPost.preview_text" class="mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-2">Превью:</p>
            <p class="text-gray-600">{{ selectedPost.preview_text }}</p>
          </div>

          <!-- Полный контент -->
          <div class="mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-2">Полный текст:</p>
            <div class="prose max-w-none bg-gray-50 p-4 rounded-lg border border-gray-200">
              <div v-html="renderContent(selectedPost.content)"></div>
            </div>
          </div>

          <!-- Дополнительная информация -->
          <div class="border-t pt-4 text-xs text-gray-500">
            <div class="flex justify-between">
              <span>Создано: {{ new Date(selectedPost.created_at).toLocaleString('ru-RU') }}</span>
              <span>Обновлено: {{ new Date(selectedPost.updated_at).toLocaleString('ru-RU') }}</span>
            </div>
            <div v-if="selectedPost.reading_time" class="mt-1">
              Время чтения: {{ selectedPost.reading_time }} мин.
            </div>
          </div>
        </div>

        <!-- Кнопки действий -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-between items-center rounded-b-lg">
          <div class="flex items-center gap-4">
            <!-- Переключатель публикации в VK -->
            <button
              type="button"
              @click.stop="postPublishSettings[selectedPost?.id] = !postPublishSettings[selectedPost?.id]"
              class="flex items-center gap-2 cursor-pointer focus:outline-none"
            >
              <div class="relative">
                <div class="w-11 h-6 bg-gray-200 rounded-full transition-colors" :class="postPublishSettings[selectedPost?.id] ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-transform" :class="postPublishSettings[selectedPost?.id] ? 'translate-x-5' : 'translate-x-0'"></div>
              </div>
              <span class="text-sm font-medium text-gray-700">Опубликовать в VK</span>
            </button>
            
            <!-- Кнопка "Опубликовать" (только для черновиков) -->
            <button
              v-if="!selectedPost.publish_at"
              @click="publishPost(selectedPost)"
              :disabled="publishProcessing"
              class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="publishProcessing">
                Публикация...
              </span>
              <span v-else>
                Опубликовать
              </span>
            </button>
            <!-- Кнопка "Редактировать в админке" -->
            <a
              :href="`/admin/posts/${selectedPost.id}/edit`"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition"
            >
              Редактировать
            </a>
          </div>
          <button
            @click="closePostModal"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
          >
            Закрыть
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';

// Сохраняем имя компонента
defineOptions({
  name: "Main",
});

// Получаем данные из Inertia
const page = usePage();
const draftPosts = computed(() => page.props.draftPosts || []);

// Состояние для модального окна
const selectedPost = ref(null);

// Состояние для процесса публикации
const publishProcessing = ref(false);

// Состояние для переключателя публикации в VK (отдельно для каждого поста)
const postPublishSettings = ref({});

// Инициализируем переключатели для всех постов
watch(draftPosts, (posts) => {
  posts.forEach(post => {
    if (postPublishSettings.value[post.id] === undefined) {
      postPublishSettings.value[post.id] = true;
    }
  });
}, { immediate: true });

// Открытие модального окна
const openPostModal = (post) => {
  selectedPost.value = post;
};

// Закрытие модального окна
const closePostModal = () => {
  selectedPost.value = null;
};

// Публикация поста
const publishPost = (post) => {
  if (!confirm(`Опубликовать новость "${post.title}"?`)) {
    return;
  }

  publishProcessing.value = true;

  router.post(route('dashboard.posts.publish', post.id), {
    publish_to_vk: postPublishSettings.value[post.id] ?? true,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      publishProcessing.value = false;
      // Закрываем модальное окно, если оно открыто
      if (selectedPost.value?.id === post.id) {
        closePostModal();
      }
    },
    onError: () => {
      publishProcessing.value = false;
    },
  });
};

// Рендеринг контента (массив блоков или строка)
const renderContent = (content) => {
  if (!content) return '';

  // Если контент - массив блоков (например, редактор)
  if (Array.isArray(content)) {
    return content.map(block => {
      if (typeof block === 'string') return block;
      if (block.type === 'paragraph' && block.data?.content) {
        return block.data.content;
      }
      if (block.type === 'heading' && block.data?.content) {
        return `<h2>${block.data.content}</h2>`;
      }
      if (block.data?.content) {
        return block.data.content;
      }
      return '';
    }).join('');
  }

  // Если контент - строка
  return content;
};

// Просмотр изображения
const viewImage = (imagePath) => {
  window.open(`/storage/${imagePath}`, '_blank');
};

// Инициализация формы Inertia
const form = useForm({
  files: [], // Массив для всех файлов
});

// Состояния для кастомного Dropzone
const isDragging = ref(false);
const filesInput = ref(null);

// Имитация клика по скрытому input type="file"
const triggerFileInput = () => {
  filesInput.value.click();
};

// Обработка перетаскивания файлов (Drag & Drop)
const handleDrop = (e) => {
  isDragging.value = false;
  const files = Array.from(e.dataTransfer.files);
  addFilesToForm(files);
};

// Обработка выбора файлов через стандартное окно браузера
const handleFileInput = (e) => {
  const files = Array.from(e.target.files);
  addFilesToForm(files);
  // Сбрасываем value, чтобы можно было удалить файл из списка и выбрать его снова
  e.target.value = '';
};

// Функция добавления файлов в массив с защитой от дублей
const addFilesToForm = (files) => {
  files.forEach(file => {
    // Проверяем, нет ли уже файла с таким именем и размером
    const exists = form.files.some(f => f.name === file.name && f.size === file.size);
    if (!exists) {
      form.files.push(file);
    }
  });
};

// Удаление конкретного файла из массива
const removeFile = (index) => {
  form.files.splice(index, 1);
};

// Отправка формы
const submitForm = () => {
  form.post(route('dashboard.files.store'), {
    preserveScroll: true,
    onSuccess: () => {
      // Очищаем данные в стейте useForm
      form.reset();
      // Очищаем визуальный input
      const filesInputElement = document.getElementById('filesInput');
      if (filesInputElement) filesInputElement.value = '';
    },
  });
};

// Вычисляемые свойства для удобного отображения ошибок валидации массива files
const hasFileErrors = computed(() => {
  return Object.keys(form.errors).some(key => key.startsWith('files'));
});

const fileSpecificErrors = computed(() => {
  const errors = {};
  Object.keys(form.errors).forEach(key => {
    if (key.startsWith('files.')) {
      errors[key] = form.errors[key];
    }
  });
  return errors;
});
</script>
