<template>
  <div class="min-h-screen bg-background-2">
    <!-- Sticky Header -->
    <div class="border-b border-line-2 bg-layer/50 backdrop-blur-sm sticky top-0 z-10">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-3">
            <a
              :href="route('dashboard.posts.index')"
              class="inline-flex items-center gap-2 px-3 py-2 text-sm text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded-lg transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Назад
            </a>
            <div class="w-px h-6 bg-line-2"></div>
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </div>
            <div>
              <h1 class="text-lg font-medium text-foreground truncate max-w-md">{{ post.title }}</h1>
              <p class="text-xs text-muted-foreground-1">Просмотр новости</p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <a
              :href="route('dashboard.posts.edit', post.id)"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all shadow-sm"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <span class="hidden sm:inline">Редактировать</span>
            </a>
            <button
              @click.prevent="confirmDelete"
              class="inline-flex items-center gap-2 px-4 py-2 bg-surface border border-layer-line text-foreground text-sm font-medium rounded-lg hover:bg-rose-500/10 hover:border-rose-500/30 hover:text-rose-600 transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Status & Meta -->
      <div class="flex flex-wrap items-center gap-3 mb-6">
        <span :class="getStatusBadgeClass(post.status)">
          <span class="w-2 h-2 rounded-full mr-2" :class="getStatusDotClass(post.status)"></span>
          {{ getStatusLabel(post.status) }}
        </span>
        <span
          v-if="post.category"
          class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-primary/10 text-primary border border-primary/20"
        >
          {{ post.category.title }}
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-surface text-muted-foreground-1 border border-layer-line">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ formatDate(post.publish_at) }}
        </span>
        <span
          v-if="post.reading_time"
          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-surface text-muted-foreground-1 border border-layer-line"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          {{ post.reading_time }} мин. чтения
        </span>
      </div>

      <!-- Preview Image -->
      <div v-if="post.preview" class="mb-6">
        <img :src="`/storage/${post.preview}`" :alt="post.title" class="w-full h-96 object-cover rounded-xl border border-layer-line shadow-lg" />
      </div>

      <!-- Info Card -->
      <div class="bg-layer border border-layer-line rounded-xl shadow-xs mb-6 overflow-hidden">
        <div class="px-5 py-4 bg-surface/50 border-b border-line-2">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-muted-foreground-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-sm font-medium text-foreground">Информация</h2>
          </div>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <svg class="w-4 h-4 text-muted-foreground-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <span class="text-xs font-medium text-muted-foreground-1">URL</span>
              </div>
              <p class="text-sm text-foreground font-mono bg-surface px-2.5 py-1.5 rounded-md border border-layer-line truncate">
                {{ post.slug }}
              </p>
            </div>

            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <svg class="w-4 h-4 text-muted-foreground-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-xs font-medium text-muted-foreground-1">Автор</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                  <span class="text-xs font-medium text-primary">
                    {{ getAuthorInitials(post.author?.name) }}
                  </span>
                </div>
                <p class="text-sm text-foreground">
                  {{ post.author?.name || '—' }}
                </p>
              </div>
            </div>

            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <svg class="w-4 h-4 text-muted-foreground-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-medium text-muted-foreground-1">Создано</span>
              </div>
              <p class="text-sm text-foreground">
                {{ formatDate(post.created_at) }}
              </p>
            </div>

            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <svg class="w-4 h-4 text-muted-foreground-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-xs font-medium text-muted-foreground-1">Обновлено</span>
              </div>
              <p class="text-sm text-foreground">
                {{ formatDate(post.updated_at) }}
              </p>
            </div>
          </div>

          <!-- Authors List -->
          <div v-if="post.authors && post.authors.length > 0" class="mt-5 pt-5 border-t border-line-2">
            <div class="flex items-center gap-2 mb-3">
              <svg class="w-4 h-4 text-muted-foreground-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span class="text-xs font-medium text-muted-foreground-1">Авторы</span>
            </div>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="(author, index) in post.authors"
                :key="index"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-surface text-foreground border border-layer-line"
              >
                <div class="w-4 h-4 rounded-full bg-primary/10 flex items-center justify-center">
                  <span class="text-[10px] font-medium text-primary">
                    {{ author[0]?.toUpperCase() }}
                  </span>
                </div>
                {{ author }}
              </span>
            </div>
          </div>

          <!-- Tags -->
          <div v-if="post.tags && post.tags.length > 0" class="mt-5 pt-5 border-t border-line-2">
            <div class="flex items-center gap-2 mb-3">
              <svg class="w-4 h-4 text-muted-foreground-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <span class="text-xs font-medium text-muted-foreground-1">Теги</span>
            </div>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="(tag, index) in post.tags"
                :key="index"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-primary/10 text-primary border border-primary/20"
              >
                {{ tag.name }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Card -->
      <div v-if="post.content && post.content.length > 0" class="bg-layer border border-layer-line rounded-xl shadow-xs mb-6 overflow-hidden">
        <div class="px-5 py-4 bg-surface/50 border-b border-line-2">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-muted-foreground-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h2 class="text-sm font-medium text-foreground">Содержимое</h2>
          </div>
        </div>
        <div class="p-5 space-y-4">
          <div
            v-for="(block, index) in post.content"
            :key="index"
            class="prose prose-sm max-w-none"
          >
            <!-- Heading -->
            <div v-if="block.type === 'heading'" :class="getHeadingClass(block.data.level)">
              {{ block.data.content }}
            </div>

            <!-- Paragraph -->
            <p v-if="block.type === 'paragraph'" class="text-foreground leading-relaxed">
              {{ block.data.content }}
            </p>

            <!-- Image -->
            <div v-if="block.type === 'image'" class="my-4">
              <img :src="`/storage/${block.data.url}`" :alt="block.data.caption" class="w-full h-auto rounded-lg border border-layer-line" />
              <p v-if="block.data.caption" class="text-xs text-muted-foreground-1 text-center mt-2">
                {{ block.data.caption }}
              </p>
            </div>

            <!-- Files -->
            <div v-if="block.type === 'files'" class="space-y-2">
              <div
                v-for="(file, fileIndex) in block.data.file"
                :key="fileIndex"
                class="flex items-center gap-3 p-3 bg-surface border border-layer-line rounded-lg hover:border-primary/50 transition-all"
              >
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <span class="text-sm text-foreground flex-1">{{ file.title }}</span>
                <span class="text-xs text-muted-foreground-1 font-mono">{{ file.size }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gallery Card -->
      <div v-if="post.images && post.images.length > 0" class="bg-layer border border-layer-line rounded-xl shadow-xs mb-6 overflow-hidden">
        <div class="px-5 py-4 bg-surface/50 border-b border-line-2">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-muted-foreground-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h2 class="text-sm font-medium text-foreground">Галерея</h2>
            <span class="ml-auto text-xs text-muted-foreground-1">{{ post.images.length }} фото</span>
          </div>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <div
              v-for="(img, index) in post.images"
              :key="index"
              class="group relative aspect-square overflow-hidden rounded-lg border border-layer-line bg-surface"
            >
              <img
                :src="`/storage/${img}`"
                :alt="`Gallery ${index + 1}`"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Slider Card -->
      <div v-if="post.slide" class="bg-layer border border-layer-line rounded-xl shadow-xs mb-6 overflow-hidden">
        <div class="px-5 py-4 bg-surface/50 border-b border-line-2">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-muted-foreground-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
            </svg>
            <h2 class="text-sm font-medium text-foreground">Настройки слайда</h2>
          </div>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <div>
              <span class="text-xs font-medium text-muted-foreground-1">Слайдер</span>
              <p class="text-sm text-foreground font-medium mt-1">{{ post.slide.title }}</p>
            </div>
            <div>
              <span class="text-xs font-medium text-muted-foreground-1">Статус</span>
              <p class="text-sm text-foreground font-medium mt-1">
                <span :class="post.slide.is_active ? 'text-emerald-600' : 'text-muted-foreground-2'">
                  {{ post.slide.is_active ? 'Активен' : 'Не активен' }}
                </span>
              </p>
            </div>
            <div>
              <span class="text-xs font-medium text-muted-foreground-1">Позиция текста</span>
              <p class="text-sm text-foreground font-medium mt-1 capitalize">{{ post.slide.settings?.text_position || 'left' }}</p>
            </div>
            <div>
              <span class="text-xs font-medium text-muted-foreground-1">Цвет текста</span>
              <div class="flex items-center gap-2 mt-1">
                <div class="w-5 h-5 rounded border border-layer-line" :style="{ backgroundColor: post.slide.color_theme }"></div>
                <span class="text-sm text-foreground font-mono">{{ post.slide.color_theme }}</span>
              </div>
            </div>
          </div>

          <div v-if="post.slide.image?.url" class="mt-5 pt-5 border-t border-line-2">
            <span class="text-xs font-medium text-muted-foreground-1 mb-2 block">Изображение слайда</span>
            <img :src="`/storage/${post.slide.image.url}`" alt="Slide" class="w-full h-48 object-cover rounded-lg border border-layer-line" />
          </div>

          <div v-if="post.slide.end_time" class="mt-5 pt-5 border-t border-line-2">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-muted-foreground-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-xs font-medium text-muted-foreground-1">Окончание показа</span>
            </div>
            <p class="text-sm text-foreground mt-1.5">{{ formatDate(post.slide.end_time) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PostStatus from '@/Enum/PostStatus.js';

export default {
  name: 'PostShow',

  props: {
    post: {
      type: Object,
      required: true
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE(`Просмотр новости - ${this.post.title}`);
  },

  methods: {
    getStatusBadgeClass(status) {
      const statusValue = status?.value ?? status;
      const statusObj = PostStatus.fromValue(statusValue);
      const colorClasses = {
        gray: 'bg-stone-500/10 text-stone-700 border-stone-500/20',
        success: 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20',
        warning: 'bg-amber-500/10 text-amber-700 border-amber-500/20',
        danger: 'bg-rose-500/10 text-rose-700 border-rose-500/20'
      };
      const color = statusObj ? statusObj.color : 'gray';
      return `inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium border ${colorClasses[color]}`;
    },

    getStatusDotClass(status) {
      const statusValue = status?.value ?? status;
      const statusObj = PostStatus.fromValue(statusValue);
      const colorClasses = {
        gray: 'bg-stone-500',
        success: 'bg-emerald-500',
        warning: 'bg-amber-500',
        danger: 'bg-rose-500'
      };
      const color = statusObj ? statusObj.color : 'gray';
      return colorClasses[color];
    },

    getStatusLabel(status) {
      const statusValue = status?.value ?? status;
      return PostStatus.getLabel(statusValue) || status;
    },

    formatDate(date) {
      if (!date) return '—';
      return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    getHeadingClass(level) {
      const classes = {
        h1: 'text-2xl font-semibold text-foreground mb-4',
        h2: 'text-xl font-semibold text-foreground mb-3',
        h3: 'text-lg font-semibold text-foreground mb-2.5',
        h4: 'text-base font-semibold text-foreground mb-2'
      };
      return classes[level] || classes.h2;
    },

    getAuthorInitials(name) {
      if (!name) return '?';
      const parts = name.split(' ');
      if (parts.length >= 2) {
        return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
      }
      return name[0].toUpperCase();
    },

    confirmDelete() {
      if (confirm(`Вы уверены, что хотите удалить новость "${this.post.title}"?`)) {
        this.$inertia.delete(route('dashboard.posts.destroy', this.post.id), {
          preserveScroll: true
        });
      }
    },

    route(name, params) {
      return route(name, params);
    }
  }
}
</script>
