<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="photo" size="5" class="text-primary" />
    </template>
    <template #header-title>Редактирование слайдера</template>
    <template #header-subtitle>{{ slider.title }}</template>
    <template #header-actions>
      <a
        :href="route('dashboard.sliders.index')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all"
      >
        <DashboardIcon name="arrow-left" size="4" />
        Назад
      </a>
    </template>

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
          <DashboardIcon name="check" size="5" class="text-emerald-600 flex-shrink-0 mt-0.5" />
          <span class="text-sm text-foreground font-medium">{{ $page.props.flash.success }}</span>
        </div>
      </div>
    </transition>

    <div class="space-y-6">
      <!-- Slider Settings -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-line-2">
          <h3 class="text-base font-semibold text-foreground">Настройки слайдера</h3>
        </div>
        <form @submit.prevent="updateSlider" class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="title" class="block text-sm font-medium text-foreground mb-1.5">
                Название слайдера *
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                required
                class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
              />
            </div>
            <div>
              <label for="slug" class="block text-sm font-medium text-foreground mb-1.5">
                URL-идентификатор *
              </label>
              <input
                id="slug"
                v-model="form.slug"
                type="text"
                required
                class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
              />
            </div>
          </div>

          <div class="flex items-center gap-3">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20"
            />
            <label for="is_active" class="text-sm font-medium text-foreground">
              Активный слайдер
            </label>
          </div>

          <div class="pt-4">
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Сохранить
            </button>
          </div>
        </form>
      </div>

      <!-- Slides Section -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-line-2 flex items-center justify-between">
          <h3 class="text-base font-semibold text-foreground">Слайды</h3>
          <button
            @click="showAddSlide = true"
            class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all"
          >
            <DashboardIcon name="plus" size="4" />
            Добавить слайд
          </button>
        </div>

        <!-- Add Slide Form -->
        <div v-if="showAddSlide" class="p-6 border-b border-line-2 bg-surface/50 space-y-6">
          <h4 class="text-sm font-semibold text-foreground">
            {{ editingSlideId ? 'Редактировать слайд' : 'Новый слайд' }}
          </h4>
          
          <!-- Quick Setup -->
          <div class="border border-layer-line rounded-lg p-4 space-y-4">
            <h5 class="text-sm font-medium text-foreground">Быстрая настройка слайда</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Тип контента
                </label>
                <select
                  v-model="slideForm.model_select"
                  @change="onModelSelectChange"
                  class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                >
                  <option value="Custom">Кастомная ссылка</option>
                  <option value="Post">Новость</option>
                </select>
              </div>
              <div v-if="slideForm.model_select === 'Post'">
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Выбор элемента
                </label>
                <select
                  v-model="slideForm.model"
                  @change="onModelChange"
                  class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                >
                  <option value="">Выберите новость</option>
                  <option v-for="post in posts" :key="post.id" :value="post.id">{{ post.title }}</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Text Content -->
          <div class="border border-layer-line rounded-lg p-4 space-y-4">
            <h5 class="text-sm font-medium text-foreground">Текстовая часть</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Заголовок слайда
                </label>
                <input
                  v-model="slideForm.title"
                  type="text"
                  maxlength="255"
                  class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Цвет текста *
                </label>
                <div class="flex items-center gap-2">
                  <input
                    v-model="slideForm.color_theme"
                    type="color"
                    class="w-10 h-10 rounded border border-layer-line cursor-pointer"
                  />
                  <span class="text-sm font-mono text-muted-foreground-1">{{ slideForm.color_theme }}</span>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-foreground mb-1.5">
                Описание слайда
              </label>
              <textarea
                v-model="slideForm.content"
                rows="2"
                maxlength="1000"
                class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Позиция текста
                </label>
                <div class="flex gap-2">
                  <button
                    v-for="pos in ['left', 'center', 'right']"
                    :key="pos"
                    type="button"
                    @click="slideForm.settings.text_position = pos"
                    :class="[
                      'flex-1 px-3 py-2 text-sm font-medium rounded-lg border transition-all',
                      slideForm.settings.text_position === pos
                        ? 'bg-primary text-white border-primary'
                        : 'bg-surface text-foreground border-layer-line hover:border-primary/50'
                    ]"
                  >
                    {{ pos === 'left' ? 'Слева' : pos === 'center' ? 'По центру' : 'Справа' }}
                  </button>
                </div>
              </div>
              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <input
                    id="active_button"
                    v-model="slideForm.active_button"
                    type="checkbox"
                    class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20"
                  />
                  <label for="active_button" class="text-sm font-medium text-foreground">
                    Показывать кнопку
                  </label>
                </div>
                <input
                  v-if="slideForm.active_button"
                  v-model="slideForm.settings.link_text"
                  type="text"
                  maxlength="50"
                  placeholder="Текст кнопки"
                  class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
              </div>
            </div>
          </div>

          <!-- Image -->
          <div class="border border-layer-line rounded-lg p-4 space-y-4">
            <h5 class="text-sm font-medium text-foreground">Изображение</h5>
            <div>
              <label class="block text-sm font-medium text-foreground mb-1.5">
                Изображение слайда *
              </label>
              <div v-if="slideForm.image_preview" class="mb-3">
                <img :src="slideForm.image_preview" alt="Preview" class="h-48 w-auto object-cover rounded-lg border border-layer-line shadow-sm" />
              </div>
              <input
                type="file"
                @change="handleImageUpload"
                accept="image/*"
                class="w-full text-sm text-foreground file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all"
              />
              <p class="mt-1.5 text-xs text-muted-foreground-1">Рекомендуемое соотношение сторон: 16:9</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-foreground mb-1.5">
                Затемнение фона
              </label>
              <div class="flex gap-2 flex-wrap">
                <button
                  v-for="opt in [{value: '1', label: 'Нет'}, {value: '0.7', label: 'Слабое'}, {value: '0.5', label: 'Среднее'}, {value: '0.3', label: 'Сильное'}]"
                  :key="opt.value"
                  type="button"
                  @click="slideForm.image_shading = opt.value"
                  :class="[
                    'px-4 py-2 text-sm font-medium rounded-lg border transition-all',
                    slideForm.image_shading === opt.value
                      ? 'bg-primary text-white border-primary'
                      : 'bg-surface text-foreground border-layer-line hover:border-primary/50'
                  ]"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>
          </div>

          <!-- Display Settings -->
          <div class="border border-layer-line rounded-lg p-4 space-y-4">
            <h5 class="text-sm font-medium text-foreground">Настройки отображения</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Целевая ссылка *
                </label>
                <input
                  v-model="slideForm.link"
                  type="text"
                  maxlength="255"
                  required
                  class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
              </div>
              <div class="flex items-center gap-3 pt-6">
                <input
                  id="slide_is_active"
                  v-model="slideForm.is_active"
                  type="checkbox"
                  class="w-4 h-4 text-primary border-layer-line rounded focus:ring-primary/20"
                />
                <label for="slide_is_active" class="text-sm font-medium text-foreground">
                  Активный слайд
                </label>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Дата начала показа
                </label>
                <input
                  v-model="slideForm.start_time"
                  type="datetime-local"
                  class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">
                  Дата окончания показа
                </label>
                <input
                  v-model="slideForm.end_time"
                  type="datetime-local"
                  class="w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
              </div>
            </div>
          </div>

          <div class="flex gap-2 pt-2">
            <button
              type="button"
              @click="createSlide"
              :disabled="slideForm.processing"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="slideForm.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ editingSlideId ? 'Сохранить изменения' : 'Создать слайд' }}
            </button>
            <button
              type="button"
              @click="showAddSlide = false; editingSlideId = null; resetSlideForm()"
              class="px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-colors"
            >
              Отмена
            </button>
          </div>
        </div>

        <!-- Slides List -->
        <div class="divide-y divide-line-2">
          <div v-for="slide in slides" :key="slide.id" class="p-4 hover:bg-muted-hover transition-colors">
            <div class="flex items-start gap-4">
              <div class="w-20 h-12 rounded overflow-hidden border border-layer-line flex-shrink-0">
                <img v-if="slide.image?.url" :src="getImageUrl(slide.image.url)" :alt="slide.title" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                  <DashboardIcon name="photo" size="6" class="text-gray-400" />
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <span class="text-xs font-medium text-muted-foreground-1">#{{ slide.sort }}</span>
                  <h5 class="text-sm font-semibold text-foreground truncate">{{ slide.title || 'Без заголовка' }}</h5>
                  <span :class="slide.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                    {{ slide.is_active ? 'Активен' : 'Неактивен' }}
                  </span>
                </div>
                <p v-if="slide.content" class="text-xs text-muted-foreground-1 line-clamp-1 mb-1">{{ slide.content }}</p>
                <div class="flex items-center gap-3 text-xs text-muted-foreground-1">
                  <span class="truncate">{{ slide.link }}</span>
                  <span v-if="slide.start_time" class="flex-shrink-0">с {{ new Date(slide.start_time).toLocaleDateString('ru-RU') }}</span>
                  <span v-if="slide.end_time" class="flex-shrink-0">по {{ new Date(slide.end_time).toLocaleDateString('ru-RU') }}</span>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="editSlide(slide)"
                  class="p-2 text-blue-600 hover:bg-blue-50 rounded transition-colors"
                  title="Редактировать"
                >
                  <DashboardIcon name="pencil-square" size="4" />
                </button>
                <button
                  @click="toggleSlideActive(slide)"
                  class="p-2 text-blue-600 hover:bg-blue-50 rounded transition-colors"
                  :title="slide.is_active ? 'Деактивировать' : 'Активировать'"
                >
                  <DashboardIcon name="arrows-up-down" size="4" />
                </button>
                <button
                  @click="deleteSlide(slide)"
                  class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors"
                  title="Удалить"
                >
                  <DashboardIcon name="trash" size="4" />
                </button>
              </div>
            </div>
          </div>
          <div v-if="slides.length === 0 && !showAddSlide" class="p-12 text-center">
            <DashboardIcon name="photo" size="12" class="text-gray-400 mx-auto mb-4" />
            <p class="text-sm text-muted-foreground-1">Слайды еще не добавлены</p>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';

