<template>
  <div class="space-y-6">
    <!-- Title & Slug -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div>
        <label for="title" class="block text-sm font-medium text-foreground mb-1.5">
          Заголовок <span class="text-rose-500">*</span>
        </label>
        <input
          id="title"
          :value="title"
          @input="$emit('update:title', $event.target.value)"
          @blur="$emit('generate-slug')"
          type="text"
          :class="inputClass"
          placeholder="Введите заголовок новости"
        />
        <p v-if="errors.title" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          {{ errors.title }}
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
            :value="slug"
            @input="$emit('update:slug', $event.target.value)"
            type="text"
            :class="inputClass"
            placeholder="url-novosti"
            class="font-mono flex-1"
            readonly
          />
        </div>
        <p v-if="errors.slug" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          {{ errors.slug }}
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
          :value="status"
          @change="$emit('update:status', $event.target.value)"
          :class="inputClass"
        >
          <option v-for="statusOption in statusOptions"
                  :key="statusOption.value"
                  :value="statusOption.value">
            {{ statusOption.label }}
          </option>
        </select>
        <p v-if="errors.status" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          {{ errors.status }}
        </p>
      </div>

      <div>
        <label for="category_id" class="block text-sm font-medium text-foreground mb-1.5">
          Категория
        </label>
        <select
          id="category_id"
          :value="categoryId"
          @change="$emit('update:categoryId', $event.target.value)"
          :class="inputClass"
        >
          <option value="">Выберите категорию</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.title }}
          </option>
        </select>
        <p v-if="errors.category_id" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          {{ errors.category_id }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PostMainInfo',
  props: {
    title: {
      type: String,
      default: ''
    },
    slug: {
      type: String,
      default: ''
    },
    status: {
      type: String,
      default: ''
    },
    categoryId: {
      type: [String, Number],
      default: ''
    },
    categories: {
      type: Array,
      default: () => []
    },
    statusOptions: {
      type: Array,
      default: () => []
    },
    errors: {
      type: Object,
      default: () => ({})
    },
    inputClass: {
      type: String,
      default: 'w-full px-3 py-2 bg-surface border border-layer-line rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all'
    }
  },
  emits: ['update:title', 'update:slug', 'update:status', 'update:categoryId', 'generate-slug']
}
</script>
