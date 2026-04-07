<template>
  <div class="min-h-screen bg-background-2">
    <!-- Sticky Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon v-if="isEdit" name="pencil-square" size="5" class="text-primary" />
              <DashboardIcon v-else name="plus" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">{{ isEdit ? 'Редактирование новости' : 'Создание новости' }}</h1>
              <p class="text-xs text-muted-foreground-1">{{ isEdit ? 'Внесите изменения в публикацию' : 'Заполните все необходимые поля' }}</p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <a
              :href="route('dashboard.posts.index')"
              class="inline-flex items-center gap-2 px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all"
            >
              <DashboardIcon name="arrow-left" size="4" />
              <span class="hidden sm:inline">Назад</span>
            </a>
            <button
              type="button"
              @click="submitForm"
              :disabled="form.processing"
              class="inline-flex items-center gap-2 px-5 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md"
            >
              <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <DashboardIcon v-else name="check" size="4" />
              {{ isEdit ? 'Сохранить' : 'Создать' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Flash Messages -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
          <div class="flex items-start gap-3">
            <DashboardIcon name="check-circle" size="5" class="text-emerald-600 flex-shrink-0 mt-0.5" />
            <span class="text-sm text-foreground font-medium">{{ $page.props.flash.success }}</span>
          </div>
        </div>
      </transition>

      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-rose-500/10 border border-rose-500/20 rounded-lg">
          <div class="flex items-start gap-3">
            <DashboardIcon name="exclamation-circle" size="5" class="text-rose-600 flex-shrink-0 mt-0.5" />
            <span class="text-sm text-foreground font-medium">{{ $page.props.flash.error }}</span>
          </div>
        </div>
      </transition>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Tabs Navigation -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
          <div class="border-b border-line-2">
            <div class="flex overflow-x-auto">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                type="button"
                @click="switchTab(tab.id)"
                :class="[
                  'group inline-flex items-center gap-2 px-5 py-4 text-sm font-medium border-b-2 transition-all duration-200 whitespace-nowrap',
                  activeTab === tab.id
                    ? 'border-primary text-primary bg-primary/5'
                    : 'border-transparent text-muted-foreground-1 hover:text-foreground hover:border-line-2'
                ]"
              >
                <DashboardIcon v-if="tab.id === 'main'" name="information-circle" size="4" :class="activeTab === tab.id ? 'text-primary' : 'text-muted-foreground-2'" />
                <DashboardIcon v-if="tab.id === 'content'" name="document-text" size="4" :class="activeTab === tab.id ? 'text-primary' : 'text-muted-foreground-2'" />
                <DashboardIcon v-if="tab.id === 'media'" name="photo" size="4" :class="activeTab === tab.id ? 'text-primary' : 'text-muted-foreground-2'" />
                <DashboardIcon v-if="tab.id === 'slider'" name="presentation-chart-bar" size="4" :class="activeTab === tab.id ? 'text-primary' : 'text-muted-foreground-2'" />
                {{ tab.label }}
              </button>
            </div>
          </div>

          <!-- Tab Content -->
          <div class="p-6">
            <!-- Tab: Main Info -->
            <div v-if="activeTab === 'main'" class="space-y-6">
              <!-- Title & Slug -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                  <label for="title" class="block text-sm font-medium text-foreground mb-1.5">
                    Заголовок <span class="text-rose-500">*</span>
                  </label>
                  <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    :class="inputClass('title')"
                    placeholder="Введите заголовок новости"
                    @blur="generateSlug"
                  />
                  <p v-if="form.errors.title" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                    <DashboardIcon name="exclamation-circle" size="3" />
                    {{ form.errors.title }}
                  </p>
                  <p v-else class="mt-1.5 text-xs text-muted-foreground-1">Заголовок будет отображаться на сайте</p>
                </div>

                <div>
                  <label for="slug" class="block text-sm font-medium text-foreground mb-1.5">
                    URL-адрес <span class="text-rose-500">*</span>
                  </label>
                  <div class="flex gap-2">
                    <input
                      id="slug"
                      v-model="form.slug"
                      type="text"
                      :class="inputClass('slug')"
                      placeholder="url-novosti"
                      class="font-mono flex-1"
                      readonly
                    />
                  </div>
                  <p v-if="form.errors.slug" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                    <DashboardIcon name="exclamation-circle" size="3" />
                    {{ form.errors.slug }}
                  </p>
                  <p v-else class="mt-1.5 text-xs text-muted-foreground-1">
                    URL генерируется автоматически. Нажмите ↻ для обновления.
                  </p>
                </div>
              </div>

              <!-- Status & Category -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                  <label for="status" class="block text-sm font-medium text-foreground mb-1.5">
                    Статус публикации <span class="text-rose-500">*</span>
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    :class="inputClass('status')"
                  >
                    <option v-for="status in [PostStatus.DRAFT, PostStatus.PUBLISHED, PostStatus.VERIFICATION, PostStatus.REJECTED]" 
                            :key="status.value" 
                            :value="status.value">
                      {{ status.label }}
                    </option>
                  </select>
                  <p v-if="form.errors.status" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                    <DashboardIcon name="exclamation-circle" size="3" />
                    {{ form.errors.status }}
                  </p>
                </div>

                <div>
                  <label for="category_id" class="block text-sm font-medium text-foreground mb-1.5">
                    Категория
                  </label>
                  <select
                    id="category_id"
                    v-model="form.category_id"
                    :class="inputClass('category_id')"
                  >
                    <option value="">Выберите категорию</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.title }}
                    </option>
                  </select>
                  <p v-if="form.errors.category_id" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                    <DashboardIcon name="exclamation-circle" size="3" />
                    {{ form.errors.category_id }}
                  </p>
                </div>
              </div>

              <!-- Tags -->
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Теги
                </label>
                <div
                  class="min-h-[42px] flex flex-wrap items-center gap-2 p-2.5 bg-surface border border-layer-line rounded-lg focus-within:border-primary/50 focus-within:ring-2 focus-within:ring-primary/20 transition-all"
                >
                  <span
                    v-for="(tag, index) in form.tags"
                    :key="index"
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium group"
                  >
                    {{ tag }}
                    <button
                      type="button"
                      @click="removeTag(index)"
                      class="text-primary/70 hover:text-primary transition-colors"
                    >
                      <DashboardIcon name="x-mark" size="4" />
                    </button>
                  </span>
                  <input
                    ref="tagInput"
                    v-model="newTag"
                    type="text"
                    class="flex-1 min-w-[120px] bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground-2"
                    placeholder="Добавьте тег и нажмите Enter"
                    @keydown.enter.prevent="addTag"
                    @keydown.comma.prevent="addTag"
                  />
                </div>
                <p class="mt-1.5 text-xs text-muted-foreground-1">Нажмите Enter для добавления тега</p>
              </div>

              <!-- Authors -->
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Авторы
                </label>
                <div
                  class="min-h-[42px] flex flex-wrap items-center gap-2 p-2.5 bg-surface border border-layer-line rounded-lg focus-within:border-primary/50 focus-within:ring-2 focus-within:ring-primary/20 transition-all"
                >
                  <span
                    v-for="(author, index) in form.authors"
                    :key="index"
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-surface-muted text-foreground rounded-full text-sm font-medium group"
                  >
                    <DashboardIcon name="user" size="4" class="text-muted-foreground-2" />
                    {{ author }}
                    <button
                      type="button"
                      @click="removeAuthor(index)"
                      class="text-muted-foreground-1 hover:text-foreground transition-colors"
                    >
                      <DashboardIcon name="x-mark" size="4" />
                    </button>
                  </span>
                  <input
                    v-model="newAuthor"
                    type="text"
                    class="flex-1 min-w-[120px] bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground-2"
                    placeholder="Добавьте автора"
                    @keydown.enter.prevent="addAuthor"
                  />
                </div>
                <p class="mt-1.5 text-xs text-muted-foreground-1">Укажите авторов новости</p>
              </div>

              <!-- Publishing Settings -->
              <div class="border border-layer-line rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-surface/50 border-b border-line-2">
                  <div class="flex items-center gap-2">
                    <DashboardIcon name="clock" size="4" class="text-muted-foreground-1" />
                    <h3 class="text-sm font-medium text-foreground">Публикация по времени</h3>
                  </div>
                </div>
                <div class="p-4 space-y-4">
                  <div class="flex items-center gap-3">
                    <input
                      id="publish_after"
                      v-model="form.publish_setting.publish_after"
                      type="checkbox"
                      :disabled="!!form.publish_setting.publish_at"
                      class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20"
                    />
                    <label for="publish_after" class="text-sm text-foreground">
                      Включить отложенную публикацию
                    </label>
                  </div>

                  <div v-if="form.publish_setting.publish_after">
                    <label for="publish_at" class="block text-sm font-medium text-foreground mb-1.5">
                      Дата и время публикации
                    </label>
                    <input
                      id="publish_at"
                      v-model="form.publish_setting.publish_at"
                      type="datetime-local"
                      :class="inputClass('publish_setting.publish_at')"
                    />
                    <p v-if="form.errors['publish_setting.publish_at']" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                      <DashboardIcon name="exclamation-circle" size="3" />
                      {{ form.errors['publish_setting.publish_at'] }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Social Media -->
              <div class="border border-layer-line rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-surface/50 border-b border-line-2">
                  <div class="flex items-center gap-2">
                    <DashboardIcon name="share" size="4" class="text-blue-500" />
                    <h3 class="text-sm font-medium text-foreground">Публикация в соцсетях</h3>
                  </div>
                </div>
                <div class="p-4">
                  <div class="flex items-center gap-3">
                    <input
                      id="publication_vk"
                      v-model="form.publication.vk"
                      type="checkbox"
                      class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20"
                    />
                    <label for="publication_vk" class="text-sm text-foreground">
                      Опубликовать ВКонтакте
                    </label>
                  </div>
                  <p class="mt-2 text-xs text-muted-foreground-1">Новость будет автоматически опубликована в VK</p>
                </div>
              </div>
            </div>

            <!-- Tab: Content -->
            <div v-if="activeTab === 'content'" class="space-y-4">
              <ContentBuilder
                v-model="form.content"
                label="Содержание новости"
              />
            </div>

            <!-- Tab: Media -->
            <div v-if="activeTab === 'media'" class="space-y-6">
              <!-- Preview Image -->
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Главное изображение
                </label>
                <div class="space-y-3">
                  <div v-if="form.preview" class="group relative w-full max-w-md mx-auto">
                    <!-- Квадратный контейнер -->
                    <div class="aspect-square w-full max-w-[280px] mx-auto relative bg-layer border-2 border-layer-line rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:border-primary/50">
                      <img
                        :src="previewUrl"
                        alt="Preview"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                      />
                      
                      <!-- Overlay с иконкой -->
                      <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center">
                        <DashboardIcon name="photo" size="12" class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 drop-shadow-lg" />
                      </div>
                      
                      <!-- Badge -->
                      <div class="absolute top-3 left-3 px-3 py-1.5 bg-primary text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1.5">
                        <DashboardIcon name="photo" size="4" />
                        Главное
                      </div>

                      <!-- Кнопка удаления -->
                      <button
                        type="button"
                        @click="form.preview = null"
                        class="absolute top-3 right-3 p-2 bg-rose-500 text-white rounded-full opacity-0 group-hover:opacity-100 hover:bg-rose-600 hover:scale-110 transition-all duration-200 shadow-lg z-10"
                        title="Удалить изображение"
                      >
                        <DashboardIcon name="x-mark" size="4" />
                      </button>
                      
                      <!-- Нижняя плашка -->
                      <div class="absolute bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-2">
                        <span class="text-[10px] font-medium text-white uppercase tracking-wider">Нажмите для замены</span>
                      </div>
                    </div>
                    
                    <!-- Кнопка загрузки под изобраением -->
                    <button
                      type="button"
                      @click="triggerPreviewInput"
                      class="absolute -bottom-3 left-1/2 -translate-x-1/2 px-4 py-1.5 bg-primary text-white text-xs font-medium rounded-full shadow-lg hover:bg-primary-hover transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap"
                    >
                      <DashboardIcon name="arrow-path" size="4" />
                      Заменить
                    </button>
                  </div>
                  
                  <!-- Drop Zone -->
                  <div
                    v-if="!form.preview"
                    @dragover.prevent="isDraggingPreview = true"
                    @dragleave.prevent="isDraggingPreview = false"
                    @drop.prevent="handlePreviewDrop"
                    @click="triggerPreviewInput"
                    :class="[
                      'relative flex flex-col items-center justify-center px-6 py-12 border-2 border-dashed rounded-xl cursor-pointer transition-all duration-300',
                      isDraggingPreview
                        ? 'border-primary bg-primary/10 scale-[1.02]'
                        : 'border-layer-line hover:border-primary/50 hover:bg-surface'
                    ]"
                  >
                    <div class="space-y-4 text-center">
                      <!-- Иконка -->
                      <div
                        :class="[
                          'w-16 h-16 rounded-2xl flex items-center justify-center transition-all duration-300 mx-auto',
                          isDraggingPreview ? 'bg-primary/20 scale-110' : 'bg-primary/10 group-hover:scale-105'
                        ]"
                      >
                        <DashboardIcon name="cloud-arrow-up" size="8" class="text-primary" />
                      </div>
                      
                      <!-- Текст -->
                      <div>
                        <p class="text-base font-medium text-foreground">
                          <span class="text-primary font-semibold">Нажмите для выбора</span> или перетащите файл
                        </p>
                        <p class="text-xs text-muted-foreground-1 mt-2">
                          JPG, PNG, WEBP (до 20MB)
                        </p>
                      </div>
                      
                      <!-- Подсказка -->
                      <div class="flex items-center justify-center gap-2 text-[10px] text-muted-foreground-2 uppercase tracking-wider">
                        <DashboardIcon name="information-circle" size="4" />
                        Рекомендуемый размер: 1200×800px
                      </div>
                    </div>
                  </div>
                  
                  <input
                    id="previewInput"
                    ref="previewInput"
                    type="file"
                    class="hidden"
                    accept="image/jpeg,image/png,image/webp"
                    @change="handlePreviewFileSelect"
                  />
                </div>
                <p v-if="form.errors.preview" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                  <DashboardIcon name="exclamation-circle" size="3" />
                  {{ form.errors.preview }}
                </p>
                <p v-else class="mt-1.5 text-xs text-muted-foreground-1">
                  Главное изображение будет отображаться на сайте и в соцсетях
                </p>
              </div>

              <!-- Gallery -->
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Галерея изображений
                </label>
                <div class="space-y-3">
                  <div
                    @dragover.prevent="isDraggingGallery = true"
                    @dragleave.prevent="isDraggingGallery = false"
                    @drop.prevent="handleGalleryDrop"
                    @click="triggerGalleryInput"
                    :class="[
                      'relative flex justify-center px-6 pt-8 pb-10 border-2 border-dashed rounded-lg cursor-pointer transition-all',
                      isDraggingGallery ? 'border-primary bg-primary/5' : 'border-layer-line hover:border-primary/50 hover:bg-surface'
                    ]"
                  >
                    <div class="space-y-3 text-center">
                      <div class="flex justify-center">
                        <div :class="[isDraggingGallery ? 'bg-primary/20' : 'bg-primary/10', 'w-12 h-12 rounded-lg flex items-center justify-center transition-all']">
                          <DashboardIcon name="cloud-arrow-up" size="6" class="text-primary" />
                        </div>
                      </div>
                      <div>
                        <p class="text-sm font-medium text-foreground">
                          <span class="text-primary">Нажмите для выбора</span> или перетащите файлы
                        </p>
                        <p class="text-xs text-muted-foreground-1 mt-1">JPG, PNG (до 20MB каждый, макс. 200 файлов)</p>
                      </div>
                    </div>
                  </div>
                  <input
                    id="galleryInput"
                    ref="galleryInput"
                    type="file"
                    class="hidden"
                    accept="image/jpeg,image/png"
                    multiple
                    @change="handleGalleryFileSelect"
                  />

                  <!-- Gallery Grid -->
                  <div v-if="form.images && form.images.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <div
                      v-for="(img, index) in galleryUrls"
                      :key="index"
                      draggable="true"
                      @dragstart="handleGalleryDragStart($event, index)"
                      @dragover="handleGalleryDragOver"
                      @dragleave="handleGalleryDragLeave"
                      @drop="handleGalleryDropOnImage($event, index)"
                      @dragend="handleGalleryDragEnd"
                      :class="[
                        'group relative aspect-square bg-layer border-2 rounded-lg overflow-hidden shadow-sm cursor-grab transition-all duration-200',
                        draggedGalleryIndex === index ? 'opacity-50 scale-95 ring-4 ring-primary ring-offset-2' : 'hover:border-primary hover:shadow-md',
                        'active:cursor-grabbing'
                      ]"
                    >
                      <!-- Drag Handle Overlay -->
                      <div class="absolute inset-0 z-10 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition-all duration-200 pointer-events-none">
                        <DashboardIcon name="bars-4" size="8" class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 drop-shadow-lg" />
                      </div>
                      
                      <img :src="img" alt="Gallery" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" />
                      
                      <!-- Number Badge -->
                      <div class="absolute top-2 left-2 w-7 h-7 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center text-sm font-bold text-primary shadow-lg border-2 border-primary/20">
                        {{ index + 1 }}
                      </div>
                      
                      <!-- Delete Button -->
                      <button
                        type="button"
                        @click="form.images.splice(index, 1)"
                        class="absolute top-2 right-2 p-1.5 bg-rose-500 text-white rounded-full opacity-0 group-hover:opacity-100 hover:bg-rose-600 hover:scale-110 transition-all duration-200 shadow-lg z-20"
                        title="Удалить"
                      >
                        <DashboardIcon name="x-mark" size="4" />
                      </button>
                      
                      <!-- Drag Indicator -->
                      <div class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-end justify-center pb-1.5 pointer-events-none">
                        <span class="text-[10px] font-medium text-white uppercase tracking-wider">Перетащить</span>
                      </div>
                    </div>
                  </div>
                </div>
                <p v-if="form.errors.images" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                  <DashboardIcon name="exclamation-circle" size="3" />
                  {{ form.errors.images }}
                </p>
              </div>
            </div>

            <!-- Tab: Slider -->
            <div v-if="activeTab === 'slider'" class="space-y-6">
              <div class="text-center py-12">
                <DashboardIcon name="information-circle" size="16" class="text-gray-400 mx-auto mb-4" />
                <p class="text-sm text-gray-600">Управление слайдами доступно в разделе "Слайдеры" Dashboard</p>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import PostStatus from '@/Enum/PostStatus.js';
