<template>
  <div class="space-y-6">
    <!-- Tags -->
    <div>
      <label class="block text-sm font-medium text-foreground mb-1.5">
        Теги
      </label>
      <div
        class="min-h-[42px] flex flex-wrap items-center gap-2 p-2.5 bg-surface border border-layer-line rounded-lg focus-within:border-primary/50 focus-within:ring-2 focus-within:ring-primary/20 transition-all"
      >
        <span
          v-for="(tag, index) in tags"
          :key="index"
          class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium group"
        >
          {{ tag }}
          <button
            type="button"
            @click="$emit('remove-tag', index)"
            class="text-primary/70 hover:text-primary transition-colors"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
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
          v-for="(author, index) in authors"
          :key="index"
          class="inline-flex items-center gap-1.5 px-3 py-1 bg-surface-muted text-foreground rounded-full text-sm font-medium group"
        >
          <svg class="w-3.5 h-3.5 text-muted-foreground-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          {{ author }}
          <button
            type="button"
            @click="$emit('remove-author', index)"
            class="text-muted-foreground-1 hover:text-foreground transition-colors"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </span>
        <input
          v-model="newAuthor"
          type="text"
          class="flex-1 min-w-[120px] bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground-2"
          placeholder="Добавьте автора"
          @keydown.enter.prevent="$emit('add-author')"
        />
      </div>
      <p class="mt-1.5 text-xs text-muted-foreground-1">Укажите авторов новости</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PostTagsAuthors',
  props: {
    tags: {
      type: Array,
      default: () => []
    },
    authors: {
      type: Array,
      default: () => []
    }
  },
  emits: ['add-tag', 'remove-tag', 'add-author', 'remove-author'],
  data() {
    return {
      newTag: '',
      newAuthor: ''
    }
  },
  methods: {
    addTag() {
      if (this.newTag.trim()) {
        this.$emit('add-tag', this.newTag.trim());
        this.newTag = '';
      }
    },
    addAuthor() {
      if (this.newAuthor.trim()) {
        this.$emit('add-author', this.newAuthor.trim());
        this.newAuthor = '';
      }
    }
  }
}
</script>
