<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="sparkles" size="5" class="text-primary" />
    </template>
    <template #header-title>AI подготовка новостей</template>
    <template #header-subtitle>Модерация новостей, подготовленных искусственным интеллектом</template>
    <template #header-actions>
      <button
        @click="parseEmailNews"
        :disabled="parseProcessing"
        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md"
      >
        <svg v-if="parseProcessing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <DashboardIcon v-else name="envelope" size="4" />
        {{ parseProcessing ? 'Парсинг...' : 'Распарсить Email' }}
      </button>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
        <div class="p-4 flex items-center justify-between">
          <div>
            <p class="text-sm text-muted-foreground-1">На рассмотрении</p>
            <p class="text-2xl font-semibold text-amber-600">{{ verificationCount }}</p>
          </div>
          <div class="w-12 h-12 bg-amber-500/10 rounded-lg flex items-center justify-center">
            <DashboardIcon name="clock" size="6" class="text-amber-600" />
          </div>
        </div>
      </div>

      <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
        <div class="p-4 flex items-center justify-between">
          <div>
            <p class="text-sm text-muted-foreground-1">Отклонено</p>
            <p class="text-2xl font-semibold text-rose-600">{{ rejectedCount }}</p>
          </div>
          <div class="w-12 h-12 bg-rose-500/10 rounded-lg flex items-center justify-center">
            <DashboardIcon name="exclamation-triangle" size="6" class="text-rose-600" />
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Card -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden mb-6">
      <div class="px-4 py-3 border-b border-card-line">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-primary/10 rounded-lg flex items-center justify-center">
              <DashboardIcon name="cloud-arrow-up" size="5" class="text-primary" />
            </div>
            <div>
              <h2 class="text-sm font-medium text-foreground">Загрузка файлов</h2>
              <p class="text-xs text-muted-foreground-1 mt-0.5">DOCX, PDF, XLSX, JPG, PNG, WEBP, ZIP</p>
            </div>
          </div>
          <button
            @click="openUploadModal"
            class="inline-flex items-center px-3 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all"
          >
            <DashboardIcon name="plus" size="4" class="mr-1.5" />
            Загрузить
          </button>
        </div>
      </div>
      <div class="px-4 py-3 bg-surface border-t border-line-2">
        <div class="flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white rounded-md text-xs font-medium text-muted-foreground-1 border border-layer-line">
            <DashboardIcon name="bolt" size="4" class="text-primary" />
            Авто-распаковка ZIP
          </span>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white rounded-md text-xs font-medium text-muted-foreground-1 border border-layer-line">
            <DashboardIcon name="check-circle" size="4" class="text-emerald-600" />
            Извлечение изображений
          </span>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white rounded-md text-xs font-medium text-muted-foreground-1 border border-layer-line">
            <DashboardIcon name="sparkles" size="4" class="text-amber-600" />
            AI-генерация
          </span>
        </div>
      </div>
    </div>

    <!-- AI Prepared Posts Accordion -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <div class="px-4 py-3 border-b border-card-line bg-surface">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-primary/10 rounded-lg flex items-center justify-center">
              <DashboardIcon name="sparkles" size="5" class="text-primary" />
            </div>
            <div>
              <h2 class="text-sm font-medium text-foreground">Подготовленные новости (AI)</h2>
              <p class="text-xs text-muted-foreground-1 mt-0.5">Требуют модерации</p>
            </div>
          </div>
          <span class="inline-flex items-center px-2.5 py-1.5 bg-primary-500/10 border border-primary-200 text-primary-700 rounded-full text-xs font-medium">
            {{ aiPreparedPosts.length }}
          </span>
        </div>
      </div>

      <div class="divide-y divide-line-2">
        <div
          v-for="post in aiPreparedPosts"
          :key="post.id"
          class="group"
        >
          <!-- Accordion Header -->
          <button
            @click="togglePost(post.id)"
            class="w-full px-4 py-4 flex items-center gap-4 hover:bg-surface/50 transition-colors text-left"
            :aria-expanded="expandedPosts[post.id]"
          >
            <!-- Preview Image -->
            <div v-if="post.preview" class="flex-shrink-0">
              <div class="w-16 h-16 rounded-lg overflow-hidden border border-layer-line">
                <img
                  :src="`/storage/${post.preview}`"
                  alt="Preview"
                  class="w-full h-full object-cover"
                />
              </div>
            </div>
            <div v-else class="flex-shrink-0 w-16 h-16 rounded-lg border border-layer-line bg-surface flex items-center justify-center">
              <DashboardIcon name="photo" size="8" class="text-muted-foreground-2" />
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                  <h3 class="text-sm font-medium text-foreground truncate group-hover:text-primary transition-colors">
                    {{ post.title }}
                  </h3>
                  <div class="flex flex-wrap items-center gap-2 mt-1.5">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase"
                      :class="{
                        'bg-amber-50 text-amber-700 border border-amber-200': post.status === 'verification',
                        'bg-rose-50 text-rose-700 border border-rose-200': post.status === 'rejected',
                        'bg-emerald-50 text-emerald-700 border border-emerald-200': post.status === 'published'
                      }"
                    >
                      <span class="w-1.5 h-1.5 rounded-full mr-1" :class="{
                        'bg-amber-500': post.status === 'verification',
                        'bg-rose-500': post.status === 'rejected',
                        'bg-emerald-500': post.status === 'published'
                      }"></span>
                      {{ post.status === 'verification' ? 'На рассмотрении' : post.status === 'rejected' ? 'Отклонено' : 'Опубликовано' }}
                    </span>
                    <span v-if="post.category" class="inline-flex items-center px-2 py-0.5 bg-layer text-muted-foreground-1 rounded text-xs font-medium border border-layer-line">
                      {{ post.category.title }}
                    </span>
                    <span class="text-xs text-muted-foreground-2">
                      {{ new Date(post.created_at).toLocaleDateString('ru-RU') }}
                    </span>
                  </div>
                </div>

                <!-- Chevron -->
                <DashboardIcon
                  name="chevron-down"
                  size="5"
                  class="text-muted-foreground-2 flex-shrink-0 transition-transform duration-200"
                  :class="{ 'rotate-180': expandedPosts[post.id] }"
                />
              </div>
            </div>
          </button>

          <!-- Accordion Content -->
          <div v-show="expandedPosts[post.id]" class="px-4 pb-4 border-t border-line-2">
            <div class="pt-4 space-y-4">
              <!-- Meta Info -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-xs font-medium text-muted-foreground-1 mb-1">Автор(ы)</p>
                  <p class="text-sm text-foreground">
                    {{ Array.isArray(post.authors) ? post.authors.join(', ') : post.authors || '—' }}
                  </p>
                </div>
                <div>
                  <p class="text-xs font-medium text-muted-foreground-1 mb-1">Время чтения</p>
                  <p class="text-sm text-foreground">{{ post.reading_time ? `${post.reading_time} мин` : '—' }}</p>
                </div>
              </div>

              <!-- Preview Text -->
              <div v-if="post.preview_text">
                <p class="text-xs font-medium text-muted-foreground-1 mb-1">Превью</p>
                <p class="text-sm text-foreground leading-relaxed">{{ post.preview_text }}</p>
              </div>

              <!-- Full Content -->
              <div>
                <p class="text-xs font-medium text-muted-foreground-1 mb-1">Полный текст</p>
                <div class="prose prose-sm max-w-none bg-surface p-4 rounded-lg border border-layer-line">
                  <div v-html="renderContent(post.content)" class="text-foreground"></div>
                </div>
              </div>

              <!-- Gallery -->
              <div v-if="post.images && post.images.length > 0">
                <p class="text-xs font-medium text-muted-foreground-1 mb-2">Галерея ({{ post.images.length }})</p>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                  <div
                    v-for="(image, index) in post.images"
                    :key="index"
                    @click="viewImage(image)"
                    class="rounded-lg overflow-hidden border border-layer-line aspect-square cursor-pointer hover:border-primary transition-all"
                  >
                    <img
                      :src="`/storage/${image}`"
                      alt="Gallery"
                      class="w-full h-full object-cover hover:scale-105 transition-transform"
                    />
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-line-2">
                <!-- VK Toggle -->
                <label class="flex items-center gap-2 cursor-pointer">
                  <div class="relative">
                    <input
                      v-model="postPublishSettings[post.id]"
                      type="checkbox"
                      class="sr-only peer"
                    >
                    <div class="w-9 h-5 bg-layer-line peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-layer-line after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                  </div>
                  <span class="text-sm font-medium text-muted-foreground-1 hover:text-primary transition-colors flex items-center gap-1.5">
                    <DashboardIcon name="globe-alt" size="4" :class="postPublishSettings[post.id] ? 'text-primary' : 'text-muted-foreground-2'" />
                    Опубликовать в VK
                  </span>
                </label>

                <!-- Publish Button -->
                <button
                  v-if="!post.publish_at"
                  @click="publishPost(post)"
                  :disabled="publishProcessing"
                  class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <DashboardIcon v-if="!publishProcessing" name="check" size="4" class="mr-1.5" />
                  <svg v-else class="animate-spin h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ publishProcessing ? 'Публикация...' : 'Опубликовать' }}
                </button>

                <!-- Edit in Admin -->
                <a
                  :href="`/admin/posts/${post.id}/edit`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all"
                >
                  <DashboardIcon name="pencil-square" size="4" class="mr-1.5" />
                  Редактировать
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="aiPreparedPosts.length === 0" class="px-4 py-16 text-center">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-surface border border-layer-line flex items-center justify-center">
            <DashboardIcon name="sparkles" size="8" class="text-muted-foreground-2" />
          </div>
          <p class="text-foreground font-medium">AI новостей нет</p>
          <p class="text-sm text-muted-foreground-1 mt-1">Загруженные файлы будут автоматически обработаны AI</p>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <transition
      enter-active-class="transition duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isUploadModalOpen"
        class="fixed inset-0 bg-slate-900/70 backdrop-blur-md z-50 flex items-center justify-center p-4"
        @click.self="closeUploadModal"
      >
        <div class="bg-white border border-layer-line rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
          <!-- Header -->
          <div class="px-4 py-3 border-b border-card-line flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 bg-primary/10 rounded-lg flex items-center justify-center">
                <DashboardIcon name="cloud-arrow-up" size="5" class="text-primary" />
              </div>
              <h3 class="text-sm font-medium text-foreground">Загрузка файлов</h3>
            </div>
            <button
              @click="closeUploadModal"
              class="text-muted-foreground-2 hover:text-foreground hover:bg-layer rounded-lg p-1.5 transition-all"
            >
              <DashboardIcon name="x-mark" size="5" />
            </button>
          </div>

          <!-- Content -->
          <div class="overflow-y-auto flex-1 p-4">
            <form @submit.prevent="submitForm" class="space-y-4">
              <!-- Dropzone -->
              <div
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop"
                @click="triggerFileInput"
                :class="[
                  'relative flex justify-center px-6 pt-8 pb-8 border-2 border-dashed rounded-lg cursor-pointer transition-all',
                  isDragging
                    ? 'border-primary bg-primary-50'
                    : 'border-layer-line hover:border-primary hover:bg-primary-50/50'
                ]"
              >
                <div class="space-y-3 text-center">
                  <div class="flex justify-center">
                    <div :class="['w-14 h-14 rounded-lg flex items-center justify-center transition-all', isDragging ? 'bg-primary/20' : 'bg-primary/10']">
                      <DashboardIcon name="cloud-arrow-up" size="7" class="text-primary" />
                    </div>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-foreground">
                      <span class="text-primary">Нажмите</span> или перетащите файлы
                    </p>
                    <p class="text-xs text-muted-foreground-1 mt-0.5">
                      DOCX, PDF, XLSX, JPG, PNG, WEBP, ZIP до 40MB
                    </p>
                  </div>
                </div>
              </div>

              <!-- Hidden Input -->
              <input
                type="file"
                id="aiFilesInput"
                multiple
                ref="filesInput"
                class="hidden"
                @change="handleFileInput"
                accept=".doc,.docx,.pdf,.xls,.xlsx,.jpg,.jpeg,.png,.webp,.gif,.zip"
              />

              <!-- Errors -->
              <div v-if="hasFileErrors" class="p-3 bg-rose-50 border border-rose-200 rounded-lg">
                <div class="flex items-start gap-2">
                  <DashboardIcon name="exclamation-circle" size="4" class="text-rose-600 flex-shrink-0 mt-0.5" />
                  <div class="text-xs text-rose-700">
                    <p v-if="form.errors.files" class="font-medium mb-1">{{ form.errors.files }}</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                      <li v-for="(error, key) in fileSpecificErrors" :key="key">
                        {{ error }}
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Unzipping -->
              <div v-if="isUnzipping" class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex items-center gap-2 mb-2">
                  <svg class="animate-spin h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span class="text-xs font-medium text-amber-800">Распаковка...</span>
                </div>
                <div class="w-full bg-amber-100 rounded-full h-2 overflow-hidden">
                  <div
                    class="h-full bg-amber-500 rounded-full transition-all"
                    :style="{ width: unzippingProgress + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Selected Files -->
              <div v-if="form.files.length > 0" class="space-y-3">
                <h4 class="text-xs font-medium text-muted-foreground-1 uppercase">
                  Файлов выбрано: {{ form.files.length }}
                </h4>

                <!-- Images -->
                <div v-if="imageFiles.length > 0" class="space-y-2">
                  <div class="flex items-center gap-2">
                    <DashboardIcon name="photo" size="4" class="text-primary" />
                    <span class="text-sm font-medium text-foreground">Изображения</span>
                    <span class="text-xs text-muted-foreground-2">({{ imageFiles.length }})</span>
                  </div>
                  <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    <div
                      v-for="(item, newIndex) in imageFiles"
                      :key="item.originalIndex"
                      draggable="true"
                      @dragstart="handleImageDragStart($event, newIndex)"
                      @dragover="handleImageDragOver"
                      @dragleave="handleImageDragLeave"
                      @drop="handleImageDrop($event, newIndex)"
                      @dragend="handleImageDragEnd"
                      class="group relative bg-surface border border-layer-line rounded-lg overflow-hidden cursor-move hover:border-primary transition-all"
                    >
                      <div class="aspect-square">
                        <img
                          :src="imagePreviews[item.originalIndex]"
                          alt="Preview"
                          class="w-full h-full object-cover"
                        />
                      </div>
                      <button
                        type="button"
                        @click.prevent="removeFile(item.originalIndex)"
                        class="absolute top-1 right-1 p-1 bg-rose-500 text-white rounded opacity-0 group-hover:opacity-100 transition-opacity"
                      >
                        <DashboardIcon name="x-mark" size="3" />
                      </button>
                      <div class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full flex items-center justify-center text-xs font-medium text-primary shadow-sm">
                        {{ newIndex + 1 }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Other Files -->
                <div v-if="otherFiles.length > 0" class="space-y-1.5">
                  <div class="flex items-center gap-2">
                    <DashboardIcon name="document-text" size="4" class="text-primary" />
                    <span class="text-sm font-medium text-foreground">Файлы</span>
                    <span class="text-xs text-muted-foreground-2">({{ otherFiles.length }})</span>
                  </div>
                  <div class="space-y-1.5">
                    <div
                      v-for="(item) in otherFiles"
                      :key="item.originalIndex"
                      class="flex items-center gap-3 p-2 bg-surface border border-layer-line rounded-lg hover:border-primary transition-all"
                    >
                      <div class="w-10 h-10 rounded-lg bg-primary-500/10 flex items-center justify-center border border-primary-200">
                        <DashboardIcon name="document-text" size="5" class="text-primary" />
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-foreground truncate">{{ item.file.name }}</p>
                        <p class="text-xs text-muted-foreground-2">{{ (item.file.size / 1024 / 1024).toFixed(2) }} MB</p>
                      </div>
                      <button
                        type="button"
                        @click.prevent="removeFile(item.originalIndex)"
                        class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-all"
                      >
                        <DashboardIcon name="trash" size="4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Progress -->
              <div v-if="form.progress" class="space-y-1.5">
                <div class="flex justify-between text-xs">
                  <span class="font-medium text-muted-foreground-1">Загрузка...</span>
                  <span class="font-medium text-primary">{{ form.progress.percentage }}%</span>
                </div>
                <div class="w-full bg-layer-line rounded-full h-2 overflow-hidden">
                  <div
                    class="h-full bg-primary rounded-full transition-all"
                    :style="{ width: form.progress.percentage + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Submit -->
              <button
                type="submit"
                :disabled="form.processing || form.files.length === 0"
                class="w-full py-2.5 px-4 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary-200 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
              >
                <span v-if="form.processing" class="flex items-center justify-center gap-2">
                  <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>Обработка ({{ form.progress ? form.progress.percentage : 0 }}%)</span>
                </span>
                <span v-else class="flex items-center justify-center gap-2">
                  <DashboardIcon name="arrow-up-tray" size="4" />
                  Отправить файлы
                </span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </transition>
  </DashboardLayout>
</template>

<script>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import JSZip from 'jszip';
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

export default {
  name: 'AiPreparedPosts',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages
  },

  props: {
    aiPreparedPosts: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      expandedPosts: {},
      publishProcessing: false,
      parseProcessing: false,
      postPublishSettings: {},
      isUploadModalOpen: false,
      imagePreviews: {},
      draggedImageIndex: null,
      isUnzipping: false,
      unzippingProgress: 0,
      isDragging: false,
      form: useForm({
        files: [],
      }),
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('AI подготовка новостей');
  },

  computed: {
    verificationCount() {
      return this.aiPreparedPosts.filter(p => p.status === 'verification').length;
    },
    rejectedCount() {
      return this.aiPreparedPosts.filter(p => p.status === 'rejected').length;
    },
    hasFileErrors() {
      return Object.keys(this.form.errors).some(key => key.startsWith('files'));
    },
    fileSpecificErrors() {
      const errors = {};
      Object.keys(this.form.errors).forEach(key => {
        if (key.startsWith('files.')) {
          errors[key] = this.form.errors[key];
        }
      });
      return errors;
    },
    imageFiles() {
      return this.form.files
        .map((file, index) => ({ file, originalIndex: index, isImage: this.isImageFile(file) }))
        .filter(item => item.isImage);
    },
    otherFiles() {
      return this.form.files
        .map((file, index) => ({ file, originalIndex: index, isImage: this.isImageFile(file) }))
        .filter(item => !item.isImage);
    }
  },

  watch: {
    aiPreparedPosts: {
      handler(posts) {
        posts.forEach(post => {
          if (this.postPublishSettings[post.id] === undefined) {
            this.postPublishSettings[post.id] = true;
          }
        });
      },
      immediate: true
    }
  },

  methods: {
    parseEmailNews() {
      if (this.parseProcessing) return;
      this.parseProcessing = true;
      this.$inertia.post(route('dashboard.posts.ai-prepared.parse-email'), {}, {
        preserveScroll: true,
        onSuccess: () => {
          this.parseProcessing = false;
        },
        onError: () => {
          this.parseProcessing = false;
        },
      });
    },

    togglePost(postId) {
      this.expandedPosts[postId] = !this.expandedPosts[postId];
    },

    publishPost(post) {
      if (!confirm(`Опубликовать новость "${post.title}"?`)) {
        return;
      }

      this.publishProcessing = true;

      this.$inertia.post(route('dashboard.posts.publish', post.id), {
        publish_to_vk: this.postPublishSettings[post.id] ?? true,
      }, {
        preserveScroll: true,
        onSuccess: () => {
          this.publishProcessing = false;
        },
        onError: () => {
          this.publishProcessing = false;
        },
      });
    },

    renderContent(content) {
      if (!content) return '';

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

      return content;
    },

    viewImage(imagePath) {
      window.open(`/storage/${imagePath}`);
    },

    openUploadModal() {
      this.isUploadModalOpen = true;
    },

    closeUploadModal() {
      this.isUploadModalOpen = false;
    },

    isImageFile(file) {
      return file.type.startsWith('image/');
    },

    createPreview(file, index) {
      if (!this.isImageFile(file)) return;

      const reader = new FileReader();
      reader.onload = (e) => {
        this.imagePreviews[index] = e.target.result;
      };
      reader.readAsDataURL(file);
    },

    triggerFileInput() {
      this.$refs.filesInput.click();
    },

    handleDrop(e) {
      this.isDragging = false;
      const files = Array.from(e.dataTransfer.files);
      this.addFilesToForm(files);
    },

    handleFileInput(e) {
      const files = Array.from(e.target.files);
      this.addFilesToForm(files);
      e.target.value = '';
    },

    async addFilesToForm(files) {
      for (const file of files) {
        const exists = this.form.files.some(f => f.name === file.name && f.size === f.size);

        if (exists) continue;

        if (file.type === 'application/zip' || file.type === 'application/x-zip-compressed' ||
            file.name.toLowerCase().endsWith('.zip')) {
          await this.unzipFile(file);
        } else {
          const newIndex = this.form.files.length;
          this.form.files.push(file);
          this.createPreview(file, newIndex);
        }
      }
    },

    async unzipFile(zipFile) {
      this.isUnzipping = true;
      this.unzippingProgress = 0;

      try {
        const zip = await JSZip.loadAsync(zipFile);
        const fileEntries = Object.entries(zip.files).filter(([name, file]) => !file.dir);
        const totalFiles = fileEntries.length;

        const supportedExtensions = ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'webp', 'gif'];

        for (const [relativePath, file] of fileEntries) {
          if (relativePath.startsWith('__MACOSX') || relativePath.includes('.DS_Store')) {
            continue;
          }

          const ext = relativePath.split('.').pop().toLowerCase();
          if (!supportedExtensions.includes(ext)) {
            continue;
          }

          const blob = await file.async('blob');

          const newFile = new File([blob], this.getBasename(relativePath), {
            type: blob.type || this.getFileMimeType(ext)
          });

          const exists = this.form.files.some(f => f.name === newFile.name && f.size === newFile.size);
          if (!exists) {
            const newIndex = this.form.files.length;
            this.form.files.push(newFile);

            if (this.isImageFile(newFile)) {
              this.createPreview(newFile, newIndex);
            }
          }

          this.unzippingProgress = Math.round(((fileEntries.indexOf([relativePath, file]) + 1) / totalFiles) * 100);
        }

      } catch (error) {
        console.error('Ошибка при распаковке ZIP:', error);
        alert('Не удалось распаковать архив.');
      } finally {
        this.isUnzipping = false;
        this.unzippingProgress = 0;
      }
    },

    getBasename(path) {
      return path.split('/').pop();
    },

    getFileMimeType(ext) {
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
    },

    removeFile(index) {
      this.form.files.splice(index, 1);
      delete this.imagePreviews[index];
      const newPreviews = {};
      Object.entries(this.imagePreviews).forEach(([key, value]) => {
        const numericKey = parseInt(key);
        if (numericKey < index) {
          newPreviews[numericKey] = value;
        } else if (numericKey > index) {
          newPreviews[numericKey - 1] = value;
        }
      });
      this.imagePreviews = newPreviews;
    },

    submitForm() {
      this.form.post(route('dashboard.files.store'), {
        preserveScroll: true,
        onSuccess: () => {
          this.form.reset();
          const filesInputElement = document.getElementById('aiFilesInput');
          if (filesInputElement) filesInputElement.value = '';
          this.closeUploadModal();
        },
      });
    },

    handleImageDragStart(event, index) {
      this.draggedImageIndex = index;
      requestAnimationFrame(() => {
        event.target.classList.add('opacity-50');
      });
    },

    handleImageDragOver(event) {
      event.preventDefault();
      event.currentTarget.classList.add('ring-2', 'ring-primary', 'ring-offset-2');
    },

    handleImageDragLeave(event) {
      event.currentTarget.classList.remove('ring-2', 'ring-primary', 'ring-offset-2');
    },

    handleImageDrop(event, dropIndex) {
      event.preventDefault();
      event.currentTarget.classList.remove('ring-2', 'ring-primary', 'ring-offset-2');

      const dragIndex = this.draggedImageIndex;

      if (dragIndex === null || dragIndex === dropIndex) {
        return;
      }

      const imageIndexes = this.form.files
        .map((file, idx) => ({ idx, isImage: this.isImageFile(file) }))
        .filter(item => item.isImage)
        .map(item => item.idx);

      const fromOriginalIndex = imageIndexes[dragIndex];
      const toOriginalIndex = imageIndexes[dropIndex];

      const temp = this.form.files[fromOriginalIndex];
      this.form.files[fromOriginalIndex] = this.form.files[toOriginalIndex];
      this.form.files[toOriginalIndex] = temp;

      const tempPreview = this.imagePreviews[fromOriginalIndex];
      this.imagePreviews[fromOriginalIndex] = this.imagePreviews[toOriginalIndex];
      this.imagePreviews[toOriginalIndex] = tempPreview;

      this.draggedImageIndex = null;
    },

    handleImageDragEnd(event) {
      event.target.classList.remove('opacity-50');
      this.draggedImageIndex = null;
    }
  }
}
</script>