import DashboardIcon from '../Components/DashboardIcon.vue';
import ContentBuilder from '../Components/ContentBuilder/ContentBuilder.vue';

export default {
  name: 'PostForm',
  components: {
    DashboardIcon,
    ContentBuilder,
  },

  props: {
    post: {
      type: Object,
      default: null
    },
    categories: {
      type: Array,
      required: true
    },
    sliders: {
      type: Array,
      required: true
    },
    statuses: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      activeTab: 'main',
      isEdit: false,
      isSliderEnabled: false,
      activeButton: true,
      isDraggingPreview: false,
      isDraggingGallery: false,
      newTag: '',
      newAuthor: '',
      PostStatus,
      draggedGalleryIndex: null,
      form: {
        title: '',
        slug: '',
        status: 'published',
        category_id: null,
        tags: [],
        authors: [],
        content: [],
        preview: null,
        images: [],
        publish_setting: {
          publish_after: false,
          publish_at: null
        },
        publication: {
          vk: true,
          telegram: true
        },
        slide: {
          slider_id: null,
          title: '',
          content: '',
          color_theme: '#ffffff',
          image: {
            url: null,
            shading: '0.5'
          },
          settings: {
            text_position: 'left',
            link_text: 'Читать'
          },
          end_time: null
        },
        errors: {}
      },
      tabs: [
        { id: 'main', label: 'Основная информация' },
        { id: 'content', label: 'Содержание' },
        { id: 'media', label: 'Медиа' },
        { id: 'slider', label: 'Слайдер' }
      ],
      slugGenerated: false
    }
  },

  watch: {
    'form.title': function(newVal, oldVal) {
      if (newVal && !this.slugGenerated) {
        this.generateSlug();
      }
      if (!newVal) {
        this.slugGenerated = false;
      }
    }
  },

  mounted() {
    const title = this.isEdit ? `Редактирование новости - ${this.post?.title}` : 'Создание новости';
    this.SET_DOCUMENT_TITLE(title);

    // Deep linking: восстанавливаем таб из URL
    const urlParams = new URLSearchParams(window.location.search);
    const tabFromUrl = urlParams.get('tab');
    if (tabFromUrl && this.tabs.some(t => t.id === tabFromUrl)) {
      this.activeTab = tabFromUrl;
    }
  },

  created() {
    if (this.post) {
      this.isEdit = true;
      this.initializeForm();
    }
  },

  computed: {
    previewUrl() {
      if (!this.form.preview) return null;
      if (typeof this.form.preview === 'string') {
        return `/storage/${this.form.preview}`;
      }
      return URL.createObjectURL(this.form.preview);
    },

    galleryUrls() {
      return this.form.images.map(img => {
        if (typeof img === 'string') {
          return `/storage/${img}`;
        }
        return URL.createObjectURL(img);
      });
    },

    slideImageUrl() {
      if (!this.form.slide.image.url) return null;
      if (typeof this.form.slide.image.url === 'string') {
        return `/storage/${this.form.slide.image.url}`;
      }
      return URL.createObjectURL(this.form.slide.image.url);
    }
  },

  beforeUnmount() {
    // Очищаем созданные URL объекты
    if (this.form.preview && typeof this.form.preview !== 'string') {
      URL.revokeObjectURL(this.previewUrl);
    }
    this.form.images.forEach((img, index) => {
      if (typeof img !== 'string') {
        URL.revokeObjectURL(this.galleryUrls[index]);
      }
    });
  },

  methods: {
    switchTab(tabId) {
      this.activeTab = tabId;
      // Deep linking: обновляем URL без перезагрузки
      const url = new URL(window.location);
      url.searchParams.set('tab', tabId);
      window.history.replaceState({}, '', url);
    },

    initializeForm() {
      const p = this.post;
      this.form = {
        ...this.form,
        title: p.title || '',
        slug: p.slug || '',
        status: p.status?.value ?? p.status ?? 'published',
        category_id: p.category_id || null,
        tags: p.tags?.map(t => t.name) || [],
        authors: p.authors || [],
        content: p.content || [],
        preview: p.preview || null,
        images: p.images || [],
        publish_setting: p.publish_setting || { publish_after: false, publish_at: null },
        publication: p.publication || { vk: true, telegram: true },
        slide: p.slide || this.form.slide
      };

      this.isSliderEnabled = !!(p.slide && p.slide.slider_id);
    },

    inputClass(field) {
      const base = 'w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all placeholder-muted-foreground-2 ';
      return this.form.errors[field]
        ? base + 'border-rose-300 focus:ring-rose-200'
        : base;
    },

    getStatusLabel(value) {
      return PostStatus.getLabel(value) || value;
    },

    generateSlug() {
      if (!this.form.title) return;

      this.form.slug = this.GENERATE_SLUG(this.form.title);
      this.slugGenerated = true;
    },

    addTag() {
      const tag = this.newTag.trim();
      if (tag && !this.form.tags.includes(tag)) {
        this.form.tags.push(tag);
        this.newTag = '';
      }
    },

    removeTag(index) {
      this.form.tags.splice(index, 1);
    },

    addAuthor() {
      const author = this.newAuthor.trim();
      if (author && !this.form.authors.includes(author)) {
        this.form.authors.push(author);
        this.newAuthor = '';
      }
    },

    removeAuthor(index) {
      this.form.authors.splice(index, 1);
    },

    handlePreviewDrop(e) {
      const files = e.dataTransfer.files;
      if (files.length > 0 && this.isImageFile(files[0])) {
        this.form.preview = files[0];
      }
    },

    handlePreviewFileSelect(e) {
      const file = e.target.files[0];
      if (file && this.isImageFile(file)) {
        this.form.preview = file;
      }
    },

    triggerPreviewInput() {
      this.$refs.previewInput.click();
    },

    handleGalleryDrop(e) {
      const files = Array.from(e.dataTransfer.files).filter(f => this.isImageFile(f));
      files.forEach(file => {
        this.form.images.push(file);
      });
    },

    handleGalleryFileSelect(e) {
      const files = Array.from(e.target.files).filter(f => this.isImageFile(f));
      files.forEach(file => {
        this.form.images.push(file);
      });
    },

    triggerGalleryInput() {
      this.$refs.galleryInput.click();
    },

    handleGalleryDragStart(event, index) {
      this.draggedGalleryIndex = index;
      // Скрываем оригинал при перетаскивании
      requestAnimationFrame(() => {
        event.target.classList.add('opacity-30', 'scale-95');
        event.target.classList.remove('opacity-100', 'scale-100');
      });
      
      // Создаем кастомный drag image
      const dragIcon = document.createElement('div');
      dragIcon.className = 'w-20 h-20 bg-primary/10 border-2 border-primary rounded-lg flex items-center justify-center';
      dragIcon.innerHTML = '<svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>';
      event.dataTransfer.setDragImage(dragIcon, 40, 40);
      event.dataTransfer.effectAllowed = 'move';
    },

    handleGalleryDragOver(event) {
      event.preventDefault();
      event.dataTransfer.dropEffect = 'move';
      event.currentTarget.classList.add('ring-4', 'ring-primary', 'ring-offset-2', 'border-primary', 'scale-105');
      event.currentTarget.classList.remove('border-layer-line');
    },

    handleGalleryDragLeave(event) {
      event.currentTarget.classList.remove('ring-4', 'ring-primary', 'ring-offset-2', 'border-primary', 'scale-105');
      event.currentTarget.classList.add('border-layer-line');
    },

    handleGalleryDropOnImage(event, dropIndex) {
      event.preventDefault();
      event.stopPropagation();
      event.currentTarget.classList.remove('ring-4', 'ring-primary', 'ring-offset-2', 'border-primary', 'scale-105');
      event.currentTarget.classList.add('border-layer-line');

      if (this.draggedGalleryIndex === null || this.draggedGalleryIndex === dropIndex) {
        return;
      }

      // Анимация обмена
      const temp = this.form.images[this.draggedGalleryIndex];
      this.form.images[this.draggedGalleryIndex] = this.form.images[dropIndex];
      this.form.images[dropIndex] = temp;

      this.draggedGalleryIndex = null;
    },

    handleGalleryDragEnd(event) {
      event.target.classList.remove('opacity-30', 'scale-95');
      event.target.classList.add('opacity-100', 'scale-100');
      this.draggedGalleryIndex = null;
      
      // Очищаем все оставшиеся классы
      document.querySelectorAll('.ring-4').forEach(el => {
        el.classList.remove('ring-4', 'ring-primary', 'ring-offset-2', 'border-primary', 'scale-105');
        el.classList.add('border-layer-line');
      });
    },

    handleSlideImageSelect(e) {
      const file = e.target.files[0];
      if (file && this.isImageFile(file)) {
        this.form.slide.image.url = file;
      }
    },

    isImageFile(file) {
      return ['image/jpeg', 'image/png', 'image/webp'].includes(file.type);
    },

    submitForm() {
      this.form.processing = true;
      this.form.errors = {};

      const formData = new FormData();

      Object.keys(this.form).forEach(key => {
        if (key === 'errors' || key === 'processing') return;

        const value = this.form[key];
        if (typeof value === 'object' && value !== null) {
          formData.append(key, JSON.stringify(value));
        } else {
          formData.append(key, value);
        }
      });

      const url = this.isEdit
        ? route('dashboard.posts.update', this.post.id)
        : route('dashboard.posts.store');

      const method = this.isEdit ? 'POST' : 'POST';

      if (this.isEdit) {
        formData.append('_method', 'PUT');
      }

      this.$inertia.post(url, formData, {
        preserveScroll: true,
        onSuccess: () => {
          this.form.processing = false;
        },
        onError: (errors) => {
          this.form.processing = false;
          this.form.errors = errors;
        }
      });
    }
  }
}
</script>