export default {
  name: 'EditSlider',
  components: {
    DashboardLayout,
    DashboardIcon
  },
  props: {
    slider: {
      type: Object,
      required: true
    },
    slides: {
      type: Array,
      required: true
    },
    posts: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      form: {
        title: this.slider.title,
        slug: this.slider.slug,
        is_active: this.slider.is_active ?? true,
        processing: false
      },
      slideForm: {
        model_select: 'Custom',
        model: null,
        title: '',
        content: '',
        image: null,
        image_preview: null,
        link: '',
        color_theme: '#ffffff',
        settings: {
          text_position: 'left',
          link_text: 'Читать'
        },
        image_shading: '0.5',
        active_button: true,
        is_active: true,
        start_time: this.formatDateTime(new Date()),
        end_time: this.formatDateTime(new Date(Date.now() + 7 * 24 * 60 * 60 * 1000)),
        processing: false
      },
      showAddSlide: false,
      editingSlideId: null
    }
  },
  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование слайдера - ${this.slider.title}`);
  },
  methods: {
    getImageUrl(path) {
      if (!path) return null
      // Если уже полный URL, возвращаем как есть
      if (path.startsWith('http')) return path
      // Добавляем /storage/ для относительных путей
      return '/storage/' + path
    },
    formatDateTime(date) {
      const year = date.getFullYear()
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const day = String(date.getDate()).padStart(2, '0')
      const hours = String(date.getHours()).padStart(2, '0')
      const minutes = String(date.getMinutes()).padStart(2, '0')
      return `${year}-${month}-${day}T${hours}:${minutes}`
    },
    updateSlider() {
      this.form.processing = true
      this.$inertia.put(route('dashboard.sliders.update', this.slider.id), this.form, {
        onSuccess: () => {
          this.form.processing = false
        },
        onError: () => {
          this.form.processing = false
        }
      })
    },
    onModelSelectChange() {
      if (this.slideForm.model_select === 'Custom') {
        this.slideForm.model = null
        this.slideForm.title = ''
        this.slideForm.content = ''
        this.slideForm.link = ''
      }
    },
    onModelChange() {
      const post = this.posts.find(p => p.id == this.slideForm.model)
      if (post) {
        this.slideForm.title = post.title
        this.slideForm.link = `/news/${post.slug}`
      }
    },
    handleImageUpload(event) {
      const file = event.target.files[0]
      if (file) {
        this.slideForm.image = file
        const reader = new FileReader()
        reader.onload = (e) => {
          this.slideForm.image_preview = e.target.result
        }
        reader.readAsDataURL(file)
      }
    },
    createSlide() {
      this.slideForm.processing = true
      const formData = new FormData()
      
      // Добавляем файл
      if (this.slideForm.image) {
        formData.append('image', this.slideForm.image)
      }
      
      // Добавляем shading к изображению
      formData.append('image_shading', this.slideForm.image_shading)
      formData.append('active_button', this.slideForm.active_button ? 1 : 0)

      // Добавляем остальные поля
      Object.keys(this.slideForm).forEach(key => {
        if (key === 'processing' || key === 'image' || key === 'image_preview' || key === 'model_select' || key === 'model' || key === 'image_shading' || key === 'active_button') return
        if (key === 'settings') {
          Object.keys(this.slideForm.settings).forEach(settingKey => {
            formData.append(`settings[${settingKey}]`, this.slideForm.settings[settingKey])
          })
        } else if (this.slideForm[key] !== null && this.slideForm[key] !== undefined) {
          formData.append(key, this.slideForm[key])
        }
      })

      const url = this.editingSlideId 
        ? route('dashboard.slides.update', this.editingSlideId)
        : route('dashboard.slides.store', this.slider.id)
      
      const method = this.editingSlideId ? 'post' : 'post'
      
      // Для обновления добавляем _method
      if (this.editingSlideId) {
        formData.append('_method', 'PUT')
      }

      axios.post(url, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      .then(response => {
        this.showAddSlide = false
        this.editingSlideId = null
        this.resetSlideForm()
        window.location.reload()
      })
      .catch(error => {
        console.error('Error creating slide:', error)
        this.slideForm.processing = false
        if (error.response?.data?.errors) {
          alert('Ошибка валидации: ' + JSON.stringify(error.response.data.errors))
        }
      })
    },
    editSlide(slide) {
      this.editingSlideId = slide.id
      this.showAddSlide = true
      this.slideForm = {
        model_select: 'Custom',
        model: null,
        title: slide.title || '',
        content: slide.content || '',
        image: null,
        image_preview: slide.image?.url ? this.getImageUrl(slide.image.url) : null,
        link: slide.link || '',
        color_theme: slide.color_theme || '#ffffff',
        settings: {
          text_position: slide.settings?.text_position || 'left',
          link_text: slide.settings?.link_text || 'Читать'
        },
        image_shading: slide.image?.shading || '0.5',
        active_button: slide.settings?.active_button ?? true,
        is_active: slide.is_active ?? true,
        start_time: slide.start_time ? this.formatDateTime(new Date(slide.start_time)) : this.formatDateTime(new Date()),
        end_time: slide.end_time ? this.formatDateTime(new Date(slide.end_time)) : this.formatDateTime(new Date(Date.now() + 7 * 24 * 60 * 60 * 1000)),
        processing: false
      }
    },
    resetSlideForm() {
      this.editingSlideId = null
      this.slideForm = {
        model_select: 'Custom',
        model: null,
        title: '',
        content: '',
        image: null,
        image_preview: null,
        link: '',
        color_theme: '#ffffff',
        settings: {
          text_position: 'left',
          link_text: 'Читать'
        },
        image_shading: '0.5',
        active_button: true,
        is_active: true,
        start_time: this.formatDateTime(new Date()),
        end_time: this.formatDateTime(new Date(Date.now() + 7 * 24 * 60 * 60 * 1000)),
        processing: false
      }
    },
    toggleSlideActive(slide) {
      axios.put(route('dashboard.slides.update', slide.id), {
        is_active: slide.is_active ? 0 : 1
      })
      .then(() => {
        window.location.reload()
      })
    },
    deleteSlide(slide) {
      if (!confirm('Удалить этот слайд?')) return

      axios.delete(route('dashboard.slides.destroy', slide.id))
        .then(() => {
          window.location.reload()
        })
    }
  }
}
</script>
