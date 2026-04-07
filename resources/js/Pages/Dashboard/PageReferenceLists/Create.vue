<template>
  <div class="min-h-screen bg-background-2">
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10 h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center h-full gap-3">
          <a :href="route('dashboard.page-reference-lists.index')" class="p-2 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all">
            <DashboardIcon name="arrow-left" size="5" />
          </a>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <DashboardIcon name="plus" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Создание списка ресурсов</h1>
              <p class="text-xs text-muted-foreground-1">Заполните информацию о новом списке</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <FlashMessages />

      <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
        <div class="border-b border-line-2">
          <nav class="flex -mb-px">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              @click="activeTab = tab.key"
              class="px-6 py-4 text-sm font-medium border-b-2 transition-all"
              :class="activeTab === tab.key ? 'border-primary text-primary' : 'border-transparent text-muted-foreground-1 hover:text-foreground hover:border-line-2'"
            >
              <div class="flex items-center gap-2">
                <DashboardIcon :name="tab.icon" size="4" />
                {{ tab.label }}
              </div>
            </button>
          </nav>
        </div>

        <form @submit.prevent="submit" class="p-6">
          <!-- Tab: Main Info -->
          <div v-if="activeTab === 'main'" class="space-y-6">
            <div>
              <label for="title" class="block text-sm font-medium text-foreground mb-2">Название ресурса <span class="text-rose-500">*</span></label>
              <input id="title" v-model="form.title" type="text" @input="generateSlug" placeholder="Например: Полезные ссылки" :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm', errors.title ? 'border-rose-500' : 'border-layer-line focus:border-primary']" />
              <p v-if="errors.title" class="mt-1.5 text-xs text-rose-500">{{ errors.title }}</p>
            </div>

            <div>
              <label for="slug" class="block text-sm font-medium text-foreground mb-2">URL-адрес (Slug) <span class="text-rose-500">*</span></label>
              <input id="slug" v-model="form.slug" type="text" placeholder="useful-links" :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm', errors.slug ? 'border-rose-500' : 'border-layer-line focus:border-primary']" />
              <p v-if="errors.slug" class="mt-1.5 text-xs text-rose-500">{{ errors.slug }}</p>
            </div>

            <div class="flex items-center gap-3">
              <input id="is_active" v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary" />
              <label for="is_active" class="text-sm font-medium text-foreground">Активность ресурса</label>
            </div>
          </div>

          <!-- Tab: Content -->
          <div v-if="activeTab === 'content'" class="space-y-6">
            <div class="flex items-center justify-between">
              <label class="block text-sm font-medium text-foreground">Элементы ресурса <span class="text-rose-500">*</span></label>
              <button type="button" @click="addItem" class="text-sm text-primary hover:text-primary-hover">+ Добавить элемент</button>
            </div>

            <div
              v-for="(item, index) in form.content"
              :key="item._uid || index"
              class="border border-layer-line rounded-lg overflow-hidden"
            >
              <div class="flex items-center gap-2 px-4 py-3 bg-muted/30 border-b border-layer-line">
                <input
                  v-model="item.title"
                  type="text"
                  required
                  maxlength="255"
                  placeholder="Заголовок элемента"
                  class="flex-1 px-3 py-1.5 border border-layer-line rounded-lg bg-white text-foreground text-sm"
                />
                <button type="button" @click="removeItem(index)" class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all" :disabled="form.content.length <= 1" title="Удалить">
                  <DashboardIcon name="trash" size="4" />
                </button>
              </div>

              <div class="p-4 space-y-4">
                <!-- Model Select -->
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Тип данных</label>
                    <select v-model="item.model_select" @change="onModelSelectChange(index)" class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm">
                      <option value="">Выберите тип</option>
                      <option value="Post">Новость</option>
                      <option value="Page">Страница</option>
                      <option value="Event">Мероприятие</option>
                      <option value="Custom">Кастомная ссылка</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Выбор элемента</label>
                    <select
                      v-model="item.model"
                      @change="onModelChange(index)"
                      :disabled="!item.model_select || item.model_select === 'Custom'"
                      class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm disabled:bg-muted disabled:cursor-not-allowed"
                    >
                      <option value="">Выберите элемент</option>
                      <option v-for="(label, id) in getModelOptions(item.model_select)" :key="id" :value="id">{{ label }}</option>
                    </select>
                  </div>
                </div>

                <!-- Visual Type -->
                <div class="flex items-center gap-4">
                  <label class="text-xs font-medium text-foreground">Графическое представление:</label>
                  <label class="flex items-center gap-1.5 text-sm">
                    <input type="radio" v-model="item.visual_type" value="image" class="text-primary focus:ring-primary" />
                    Изображение
                  </label>
                  <label class="flex items-center gap-1.5 text-sm">
                    <input type="radio" v-model="item.visual_type" value="icon" class="text-primary focus:ring-primary" />
                    Иконка
                  </label>
                </div>

                <!-- Image Upload -->
                <div v-if="item.visual_type === 'image'">
                  <label class="block text-xs font-medium text-foreground mb-1">Изображение</label>
                  <input type="file" @change="handleImageUpload(index, $event)" accept="image/*" class="w-full text-sm" />
                  <img v-if="item.image && typeof item.image === 'string'" :src="RESOLVE_ASSET_URL(item.image)" class="mt-2 h-16 w-16 object-cover rounded" />
                </div>

                <!-- Icon Picker -->
                <div v-if="item.visual_type === 'icon'">
                  <label class="block text-xs font-medium text-foreground mb-1">Иконка</label>
                  <input v-model="item.icon" type="text" placeholder="heroicon-o-academic-cap" class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm" />
                </div>

                <!-- Link & Button Text -->
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Ссылка <span class="text-rose-500">*</span></label>
                    <input v-model="item.link" type="text" required placeholder="/path или https://..." class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm" />
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Текст кнопки <span class="text-rose-500">*</span></label>
                    <input v-model="item.link_text" type="text" required maxlength="50" placeholder="Читать" class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm" />
                  </div>
                </div>
              </div>
            </div>

            <div v-if="form.content.length === 0" class="text-center py-8 text-muted-foreground-1 text-sm">
              Нет элементов. Добавьте первый элемент ресурса.
            </div>
          </div>

          <!-- Submit -->
          <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
            <a :href="route('dashboard.page-reference-lists.index')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all">Отмена</a>
            <button type="submit" :disabled="processing" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed">
              <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" /></svg>
              <DashboardIcon v-else name="check" size="4" />
              {{ processing ? 'Сохранение...' : 'Создать список' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

export default {
  name: 'PageReferenceListsCreate',
  components: { DashboardIcon, FlashMessages },

  data() {
    return {
      activeTab: 'main',
      tabs: [
        { key: 'main', label: 'Основная информация', icon: 'information-circle' },
        { key: 'content', label: 'Содержание ресурса', icon: 'document-text' },
      ],
      form: {
        title: '',
        slug: '',
        is_active: true,
        content: []
      },
      errors: {},
      processing: false,
      modelOptions: {
        Post: [],
        Page: [],
        Event: []
      }
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Создание списка ресурсов');
    this.loadModelOptions();
  },

  methods: {
    async loadModelOptions() {
      // Загружаем опции для селекторов моделей
      try {
        const [posts, pages] = await Promise.all([
          fetch('/api/options/posts').then(r => r.json()).catch(() => []),
          fetch('/api/options/pages').then(r => r.json()).catch(() => [])
        ]);
        this.modelOptions.Post = posts;
        this.modelOptions.Page = pages;
      } catch (e) {
        // Опции загрузятся при выборе типа
      }
    },

    generateSlug() {
      if (!this.form.slug) {
        this.form.slug = this.GENERATE_SLUG(this.form.title);
      }
    },

    addItem() {
      this.form.content.push({
        _uid: `item-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`,
        model_select: '',
        model: null,
        title: '',
        visual_type: 'image',
        image: null,
        icon: 'heroicon-o-academic-cap',
        link: '',
        link_text: 'Читать'
      });
    },

    removeItem(index) {
      if (this.form.content.length > 1) {
        this.form.content.splice(index, 1);
      }
    },

    onModelSelectChange(index) {
      const item = this.form.content[index];
      if (item.model_select === 'Custom') {
        item.model = null;
        item.title = '';
        item.link = '';
      }
    },

    onModelChange(index) {
      const item = this.form.content[index];
      // Автозаполнение title и link при выборе элемента
      // Данные подставляются из modelOptions
    },

    getModelOptions(type) {
      return this.modelOptions[type] || {};
    },

    handleImageUpload(index, event) {
      const file = event.target.files[0];
      if (!file) return;

      const error = this.VALIDATE_PDF ? null : null; // Для изображений нужна отдельная валидация
      const reader = new FileReader();
      reader.onload = (e) => {
        this.form.content[index].image = e.target.result;
      };
      reader.readAsDataURL(file);
    },

    submit() {
      this.processing = true;
      this.errors = {};

      // Очищаем _uid перед отправкой
      const payload = {
        ...this.form,
        content: this.form.content.map(({ _uid, ...item }) => item)
      };

      this.$inertia.post(route('dashboard.page-reference-lists.store'), payload, {
        onFinish: () => { this.processing = false; },
        onError: (errors) => { this.errors = errors; }
      });
    }
  }
}
</script>
