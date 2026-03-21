<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-6">

      <!-- Заголовок страницы -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Панель управления</h1>
        <p class="text-gray-600">Загрузка и модерация новостей, созданных с помощью AI</p>
      </div>

      <!-- Уведомления (Flash messages) -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-4"
      >
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm">
          <div class="flex items-start">
            <svg class="w-5 h-5 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-emerald-800 font-medium">{{ $page.props.flash.success }}</span>
          </div>
        </div>
      </transition>

      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-4"
      >
        <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
          <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-red-800 font-medium">{{ $page.props.flash.error }}</span>
          </div>
        </div>
      </transition>

      <!-- Информация о созданном посте -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div v-if="$page.props.flash?.created_post" class="mb-8 bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 border border-indigo-200 rounded-2xl p-6 shadow-lg">
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-bold text-indigo-900 mb-3">Новость успешно создана!</h4>
              <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div class="space-y-2">
                  <p class="text-indigo-900"><span class="font-semibold">Заголовок:</span> {{ $page.props.flash.created_post.title }}</p>
                  <p class="text-indigo-900"><span class="font-semibold">Статус:</span> 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                      {{ $page.props.flash.created_post.status }}
                    </span>
                  </p>
                  <p class="text-indigo-900"><span class="font-semibold">Категория:</span> {{ $page.props.flash.created_post.category?.title || 'Не указана' }}</p>
                </div>
                <div v-if="$page.props.flash.created_post.preview" class="flex justify-center md:justify-end">
                  <img
                    :src="`/storage/${$page.props.flash.created_post.preview}`"
                    alt="Preview"
                    class="h-32 w-auto rounded-xl shadow-md border-2 border-white object-cover"
                  />
                </div>
              </div>

              <div class="mt-4 flex gap-3">
                <a
                  :href="`/admin/posts/${$page.props.flash.created_post.id}/edit`"
                  target="_blank" rel="external"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-md hover:shadow-lg"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Редактировать
                  <svg class="w-3.5 h-3.5 ml-2 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                  </svg>
                </a>
                <button
                  @click.prevent="openPostModal($page.props.flash.created_post)"
                  class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 border border-indigo-200 text-sm font-medium rounded-lg hover:bg-indigo-50 transition shadow-sm hover:shadow-md"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Просмотр
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <!-- Карточка загрузки файлов -->
      <div class="bg-white rounded-2xl shadow-xl mb-8 overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold text-white mb-1">Загрузка файлов</h2>
              <p class="text-indigo-100 text-sm">
                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                DOCX, PDF, XLSX, JPG, PNG, WEBP, ZIP (до 40MB)
              </p>
            </div>
            <button
              @click="openUploadModal"
              class="group inline-flex items-center px-6 py-3.5 bg-white text-indigo-600 font-semibold rounded-xl hover:bg-indigo-50 transition-all shadow-lg hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white"
            >
              <svg class="w-5 h-5 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              Загрузить
            </button>
          </div>
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
          <p class="text-sm text-gray-600">
            <span class="font-semibold text-indigo-600">💡 AI-функции:</span>
            Автоматическое определение текста новости • Распаковка ZIP-архивов (до 40MB) • Извлечение изображений
          </p>
        </div>
      </div>

    <!-- Модальное окно загрузки файлов -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isUploadModalOpen"
        class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closeUploadModal"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden transform transition-all flex flex-col">
          <div class="sticky top-0 bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 px-8 py-5 flex items-center justify-between rounded-t-2xl flex-shrink-0">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-white">Загрузка файлов</h3>
            </div>
            <button
              @click="closeUploadModal"
              class="text-white hover:text-indigo-100 transition bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg p-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="overflow-y-auto flex-1">
            <form @submit.prevent="submitForm" class="p-8 space-y-6">
            <!-- Зона Drag & Drop -->
            <div
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleDrop"
              @click="triggerFileInput"
              :class="[
                'relative mt-1 flex justify-center px-6 pt-10 pb-12 border-2 border-dashed rounded-2xl cursor-pointer transition-all duration-300',
                isDragging 
                  ? 'border-indigo-500 bg-indigo-50 scale-[1.02] shadow-inner' 
                  : 'border-gray-300 hover:border-indigo-400 hover:bg-gradient-to-br hover:from-gray-50 hover:to-indigo-50'
              ]"
            >
              <div class="space-y-4 text-center">
                <div class="flex justify-center">
                  <div :class="['w-20 h-20 rounded-2xl flex items-center justify-center transition-all duration-300', isDragging ? 'bg-indigo-200 scale-110' : 'bg-indigo-100']">
                    <svg class="w-10 h-10 text-indigo-600" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                      <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
                <div>
                  <p class="text-lg font-semibold text-gray-700">
                    <span class="text-indigo-600">Нажмите для выбора</span> или перетащите файлы
                  </p>
                  <p class="text-sm text-gray-500 mt-1">
                    DOCX, PDF, XLSX, JPG, PNG, WEBP, ZIP
                  </p>
                </div>
                <div class="flex justify-center gap-4 text-xs">
                  <span class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full font-medium">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Авто-распаковка ZIP
                  </span>
                  <span class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-full font-medium">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Извлечение файлов
                  </span>
                  <span class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 rounded-full font-medium">
                    до 40MB
                  </span>
                </div>
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
            <div v-if="hasFileErrors" class="p-4 bg-red-50 border border-red-200 rounded-xl">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm text-red-700">
                  <p v-if="form.errors.files" class="font-semibold mb-1">{{ form.errors.files }}</p>
                  <ul class="list-disc pl-5 space-y-1">
                    <li v-for="(error, key) in fileSpecificErrors" :key="key">
                      {{ error }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Индикатор распаковки ZIP -->
            <div v-if="isUnzipping" class="p-4 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 border border-indigo-200 rounded-xl">
              <div class="flex items-center gap-3 mb-3">
                <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-semibold text-indigo-900">Распаковка архива...</span>
              </div>
              <div class="w-full bg-white rounded-full h-3 overflow-hidden shadow-inner">
                <div
                  class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-full transition-all duration-300 ease-out"
                  :style="{ width: unzippingProgress + '%' }"
                ></div>
              </div>
              <p class="text-xs text-indigo-700 mt-2 text-center">{{ unzippingProgress }}% завершено</p>
            </div>

            <!-- Список выбранных файлов -->
            <div v-if="form.files.length > 0" class="space-y-6">
              <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Выбрано файлов: {{ form.files.length }}
              </h4>

              <!-- Секция изображений (с drag-and-drop) -->
              <div v-if="imageFiles.length > 0" class="space-y-2">
                <div class="flex items-center gap-2 mb-3">
                  <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <h5 class="text-base font-bold text-gray-800">Изображения <span class="text-xs font-normal text-gray-500">(перетаскивайте для изменения порядка)</span></h5>
                  <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">{{ imageFiles.length }}</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                  <div
                    v-for="(item, newIndex) in imageFiles"
                    :key="item.originalIndex"
                    draggable="true"
                    @dragstart="handleImageDragStart($event, newIndex)"
                    @dragover="handleImageDragOver"
                    @dragleave="handleImageDragLeave"
                    @drop="handleImageDrop($event, newIndex)"
                    @dragend="handleImageDragEnd"
                    class="group relative bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg hover:border-indigo-300 transition-all duration-200 overflow-hidden cursor-move"
                  >
                    <!-- Миниатюра изображения -->
                    <div class="aspect-square overflow-hidden">
                      <img
                        :src="imagePreviews[item.originalIndex]"
                        alt="Preview"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-200"
                      />
                    </div>

                    <!-- Градиентная полоска снизу -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                    <!-- Информация при наведении -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                      <div class="text-center p-2">
                        <p class="text-white text-xs font-medium truncate max-w-[120px]">{{ item.file.name }}</p>
                        <p class="text-indigo-200 text-[10px] mt-0.5">{{ (item.file.size / 1024 / 1024).toFixed(2) }} MB</p>
                      </div>
                    </div>

                    <!-- Кнопка удаления -->
                    <button
                      type="button"
                      @click.prevent="removeFile(item.originalIndex)"
                      class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-lg opacity-0 group-hover:opacity-100 hover:bg-red-600 transition-all duration-200 shadow-lg z-10"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>

                    <!-- Индикатор drag-and-drop -->
                    <div class="absolute top-2 left-2 p-1.5 bg-white bg-opacity-90 rounded-lg cursor-move opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-opacity-100 z-10">
                      <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                      </svg>
                    </div>

                    <!-- Бейдж порядка -->
                    <div class="absolute top-2 right-2 w-6 h-6 bg-white bg-opacity-90 rounded-full flex items-center justify-center text-xs font-bold text-indigo-600 shadow-md">
                      {{ newIndex + 1 }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Секция остальных файлов -->
              <div v-if="otherFiles.length > 0" class="space-y-2">
                <div class="flex items-center gap-2 mb-3">
                  <div class="w-8 h-8 bg-gradient-to-br from-slate-500 to-gray-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <h5 class="text-base font-bold text-gray-800">Файлы</h5>
                  <span class="text-xs font-semibold text-slate-600 bg-slate-50 px-2 py-1 rounded-full">{{ otherFiles.length }}</span>
                </div>
                <div class="space-y-2">
                  <div
                    v-for="(item) in otherFiles"
                    :key="item.originalIndex"
                    class="group flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:border-slate-300 transition-all"
                  >
                    <!-- Иконка файла -->
                    <div class="flex-shrink-0">
                      <div
                        class="w-14 h-14 rounded-lg bg-gradient-to-br from-slate-100 to-gray-100 flex items-center justify-center border border-slate-200"
                      >
                        <svg class="w-7 h-7 text-slate-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>

                    <!-- Информация о файле -->
                    <div class="flex-1 min-w-0">
                      <p class="text-gray-800 font-medium truncate text-sm">{{ item.file.name }}</p>
                      <p class="text-xs text-gray-500 mt-0.5">
                        {{ (item.file.size / 1024 / 1024).toFixed(2) }} MB
                      </p>
                      <p class="text-xs text-slate-600 font-medium mt-1">
                        {{ getFileTypeIcon(item.file.type) }}
                      </p>
                    </div>

                    <!-- Кнопка удаления -->
                    <button
                      type="button"
                      @click.prevent="removeFile(item.originalIndex)"
                      class="flex-shrink-0 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Прогресс-бар загрузки -->
            <div v-if="form.progress" class="space-y-2">
              <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-700">Загрузка...</span>
                <span class="text-indigo-600 font-semibold">{{ form.progress.percentage }}%</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden shadow-inner">
                <div
                  class="h-full bg-gradient-to-r from-indigo-500 via-blue-500 to-indigo-600 rounded-full transition-all duration-300 ease-out relative overflow-hidden"
                  :style="{ width: form.progress.percentage + '%' }"
                >
                  <div class="absolute inset-0 bg-white bg-opacity-20 animate-pulse"></div>
                </div>
              </div>
            </div>

            <!-- Кнопка сабмита -->
            <button
                type="submit"
                :disabled="form.processing || form.files.length === 0"
                class="w-full py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-semibold text-white bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 hover:from-indigo-700 hover:via-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:shadow-xl hover:scale-[1.02] disabled:hover:scale-100"
            >
              <span v-if="form.processing" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Обработка ({{ form.progress ? form.progress.percentage : 0 }}%)...
              </span>
              <span v-else class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Отправить файлы на сервер
              </span>
            </button>
          </form>
          </div>
        </div>
      </div>
    </transition>

    <!-- БЛОК 0: Список подготовленных AI новостей -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 translate-y-8"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-8"
    >
      <div v-if="aiPreparedPosts && aiPreparedPosts.length > 0" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Заголовок секции -->
        <div class="bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 px-8 py-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                  Подготовленные новости (AI)
                  <span class="inline-flex items-center px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-full text-sm font-semibold">
                    {{ aiPreparedPosts.length }}
                  </span>
                </h2>
                <p class="text-purple-100 text-sm mt-1">
                  Новости, автоматически сгенерированные из загруженных файлов
                </p>
              </div>
            </div>
            <div class="hidden lg:flex items-center gap-2 px-4 py-2 bg-white bg-opacity-10 backdrop-blur-sm rounded-xl">
              <svg class="w-5 h-5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-purple-100 text-sm font-medium">Требуют модерации</span>
            </div>
          </div>
        </div>

        <!-- Список постов -->
        <div class="p-6 bg-gradient-to-br from-gray-50 via-white to-purple-50">
          <div class="grid gap-4">
            <div
              v-for="(post, index) in aiPreparedPosts"
              :key="post.id"
              class="group bg-white rounded-xl p-5 border border-gray-200 hover:border-purple-300 hover:shadow-lg hover:shadow-purple-100 transition-all duration-300 cursor-pointer transform hover:-translate-y-0.5"
              @click="openPostModal(post)"
              :style="{ animationDelay: `${index * 50}ms` }"
            >
              <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                  <!-- Заголовок и бейджи -->
                  <div class="flex items-start justify-between gap-3 mb-3">
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-purple-700 transition-colors line-clamp-2">
                      {{ post.title }}
                    </h3>
                    <!-- Статус поста -->
                    <span
                      v-if="post.status"
                      class="flex-shrink-0 inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide"
                      :class="{
                        'bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 border border-amber-200': post.status === 'verification',
                        'bg-gradient-to-r from-red-100 to-rose-100 text-red-800 border border-red-200': post.status === 'rejected',
                        'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200': post.status === 'published'
                      }"
                    >
                      <span class="w-2 h-2 rounded-full mr-2" :class="{
                        'bg-amber-500': post.status === 'verification',
                        'bg-red-500': post.status === 'rejected',
                        'bg-green-500': post.status === 'published'
                      }"></span>
                      {{ post.status === 'verification' ? 'На рассмотрении' : post.status === 'rejected' ? 'Отклонено' : post.status }}
                    </span>
                  </div>

                  <!-- Мета информация -->
                  <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium">
                      ID: {{ post.id }}
                    </span>
                    <span v-if="post.category" class="inline-flex items-center px-2.5 py-1 bg-gradient-to-r from-indigo-50 to-blue-50 text-indigo-700 rounded-md text-xs font-medium border border-indigo-100">
                      <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                      </svg>
                      {{ post.category.title }}
                    </span>
                    <span v-if="post.authors" class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium">
                      <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      {{ Array.isArray(post.authors) ? post.authors.join(', ') : post.authors }}
                    </span>
                  </div>

                  <!-- Превью текста -->
                  <p v-if="post.preview_text" class="text-sm text-gray-600 line-clamp-2 leading-relaxed">
                    {{ post.preview_text }}
                  </p>

                  <!-- Даты -->
                  <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      {{ new Date(post.created_at).toLocaleDateString('ru-RU') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                      {{ new Date(post.updated_at).toLocaleDateString('ru-RU') }}
                    </span>
                    <span v-if="post.reading_time" class="flex items-center gap-1.5">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                      </svg>
                      {{ post.reading_time }} мин.
                    </span>
                  </div>
                </div>

                <!-- Панель действий -->
                <div class="flex-shrink-0 flex flex-col items-end gap-3">
                  <!-- Переключатель VK -->
                  <label class="flex items-center gap-2.5 cursor-pointer group/toggle" @click.stop>
                    <div class="relative">
                      <input
                        v-model="postPublishSettings[post.id]"
                        type="checkbox"
                        class="sr-only peer"
                      >
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-indigo-500 peer-checked:to-purple-600"></div>
                    </div>
                    <span class="text-xs font-semibold text-gray-600 group-hover/toggle:text-purple-600 transition-colors flex items-center gap-1">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.477 2 2 6.477 2 12c0 5.523 4.477 10 10 10s10-4.477 10-10c0-5.523-4.477-10-10-10zm0 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z"/>
                      </svg>
                      VK
                    </span>
                  </label>

                  <!-- Кнопки -->
                  <div class="flex flex-col gap-2">
                    <button
                      @click.stop="publishPost(post)"
                      :disabled="publishProcessing"
                      class="group/btn inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                    >
                      <svg v-if="!publishProcessing" class="w-4 h-4 mr-2 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <svg v-else class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ publishProcessing ? '...' : 'Опубликовать' }}
                    </button>
                    <span class="text-purple-600 group-hover:text-purple-800 text-xs font-semibold transition-colors flex items-center gap-1 justify-center">
                      Подробнее
                      <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Модальное окно просмотра новости -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="selectedPost"
        class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closePostModal"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col transform transition-all animate-fade-in">
          <!-- Заголовок -->
          <div class="sticky top-0 bg-gradient-to-r from-slate-700 via-gray-700 to-slate-800 px-8 py-5 flex items-center justify-between rounded-t-2xl">
            <div class="flex items-center gap-4 flex-1 min-w-0">
              <div class="w-10 h-10 bg-white bg-opacity-10 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-white truncate">{{ selectedPost.title }}</h3>
            </div>
            <button
              @click="closePostModal"
              class="ml-4 text-gray-300 hover:text-white transition bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg p-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Контент -->
          <div class="p-8 overflow-y-auto flex-1">
            <!-- Мета информация -->
            <div class="flex flex-wrap gap-2 mb-6">
              <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                ID: {{ selectedPost.id }}
              </span>
              <span v-if="selectedPost.category" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-indigo-50 to-blue-50 text-indigo-700 rounded-lg text-sm font-medium border border-indigo-100">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                {{ selectedPost.category.title }}
              </span>
              <span
                v-if="selectedPost.status"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold uppercase tracking-wide"
                :class="{
                  'bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 border border-amber-200': selectedPost.status === 'verification',
                  'bg-gradient-to-r from-red-100 to-rose-100 text-red-800 border border-red-200': selectedPost.status === 'rejected',
                  'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200': selectedPost.status === 'published'
                }"
              >
                <span class="w-2 h-2 rounded-full mr-2" :class="{
                  'bg-amber-500': selectedPost.status === 'verification',
                  'bg-red-500': selectedPost.status === 'rejected',
                  'bg-green-500': selectedPost.status === 'published'
                }"></span>
                {{ selectedPost.status === 'verification' ? 'На рассмотрении' : selectedPost.status === 'rejected' ? 'Отклонено' : selectedPost.status }}
              </span>
            </div>

            <!-- Главное изображение (Preview) -->
            <div v-if="selectedPost.preview" class="mb-6">
              <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Главное изображение
              </p>
              <div class="rounded-xl overflow-hidden shadow-lg border-2 border-gray-100">
                <img
                  :src="`/storage/${selectedPost.preview}`"
                  alt="Preview"
                  class="w-full h-auto max-h-96 object-cover"
                />
              </div>
            </div>

            <!-- Галерея изображений -->
            <div v-if="selectedPost.images && selectedPost.images.length > 0" class="mb-6">
              <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Галерея ({{ selectedPost.images.length }})
              </p>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <div
                  v-for="(image, index) in selectedPost.images"
                  :key="index"
                  class="group relative rounded-xl overflow-hidden border-2 border-gray-100 shadow-md aspect-square cursor-pointer"
                >
                  <img
                    :src="`/storage/${image}`"
                    alt="Gallery image {{ index + 1 }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                    @click="viewImage(image)"
                  />
                  <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all"></div>
                </div>
              </div>
            </div>

            <!-- Авторы -->
            <div v-if="selectedPost.authors" class="mb-6 p-4 bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl border border-gray-200">
              <p class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Автор(ы):
              </p>
              <p class="text-gray-700">
                {{ Array.isArray(selectedPost.authors) ? selectedPost.authors.join(', ') : selectedPost.authors }}
              </p>
            </div>

            <!-- Превью -->
            <div v-if="selectedPost.preview_text" class="mb-6">
              <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Превью:
              </p>
              <p class="text-gray-700 leading-relaxed">{{ selectedPost.preview_text }}</p>
            </div>

            <!-- Полный контент -->
            <div class="mb-6">
              <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Полный текст:
              </p>
              <div class="prose prose-sm max-w-none bg-gradient-to-br from-gray-50 to-slate-50 p-5 rounded-xl border border-gray-200">
                <div v-html="renderContent(selectedPost.content)" class="text-gray-700 leading-relaxed"></div>
              </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="border-t border-gray-200 pt-4">
              <div class="flex flex-wrap justify-between gap-4 text-xs text-gray-500">
                <div class="flex items-center gap-4">
                  <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Создано: {{ new Date(selectedPost.created_at).toLocaleString('ru-RU') }}
                  </span>
                  <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Обновлено: {{ new Date(selectedPost.updated_at).toLocaleString('ru-RU') }}
                  </span>
                </div>
                <span v-if="selectedPost.reading_time" class="flex items-center gap-1.5">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                  {{ selectedPost.reading_time }} мин.
                </span>
              </div>
            </div>
          </div>

          <!-- Кнопки действий -->
          <div class="sticky bottom-0 bg-gradient-to-r from-gray-50 via-slate-50 to-gray-50 border-t border-gray-200 px-8 py-5 flex justify-between items-center rounded-b-2xl">
            <div class="flex items-center gap-4">
              <!-- Переключатель публикации в VK -->
              <button
                type="button"
                @click.stop="postPublishSettings[selectedPost?.id] = !postPublishSettings[selectedPost?.id]"
                class="flex items-center gap-3 cursor-pointer focus:outline-none group"
              >
                <div class="relative">
                  <div class="w-12 h-7 bg-gray-200 rounded-full transition-colors group-hover:bg-gray-300" :class="postPublishSettings[selectedPost?.id] ? 'bg-gradient-to-r from-indigo-500 to-purple-600 group-hover:from-indigo-600 group-hover:to-purple-700' : ''"></div>
                  <div class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full transition-transform shadow-md" :class="postPublishSettings[selectedPost?.id] ? 'translate-x-5' : 'translate-x-0'"></div>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-purple-600 transition-colors flex items-center gap-2">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.477 2 2 6.477 2 12c0 5.523 4.477 10 10 10s10-4.477 10-10c0-5.523-4.477-10-10-10zm0 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z"/>
                  </svg>
                  Опубликовать в VK
                </span>
              </button>

              <!-- Кнопка "Опубликовать" -->
              <button
                v-if="!selectedPost.publish_at"
                @click="publishPost(selectedPost)"
                :disabled="publishProcessing"
                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
              >
                <svg v-if="!publishProcessing" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ publishProcessing ? 'Публикация...' : 'Опубликовать' }}
              </button>

              <!-- Кнопка "Редактировать в админке" -->
              <a
                :href="`/admin/posts/${selectedPost.id}/edit`"
                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all shadow-md hover:shadow-lg hover:scale-105"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Редактировать
              </a>
            </div>
            <button
              @click="closePostModal"
              class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-semibold"
            >
              Закрыть
            </button>
          </div>
        </div>
      </div>
    </transition>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import JSZip from 'jszip';

