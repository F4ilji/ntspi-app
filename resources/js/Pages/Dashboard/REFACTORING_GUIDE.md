# Dashboard Refactoring Guide

## Backend (Porto Architecture)

### ✅ Completed Changes

#### 1. Actions реорганизованы по доменам:
```
Actions/
├── Posts/
│   ├── CreatePostAction.php
│   ├── UpdatePostAction.php
│   ├── PublishPostAction.php
│   └── QuickUploadFileAction.php
├── Sliders/ (9 files)
├── Schedules/ (5 files)
├── EducationalGroups/ (4 files)
└── EmailNews/ (3 files)
```

#### 2. Tasks реорганизованы по доменам:
```
Tasks/
├── Posts/ (7 files)
├── Sliders/ (1 file)
├── Files/ (2 files)
├── AI/ (7 files)
└── Email/ (5 files)
```

#### 3. Все импорты обновлены:
- Controllers используют новые пути Actions
- Actions используют новые пути Tasks
- Commands обновлены

---

## Frontend (Vue Components)

### ✅ Created Shared Components

Расположение: `resources/js/Pages/Dashboard/Components/shared/`

1. **FlashMessages.vue** - Универсальный компонент flash-сообщений
   - Поддерживает глобальные ($page.props.flash) и локальные сообщения
   - Анимации появления/исчезновения
   
2. **Pagination.vue** - Pagination компонент
   - Принимает объект pagination data
   - Рендерит ссылки и информацию о показанных записях

3. **EmptyState.vue** - Empty state для таблиц
   - Настраиваемый заголовок и описание
   - Поддержка action кнопки (ссылка или функция)
   - Кастомизируемая иконка

4. **DataFilters.vue** - Контейнер фильтров
   - Slot-based архитектура для гибкости
   - Встроенная кнопка сброса
   - Emit события reset

5. **SearchInput.vue** - Поле поиска
   - v-model поддержка
   - Иконка лупы
   - Emit события search при Enter

6. **SelectFilter.vue** - Выпадающий список фильтра
   - v-model поддержка
   - Slot-based для options
   - Emit события change

---

## 📋 How to Refactor Pages

### Example: Refactoring Posts/Index.vue

#### BEFORE (old approach - 718 lines):
```vue
<template>
  <DashboardLayout>
    <!-- Flash Messages - duplicated code -->
    <transition ...>
      <div v-if="$page.props.flash?.success" class="...">
        <!-- 40 lines of duplicated markup -->
      </div>
    </transition>
    
    <!-- Filters - duplicated structure -->
    <div class="bg-layer border...">
      <div class="p-4 border-b...">
        <!-- Filter markup -->
      </div>
      <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Search input -->
          <!-- Status filter -->
          <!-- Reset button -->
        </div>
      </div>
    </div>
    
    <!-- Table -->
    <div class="bg-layer border...">
      <!-- Table header -->
      <table>
        <!-- Table body -->
        <!-- Empty state - 30+ lines -->
      </table>
      <!-- Pagination - 30+ lines -->
    </div>
  </DashboardLayout>
</template>
```

#### AFTER (with shared components - ~400 lines):
```vue
<template>
  <DashboardLayout>
    <template #header-icon>
      <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
    </template>
    <template #header-title>Новости</template>
    <template #header-subtitle>Управление публикациями</template>
    <template #header-actions>
      <a :href="route('dashboard.posts.create')" class="...">
        Создать новость
      </a>
    </template>

    <!-- Use shared FlashMessages -->
    <FlashMessages />

    <!-- Created Post Notification (specific to Posts) -->
    <transition ...>
      <div v-if="$page.props.flash?.created_post" class="...">
        <!-- Post-specific notification -->
      </div>
    </transition>

    <!-- Use shared DataFilters -->
    <DataFilters title="Фильтры" @reset="resetFilters">
      <SearchInput 
        v-model="searchQuery" 
        label="Поиск по заголовку"
        placeholder="Введите название..."
        @search="search"
      />
      
      <SelectFilter
        v-model="statusQuery"
        label="Статус"
        placeholder="Все статусы"
        @change="filterByStatus"
      >
        <option v-for="status in [PostStatus.PUBLISHED, PostStatus.VERIFICATION, PostStatus.REJECTED]"
                :key="status.value"
                :value="status.value">
          {{ status.label }}
        </option>
      </SelectFilter>
    </DataFilters>

    <!-- Posts Table -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <!-- Table Header Stats -->
      <div class="px-6 py-4 border-b border-line-2 bg-surface/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-sm text-foreground">
              Всего: <span class="font-medium">{{ posts.total }}</span>
            </span>
            <span class="text-xs text-muted-foreground-1 px-2 py-0.5 bg-primary/10 text-primary rounded-full">
              {{ posts.data.length }} на странице
            </span>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-line-2">
          <thead class="bg-surface/50">
            <tr>
              <!-- Table headers -->
            </tr>
          </thead>
          <tbody class="divide-y divide-line-2">
            <tr v-for="post in posts.data" :key="post.id" class="...">
              <!-- Post row - keep this as is -->
            </tr>

            <!-- Use shared EmptyState -->
            <EmptyState 
              v-if="posts.data.length === 0"
              :columns="6"
              title="Новости не найдены"
              description="Создайте первую новость или измените параметры поиска"
              :action-url="route('dashboard.posts.create')"
              action-text="Создать новость"
            />
          </tbody>
        </table>
      </div>

      <!-- Use shared Pagination -->
      <Pagination :data="posts" />
    </div>

    <!-- Post Modal (keep as is - specific to Posts) -->
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';
import DataFilters from '../Components/shared/DataFilters.vue';
import SearchInput from '../Components/shared/SearchInput.vue';
import SelectFilter from '../Components/shared/SelectFilter.vue';
import EmptyState from '../Components/shared/EmptyState.vue';
import Pagination from '../Components/shared/Pagination.vue';
import PostStatus from '@/Enum/PostStatus.js';

export default {
  name: 'PostsIndex',
  components: {
    DashboardLayout,
    FlashMessages,
    DataFilters,
    SearchInput,
    SelectFilter,
    EmptyState,
    Pagination
  },
  
  // ... rest of the script stays the same
}
</script>
```

### Key Benefits:
1. **Reduced duplication**: ~150 lines saved from FlashMessages, ~30 from EmptyState, ~30 from Pagination
2. **Consistent UI**: All pages use same components
3. **Easier maintenance**: Fix once, apply everywhere
4. **Better readability**: Clear structure, less noise

---

## 🎯 Next Steps

### For Posts/Index.vue:
1. Replace FlashMessages section with `<FlashMessages />`
2. Replace Filters section with `<DataFilters>` + child components
3. Replace Empty State with `<EmptyState>`
4. Replace Pagination with `<Pagination>`
5. Keep table rows and modal as is (post-specific)

### For Schedules/Index.vue:
Same approach, but with schedule-specific filters

### For Posts/Form.vue (1284 lines!):
Break into subcomponents:
- `PostMainInfo.vue` - title, slug, status, category
- `PostContentBuilder.vue` - content blocks
- `PostMedia.vue` - images, preview
- `PostSliderSettings.vue` - slider config
- `PostPublishSettings.vue` - publish settings
- `PostSocialMedia.vue` - VK, other social

---

## 📝 Migration Checklist

- [x] Backend Actions grouped by domain
- [x] Backend Tasks grouped by domain
- [x] All imports updated in Controllers
- [x] Shared components created
- [ ] Posts/Index refactored
- [ ] Schedules/Index refactored
- [ ] Posts/Form broken into subcomponents
- [ ] All pages tested and working
