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
              <DashboardIcon name="pencil-square" size="5" class="text-primary" />
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground">Редактирование списка ресурсов</h1>
              <p class="text-xs text-muted-foreground-1">{{ list.title }}</p>
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
          <div v-if="activeTab === 'main'" class="space-y-6">
            <div>
              <label for="title" class="block text-sm font-medium text-foreground mb-2">Название ресурса <span class="text-rose-500">*</span></label>
              <input id="title" v-model="data.title" type="text" @input="generateSlug" :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm', errors.title ? 'border-rose-500' : 'border-layer-line focus:border-primary']" />
              <p v-if="errors.title" class="mt-1.5 text-xs text-rose-500">{{ errors.title }}</p>
            </div>
            <div>
              <label for="slug" class="block text-sm font-medium text-foreground mb-2">URL-адрес (Slug) <span class="text-rose-500">*</span></label>
              <input id="slug" v-model="data.slug" type="text" :class="['w-full px-4 py-2.5 bg-surface border rounded-lg text-sm', errors.slug ? 'border-rose-500' : 'border-layer-line focus:border-primary']" />
              <p v-if="errors.slug" class="mt-1.5 text-xs text-rose-500">{{ errors.slug }}</p>
            </div>
            <div class="flex items-center gap-3">
              <input id="is_active" v-model="data.is_active" type="checkbox" class="h-4 w-4 rounded border-layer-line text-primary focus:ring-primary" />
              <label for="is_active" class="text-sm font-medium text-foreground">Активность ресурса</label>
            </div>
          </div>

          <div v-if="activeTab === 'content'" class="space-y-6">
            <div class="flex items-center justify-between">
              <label class="block text-sm font-medium text-foreground">Элементы ресурса <span class="text-rose-500">*</span></label>
              <button type="button" @click="addItem" class="text-sm text-primary hover:text-primary-hover">+ Добавить элемент</button>
            </div>

            <div v-for="(item, index) in data.content" :key="item._uid || index" class="border border-layer-line rounded-lg overflow-hidden">
              <div class="flex items-center gap-2 px-4 py-3 bg-muted/30 border-b border-layer-line">
                <input v-model="item.title" type="text" required maxlength="255" placeholder="Заголовок элемента" class="flex-1 px-3 py-1.5 border border-layer-line rounded-lg bg-white text-foreground text-sm" />
                <button type="button" @click="removeItem(index)" class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all" :disabled="data.content.length <= 1" title="Удалить">
                  <DashboardIcon name="trash" size="4" />
                </button>
              </div>

              <div class="p-4 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Тип данных</label>
                    <select v-model="item.model_select" class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm">
                      <option value="">Выберите тип</option>
                      <option value="Post">Новость</option>
                      <option value="Page">Страница</option>
                      <option value="Event">Мероприятие</option>
                      <option value="Custom">Кастомная ссылка</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Выбор элемента</label>
                    <select v-model="item.model" :disabled="!item.model_select || item.model_select === 'Custom'" class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm disabled:bg-muted disabled:cursor-not-allowed">
                      <option value="">Выберите элемент</option>
                    </select>
                  </div>
                </div>

                <div class="flex items-center gap-4">
                  <label class="text-xs font-medium text-foreground">Графическое представление:</label>
                  <label class="flex items-center gap-1.5 text-sm">
                    <input type="radio" v-model="item.visual_type" value="image" class="text-primary focus:ring-primary" /> Изображение
                  </label>
                  <label class="flex items-center gap-1.5 text-sm">
                    <input type="radio" v-model="item.visual_type" value="icon" class="text-primary focus:ring-primary" /> Иконка
                  </label>
                </div>

                <div v-if="item.visual_type === 'image'">
                  <label class="block text-xs font-medium text-foreground mb-1">Изображение</label>
                  <input type="file" @change="handleImageUpload(index, $event)" accept="image/*" class="w-full text-sm" />
                  <img v-if="item.image && typeof item.image === 'string'" :src="RESOLVE_ASSET_URL(item.image)" class="mt-2 h-16 w-16 object-cover rounded" />
                </div>

                <div v-if="item.visual_type === 'icon'">
                  <label class="block text-xs font-medium text-foreground mb-1">Иконка</label>
                  <input v-model="item.icon" type="text" placeholder="heroicon-o-academic-cap" class="w-full px-3 py-2 border border-layer-line rounded-lg text-sm" />
                </div>

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

            <div v-if="data.content.length === 0" class="text-center py-8 text-muted-foreground-1 text-sm">
              Нет элементов. Добавьте первый элемент ресурса.
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-line-2">
            <a :href="route('dashboard.page-reference-lists.index')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-muted-hover transition-all">Отмена</a>
            <button type="submit" :disabled="processing" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed">
              <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" /></svg>
              <DashboardIcon v-else name="check" size="4" />
              {{ processing ? 'Сохранение...' : 'Сохранить' }}
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
  name: 'PageReferenceListsEdit',
  components: { DashboardIcon, FlashMessages },
  props: { list: { type: Object, required: true } },

  data() {
    return {
      activeTab: 'main',
      tabs: [
        { key: 'main', label: 'Основная информация', icon: 'information-circle' },
        { key: 'content', label: 'Содержание ресурса', icon: 'document-text' },
      ],
      data: {
        title: this.list.title,
        slug: this.list.slug,
        is_active: this.list.is_active === 1 || this.list.is_active === true || this.list.is_active === '1',
        content: this.normalizeContent(this.list.content || [])
      },
      errors: {},
      processing: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Редактирование: ${this.list.title}`);
  },

  methods: {
    normalizeContent(content) {
      return content.map(item => {
        // Если image — объект (из Filament), извлекаем URL
        let imageUrl = item.image || null;
        if (typeof imageUrl === 'object' && imageUrl !== null) {
          imageUrl = imageUrl.url || imageUrl.path || null;
        }

        return {
          ...item,
          _uid: item._uid || `item-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`,
          visual_type: item.visual_type || (imageUrl ? 'image' : (item.icon ? 'icon' : 'image')),
          image: imageUrl,
          icon: item.icon || 'heroicon-o-academic-cap'
        };
      });
    },

    generateSlug() {
      if (!this.data.slug || this.data.slug === this.list.slug) {
        this.data.slug = this.GENERATE_SLUG(this.data.title);
      }
    },

    addItem() {
      this.data.content.push({
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
      if (this.data.content.length > 1) {
        this.data.content.splice(index, 1);
      }
    },

    handleImageUpload(index, event) {
      const file = event.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = (e) => {
        if (e.target && typeof e.target.result === 'string') {
          this.$set ? this.$set(this.data.content[index], 'image', e.target.result) : (this.data.content[index].image = e.target.result);
        }
      };
      reader.readAsDataURL(file);
    },

    submit() {
      this.processing = true;
      this.errors = {};

      const payload = {
        ...this.data,
        content: this.data.content.map(({ _uid, ...item }) => item)
      };

      this.$inertia.put(route('dashboard.page-reference-lists.update', this.list.id), payload, {
        onFinish: () => { this.processing = false; },
        onError: (errors) => { this.errors = errors; }
      });
    }
  }
}
</script>