// Сохраняем имя компонента
defineOptions({
  name: "Main",
});

// Получаем данные из Inertia
const page = usePage();
const aiPreparedPosts = computed(() => page.props.aiPreparedPosts || []);

// Состояние для модальных окон
const selectedPost = ref(null);
const isUploadModalOpen = ref(false);

// Состояние для процесса публикации
const publishProcessing = ref(false);

// Состояние для переключателя публикации в VK (отдельно для каждого поста)
const postPublishSettings = ref({});

// Состояние для превью изображений
const imagePreviews = ref({}); // { index: dataUrl }

// Состояние для drag-and-drop изображений
const draggedImageIndex = ref(null);

// Состояние для процесса распаковки ZIP
const isUnzipping = ref(false);
const unzippingProgress = ref(0);

// Инициализируем переключатели для всех постов
watch(aiPreparedPosts, (posts) => {
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

// Открытие модального окна загрузки
const openUploadModal = () => {
  isUploadModalOpen.value = true;
};

// Закрытие модального окна загрузки
const closeUploadModal = () => {
  isUploadModalOpen.value = false;
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

// Проверка, является ли файл изображением
const isImageFile = (file) => {
  return file.type.startsWith('image/');
};

// Создание превью для изображения
const createPreview = (file, index) => {
  if (!isImageFile(file)) return;
  
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreviews.value[index] = e.target.result;
  };
  reader.readAsDataURL(file);
};

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
const addFilesToForm = async (files) => {
  for (const file of files) {
    // Проверяем, нет ли уже файла с таким именем и размером
    const exists = form.files.some(f => f.name === file.name && f.size === f.size);
    
    if (exists) continue;

    // Если это ZIP-архив, распаковываем его
    if (file.type === 'application/zip' || file.type === 'application/x-zip-compressed' || 
        file.name.toLowerCase().endsWith('.zip')) {
      await unzipFile(file);
    } else {
      // Обычный файл
      const newIndex = form.files.length;
      form.files.push(file);
      // Создаём превью для изображений
      createPreview(file, newIndex);
    }
  }
};

// Распаковка ZIP-архива
const unzipFile = async (zipFile) => {
  isUnzipping.value = true;
  unzippingProgress.value = 0;
  
  try {
    const zip = await JSZip.loadAsync(zipFile);
    const fileEntries = Object.entries(zip.files).filter(([name, file]) => !file.dir);
    const totalFiles = fileEntries.length;
    
    // Фильтруем только поддерживаемые файлы
    const supportedExtensions = ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'webp', 'gif'];
    
    for (const [relativePath, file] of fileEntries) {
      // Пропускаем системные файлы
      if (relativePath.startsWith('__MACOSX') || relativePath.includes('.DS_Store')) {
        continue;
      }
      
      // Проверяем расширение
      const ext = relativePath.split('.').pop().toLowerCase();
      if (!supportedExtensions.includes(ext)) {
        continue;
      }
      
      // Получаем содержимое файла
      const blob = await file.async('blob');
      
      // Создаём новый File объект из Blob
      const newFile = new File([blob], getBasename(relativePath), {
        type: blob.type || getFileMimeType(ext)
      });
      
      // Проверяем на дубликаты
      const exists = form.files.some(f => f.name === newFile.name && f.size === newFile.size);
      if (!exists) {
        const newIndex = form.files.length;
        form.files.push(newFile);
        
        // Создаём превью для изображений
        if (isImageFile(newFile)) {
          createPreview(newFile, newIndex);
        }
      }
      
      // Обновляем прогресс
      unzippingProgress.value = Math.round(((fileEntries.indexOf([relativePath, file]) + 1) / totalFiles) * 100);
    }
    
  } catch (error) {
    console.error('Ошибка при распаковке ZIP:', error);
    alert('Не удалось распаковать архив. Убедитесь, что это корректный ZIP-файл.');
  } finally {
    isUnzipping.value = false;
    unzippingProgress.value = 0;
  }
};

// Получение имени файла из полного пути
const getBasename = (path) => {
  return path.split('/').pop();
};

// Определение MIME-типа по расширению
const getFileMimeType = (ext) => {
  const mimeTypes = {
    'doc': 'application/msword',
    'docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'pdf': 'application/pdf',
    'xls': 'application/vnd.ms-excel',
    'xlsx': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'jpg': 'image/jpeg',
    'jpeg': 'image/jpeg',
    'png': 'image/png',
    'webp': 'image/webp',
    'gif': 'image/gif'
  };
  return mimeTypes[ext] || 'application/octet-stream';
};

// Удаление конкретного файла из массива
const removeFile = (index) => {
  form.files.splice(index, 1);
  // Удаляем превью для этого индекса
  delete imagePreviews.value[index];
  // Сдвигаем ключи превью после удаления
  const newPreviews = {};
  Object.entries(imagePreviews.value).forEach(([key, value]) => {
    const numericKey = parseInt(key);
    if (numericKey < index) {
      newPreviews[numericKey] = value;
    } else if (numericKey > index) {
      newPreviews[numericKey - 1] = value;
    }
  });
  imagePreviews.value = newPreviews;
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
      // Закрываем модальное окно
      closeUploadModal();
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

// Вычисляемые свойства для разделения файлов на изображения и остальные
const imageFiles = computed(() => {
  return form.files
    .map((file, index) => ({ file, originalIndex: index, isImage: isImageFile(file) }))
    .filter(item => item.isImage);
});

const otherFiles = computed(() => {
  return form.files
    .map((file, index) => ({ file, originalIndex: index, isImage: isImageFile(file) }))
    .filter(item => !item.isImage);
});

// Определение типа файла для отображения
const getFileTypeIcon = (type) => {
  if (!type) return '📁 Файл';
  if (type === 'application/pdf') return '📄 PDF';
  if (type === 'application/zip' || type === 'application/x-zip-compressed') return '📦 ZIP';
  if (type === 'application/msword' || type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') return '📝 DOCX';
  if (type === 'application/vnd.ms-excel' || type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') return '📊 XLSX';
  return '📁 Файл';
};

// Drag-and-drop методы для изображений
const handleImageDragStart = (event, index) => {
  draggedImageIndex.value = index;
  // Используем requestAnimationFrame для плавности
  requestAnimationFrame(() => {
    event.target.classList.add('opacity-50');
  });
};

const handleImageDragOver = (event) => {
  event.preventDefault();
  // Убираем transform, используем только визуальную подсветку
  event.currentTarget.classList.add('ring-2', 'ring-indigo-400', 'ring-offset-2');
};

const handleImageDragLeave = (event) => {
  event.currentTarget.classList.remove('ring-2', 'ring-indigo-400', 'ring-offset-2');
};

const handleImageDrop = (event, dropIndex) => {
  event.preventDefault();
  event.currentTarget.classList.remove('ring-2', 'ring-indigo-400', 'ring-offset-2');
  
  const dragIndex = draggedImageIndex.value;
  
  if (dragIndex === null || dragIndex === dropIndex) {
    return;
  }

  // Получаем оригинальные индексы изображений
  const imageIndexes = form.files
    .map((file, idx) => ({ idx, isImage: isImageFile(file) }))
    .filter(item => item.isImage)
    .map(item => item.idx);

  const fromOriginalIndex = imageIndexes[dragIndex];
  const toOriginalIndex = imageIndexes[dropIndex];

  // Меняем местами файлы в основном массиве
  const temp = form.files[fromOriginalIndex];
  form.files[fromOriginalIndex] = form.files[toOriginalIndex];
  form.files[toOriginalIndex] = temp;

  // Меняем местами превью
  const tempPreview = imagePreviews.value[fromOriginalIndex];
  imagePreviews.value[fromOriginalIndex] = imagePreviews.value[toOriginalIndex];
  imagePreviews.value[toOriginalIndex] = tempPreview;

  draggedImageIndex.value = null;
};

const handleImageDragEnd = (event) => {
  event.target.classList.remove('opacity-50');
  draggedImageIndex.value = null;
};
</script>
