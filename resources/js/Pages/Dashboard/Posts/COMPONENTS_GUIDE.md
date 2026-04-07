# Posts Form Components Guide

## Структура компонентов

Posts/Form.vue (1284 строки) разбит на переиспользуемые подкомпоненты:

```
Posts/Components/
├── PostMainInfo.vue           # Заголовок, slug, статус, категория
├── PostTagsAuthors.vue        # Теги и авторы
└── PostPublishSettings.vue    # Настройки публикации и соцсети
```

## Как использовать

### 1. PostMainInfo.vue

Основная информация о посте.

```vue
<PostMainInfo
  v-model:title="form.title"
  v-model:slug="form.slug"
  v-model:status="form.status"
  v-model:category-id="form.category_id"
  :categories="categories"
  :status-options="[PostStatus.DRAFT, PostStatus.PUBLISHED, PostStatus.VERIFICATION, PostStatus.REJECTED]"
  :errors="form.errors"
  @generate-slug="generateSlug"
/>
```

**Props:**
- `title` - заголовок новости
- `slug` - URL-адрес
- `status` - статус публикации
- `categoryId` - ID категории
- `categories` - массив категорий
- `statusOptions` - массив опций статуса
- `errors` - объект ошибок валидации
- `inputClass` - CSS классы для инпутов

**Events:**
- `update:title` - обновление заголовка
- `update:slug` - обновление slug
- `update:status` - обновление статуса
- `update:categoryId` - обновление категории
- `generate-slug` - генерация slug из заголовка

---

### 2. PostTagsAuthors.vue

Управление тегами и авторами.

```vue
<PostTagsAuthors
  :tags="form.tags"
  :authors="form.authors"
  @add-tag="addTag"
  @remove-tag="removeTag"
  @add-author="addAuthor"
  @remove-author="removeAuthor"
/>
```

**Props:**
- `tags` - массив тегов
- `authors` - массив авторов

**Events:**
- `add-tag` - добавить тег
- `remove-tag` - удалить тег по индексу
- `add-author` - добавить автора
- `remove-author` - удалить автора по индексу

---

### 3. PostPublishSettings.vue

Настройки публикации и социальных сетей.

```vue
<PostPublishSettings
  v-model:publish-after="form.publish_setting.publish_after"
  v-model:publish-at="form.publish_setting.publish_at"
  v-model:publish-vk="form.publication.vk"
  :errors="form.errors"
/>
```

**Props:**
- `publishAfter` - флаг отложенной публикации
- `publishAt` - дата/время публикации
- `publishVk` - публикация ВКонтакте
- `errors` - объект ошибок валидации
- `inputClass` - CSS классы для инпутов

**Events:**
- `update:publishAfter` - обновление флага отложенной публикации
- `update:publishAt` - обновление даты/времени
- `update:publishVk` - обновление флага VK

---

## Пример интеграции в Form.vue

### BEFORE (монолитный подход):
```vue
<!-- Внутри Form.vue - 1284 строки -->
<div class="space-y-6">
  <!-- Title & Slug -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- 50+ строк markup -->
  </div>
  
  <!-- Status & Category -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- 40+ строк markup -->
  </div>
  
  <!-- Tags -->
  <div>
    <!-- 60+ строк markup -->
  </div>
  
  <!-- Authors -->
  <div>
    <!-- 60+ строк markup -->
  </div>
  
  <!-- Publishing Settings -->
  <div class="border border-layer-line rounded-lg overflow-hidden">
    <!-- 80+ строк markup -->
  </div>
  
  <!-- Social Media -->
  <div class="border border-layer-line rounded-lg overflow-hidden">
    <!-- 50+ строк markup -->
  </div>
</div>
```

### AFTER (компонентный подход):
```vue
<template>
  <div class="min-h-screen bg-background-2">
    <!-- Sticky Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-20">
      <!-- Header content -->
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <FlashMessages />
      
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Tabs Navigation -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
          <div class="border-b border-line-2">
            <!-- Tab buttons -->
          </div>

          <!-- Tab Content -->
          <div class="p-6">
            <!-- Tab: Main Info -->
            <div v-show="activeTab === 'main'" class="space-y-6">
              <PostMainInfo
                v-model:title="form.title"
                v-model:slug="form.slug"
                v-model:status="form.status"
                v-model:category-id="form.category_id"
                :categories="categories"
                :status-options="statusOptions"
                :errors="form.errors"
                @generate-slug="generateSlug"
              />
              
              <PostTagsAuthors
                :tags="form.tags"
                :authors="form.authors"
                @add-tag="addTag"
                @remove-tag="removeTag"
                @add-author="addAuthor"
                @remove-author="removeAuthor"
              />
              
              <PostPublishSettings
                v-model:publish-after="form.publish_setting.publish_after"
                v-model:publish-at="form.publish_setting.publish_at"
                v-model:publish-vk="form.publication.vk"
                :errors="form.errors"
              />
            </div>

            <!-- Tab: Content -->
            <div v-show="activeTab === 'content'">
              <!-- Content builder stays as is -->
            </div>

            <!-- Tab: Media -->
            <div v-show="activeTab === 'media'">
              <!-- Media management -->
            </div>

            <!-- Tab: Slider -->
            <div v-show="activeTab === 'slider'">
              <!-- Slider settings -->
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import PostMainInfo from './Components/PostMainInfo.vue';
import PostTagsAuthors from './Components/PostTagsAuthors.vue';
import PostPublishSettings from './Components/PostPublishSettings.vue';

export default {
  components: {
    PostMainInfo,
    PostTagsAuthors,
    PostPublishSettings
  },
  
  methods: {
    addTag(tag) {
      this.form.tags.push(tag);
    },
    removeTag(index) {
      this.form.tags.splice(index, 1);
    },
    addAuthor(author) {
      this.form.authors.push(author);
    },
    removeAuthor(index) {
      this.form.authors.splice(index, 1);
    },
    generateSlug() {
      if (!this.form.slug && this.form.title) {
        this.form.slug = this.slugify(this.form.title);
      }
    }
  }
}
</script>
```

---

## Преимущества рефакторинга

### До:
- **1284 строки** в одном файле
- Сложно искать и отлаживать
- Невозможно переиспользовать
- Долгая загрузка

### После:
- **~300-400 строк** в Form.vue (только логика)
- **3 переиспользуемых компонента** (~150 строк каждый)
- Легко тестировать изолированно
- Можно использовать в других формах (например, редактирование новостей)
- Улучшенная читаемость

---

## Следующие шаги

1. **Заменить в Form.vue** использование этих компонентов
2. **Создать дополнительные компоненты:**
   - `PostContentBuilder.vue` - конструктор контента
   - `PostMediaUploader.vue` - загрузка медиа
   - `PostSliderConfig.vue` - настройка слайдера
3. **Протестировать** все сценарии создания/редактирования
