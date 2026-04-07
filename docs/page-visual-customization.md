# Визуальная кастомизация страниц — Анализ и план реализации

> **Дата:** 7 апреля 2026 г.
> **Контекст:** Запрос на функционал изменения визуала страниц через админку (отступы, шрифты, размеры, цвета и т.д.)

---

## 📊 Текущая архитектура страниц

В проекте существуют **два типа страниц**:

### Тип A: Database-driven страницы (Page Builder)

| Параметр | Значение |
|----------|----------|
| **Создаются** | Через Filament админку |
| **Хранение** | JSON в колонке `content` таблицы `pages` |
| **Рендеринг** | Универсальный `Page.vue` → `Builder` компонент → динамический рендер блоков |
| **Стилизация** | Hardcoded Tailwind классы в блоках (`HeadingBlock.vue`, `ParagraphBlock.vue`, и т.д.) |
| **Настройки** | Колонка `settings` (JSON) — только **логические флаги** (скрыть breadcrumbs, навигацию и т.д.) |

**Ключевые файлы:**
- Model: `app/Containers/AppStructure/Models/Page.php`
- Controller: `app/Containers/AppStructure/UI/WEB/Controllers/PageController.php`
- Action: `app/Containers/AppStructure/Actions/RenderPageAction.php`
- Vue: `resources/js/Pages/Page.vue`
- Builder: `resources/js/componentss/shared/builder/pageBuilder/Builder.vue`
- Filament Form: `app/Filament/Components/Forms/PageForm.php`
- Filament Builder: `app/Filament/Components/Forms/ItemForm/Pages/ContentBuilderItem.php`

### Тип B: Code-driven страницы (Hardcoded Vue компоненты)

| Параметр | Значение |
|----------|----------|
| **Создаются** | Как `.vue` файлы в `resources/js/Pages/` |
| **Примеры** | `Main.vue` (главная), страницы Dashboard |
| **Стилизация** | Tailwind классы напрямую в template |
| **Авто-регистрация** | `RegisterApplicationRoutesTask` создаёт запись в `pages` с `is_registered = true` |

**Ключевые файлы:**
- Task: `app/Containers/AppStructure/Tasks/RegisterApplicationRoutesTask.php`
- Middleware: `app/Ship/Middleware/AccessCheck.php`
- Пример: `resources/js/Pages/Main.vue`

### Текущая структура `pages.settings`

```json
{
  "hide_page_sub_section_links": false,
  "hide_page_navigate_links": false,
  "hide_breadcrumbs": false,
  "form": {
    "id": "form_id",
    "title": "Заголовок",
    "description": "Описание",
    "button": "Текст кнопки"
  }
}
```

### Текущая структура `pages` таблицы

| Колонка | Тип | Описание |
|---------|-----|----------|
| `id` | bigint | Primary key |
| `title` | string(255) | Заголовок страницы |
| `content` | longText (JSON) | Builder блоки |
| `slug` | string(255) | URL сегмент |
| `path` | string | Полный URL путь |
| `is_registered` | boolean | true = авто-регистрация из кода |
| `is_visible` | boolean | Видимость |
| `searchable` | boolean | Индексация в поиске |
| `is_url` | boolean | Редирект на внешний URL |
| `code` | integer | HTTP статус (200, 404, 500) |
| `sub_section_id` | bigint FK | Связь с подразделом |
| `settings` | longText (JSON) | Настройки отображения |
| `icon` | string | Heroicon для навигации |
| `search_data` | longText | Текст для поиска |

---

## 🎯 Проблема

Сейчас **нет возможности** через админку менять визуальные параметры страниц:
- ❌ Отступы (padding, margin)
- ❌ Размер шрифта
- ❌ Шрифт (font-family)
- ❌ Цвета
- ❌ Максимальная ширина контента
- ❌ И другие CSS-свойства

Все стили **захардкожены** в Vue компонентах и блоках билдера.

---

## 💡 Решения (от простого к сложному)

### Решение 1: Расширение `settings` JSON (Рекомендуемое)

**Концепция:** Добавить в существующую колонку `pages.settings` секцию `visual` с визуальными настройками.

**Структура данных:**
```json
{
  "hide_page_sub_section_links": false,
  "hide_page_navigate_links": false,
  "hide_breadcrumbs": false,
  "form": { ... },
  "visual": {
    "typography": {
      "font_family": "Inter",
      "title_size": "2xl",
      "body_size": "base",
      "line_height": "normal"
    },
    "spacing": {
      "container_padding_top": "10",
      "container_padding_bottom": "10",
      "container_padding_x": "4",
      "content_gap": "5"
    },
    "layout": {
      "max_width": "screen-xl",
      "sidebar_position": "left"
    },
    "colors": {
      "background": "white",
      "text": "gray-900"
    }
  }
}
```

**Backend (Filament Form) — пример добавления в PageForm.php:**
```php
Section::make('Визуальные настройки')
    ->description('Настройка внешнего вида страницы')
    ->collapsible()
    ->schema([
        Select::make('visual.typography.font_family')
            ->label('Шрифт')
            ->options([
                'Inter' => 'Inter (по умолчанию)',
                'Roboto' => 'Roboto',
                'Open Sans' => 'Open Sans',
                'Montserrat' => 'Montserrat',
            ])
            ->default('Inter'),
        
        Select::make('visual.typography.title_size')
            ->label('Размер заголовка')
            ->options([
                'xl' => 'XL (маленький)',
                '2xl' => '2XL (стандарт)',
                '3xl' => '3XL (большой)',
                '4xl' => '4XL (очень большой)',
            ])
            ->default('2xl'),
        
        Select::make('visual.spacing.container_padding_top')
            ->label('Отступ сверху')
            ->options([
                '0' => '0',
                '4' => '16px',
                '6' => '24px',
                '10' => '40px',
            ])
            ->default('10'),
    ]);
```

**Frontend (Page.vue) — пример применения:**
```vue
<template>
  <BasicPageWrapper>
    <div 
      :class="[
        'relative mx-auto max-w-screen-xl w-full px-4 md:flex md:flex-row',
        visualClasses.containerPadding,
        visualClasses.backgroundColor
      ]"
      :style="customFontStyle"
    >
      <BasicTitle 
        :header="page.data.title" 
        :size="settings?.visual?.typography?.title_size || '2xl'" 
      />
      <div id="page-area" :class="contentGapClass">
        <Builder :blocks="page.data.content" />
      </div>
    </div>
  </BasicPageWrapper>
</template>

<script>
export default {
  computed: {
    visualClasses() {
      const v = this.settings?.visual || {};
      return {
        containerPadding: `pt-${v.spacing?.container_padding_top || 10} pb-${v.spacing?.container_padding_bottom || 10}`,
        backgroundColor: `bg-${v.colors?.background || 'white'}`,
      };
    },
    customFontStyle() {
      const typography = this.settings?.visual?.typography || {};
      return {
        fontFamily: typography.font_family || 'Inter',
        fontSize: typography.body_size || '16px',
        lineHeight: typography.line_height === 'relaxed' ? '1.75' : 'normal',
      };
    },
  },
};
</script>
```

**Плюсы:**
- ✅ Минимальные изменения в архитектуре
- ✅ Использует существующую инфраструктуру `settings`
- ✅ Легко масштабировать (добавить новые поля в JSON)
- ✅ Не требует миграций БД
- ✅ Работает для **обоих типов страниц** (нужно только передать `settings` в Inertia)

**Минусы:**
- ❌ Ограниченный набор стилей (только то, что предусмотрено в UI)
- ❌ Нужно менять все Builder-блоки для поддержки наследования стилей

---

### Решение 2: CSS Custom Properties (CSS Variables)

**Концепция:** Хранить CSS-переменные в `settings`, применять через inline `<style>` тег.

**Структура данных:**
```json
{
  "visual": {
    "css_variables": {
      "--page-font-family": "Inter",
      "--page-title-size": "2rem",
      "--page-content-padding-top": "40px",
      "--page-max-width": "1280px",
      "--page-background": "#FFFFFF",
      "--page-text-color": "#0F172A"
    }
  }
}
```

**Frontend (Page.vue):**
```vue
<template>
  <BasicPageWrapper>
    <style v-if="hasCustomStyles" scoped>
      #page-area {
        font-family: var(--page-font-family, Inter);
        font-size: var(--page-body-size, 16px);
      }
      h1 {
        font-size: var(--page-title-size, 2rem);
      }
      .page-container {
        padding-top: var(--page-content-padding-top, 40px);
        padding-bottom: var(--page-content-padding-bottom, 40px);
        max-width: var(--page-max-width, 1280px);
        background-color: var(--page-background, #FFFFFF);
        color: var(--page-text-color, #0F172A);
      }
    </style>

    <div class="page-container">
      <!-- ... -->
    </div>
  </BasicPageWrapper>
</template>

<script>
export default {
  computed: {
    cssVariables() {
      return this.settings?.visual?.css_variables || {};
    },
    hasCustomStyles() {
      return Object.keys(this.cssVariables).length > 0;
    },
  },
};
</script>
```

**Плюсы:**
- ✅ Гибкость — можно задать **любое** CSS-свойство
- ✅ Каскадное применение — переменные наследуются вниз
- ✅ Не нужно менять все Builder-блоки (применяются глобально)
- ✅ Легко реализовать в админке (ключ-значение форма)

**Минусы:**
- ❌ Требует знания CSS у контент-менеджеров (или ограниченный набор в UI)
- ❌ Могут быть конфликты с Tailwind классами
- ❌ Сложнее валидировать значения

---

### Решение 3: Page Themes / Templates System

**Концепция:** Создать систему **тем** — предустановленных наборов стилей.

**Структура данных:**

**Таблица `page_themes`:**
```sql
id | name | description | styles (JSON) | is_active
1  | Default | Стандартная тема | {...} | true
2  | Compact | Компактная | {...} | false
3  | Spacious | Просторная | {...} | false
```

**`pages` таблица — новые колонки:**
```sql
theme_id (FK -> page_themes)
custom_overrides (JSON) — индивидуальные переопределения
```

**Пример `styles` в теме:**
```json
{
  "typography": {
    "font_family": "Inter",
    "title_sizes": { "h1": "3xl", "h2": "2xl", "h3": "xl" },
    "body_size": "base",
    "line_height": "relaxed"
  },
  "spacing": {
    "container": { "max_width": "screen-xl", "px": "4", "py": "10" },
    "content_gap": "space-y-5"
  },
  "colors": {
    "background": "white",
    "text": "gray-900",
    "accent": "primary"
  },
  "borders": {
    "radius": "rounded-lg",
    "shadow": "shadow-sm"
  }
}
```

**Filament админка:**
- CRUD для `PageTheme` (создание/редактирование тем)
- В `PageForm`: `Select::make('theme_id')->relationship('theme', 'name')`
- Опционально: overrides для конкретной страницы

**Frontend:**
```vue
<script>
export default {
  computed: {
    theme() {
      return this.page.data.theme?.styles || {};
    },
    overrides() {
      return this.page.data.custom_overrides || {};
    },
    mergedSettings() {
      return _.merge({}, this.theme, this.overrides);
    },
  },
};
</script>
```

**Плюсы:**
- ✅ **Масштабируемость** — одна тема применяется к множеству страниц
- ✅ **Безопасность** — админ не сломает стили (выбирает из готовых)
- ✅ **A/B тестирование** — легко менять темы
- ✅ Разделение ответственности: дизайнер создаёт темы, контент-менеджер выбирает

**Минусы:**
- ❌ **Сложность реализации** — новая таблица, CRUD, UI для тем
- ❌ Требует больше времени на разработку
- ❌ Нужно менять `PageResource`, `RenderPageAction`, `Page.vue`

---

### Решение 4: Custom CSS per Page (Полная свобода)

**Концепция:** Позволить админу писать **произвольный CSS** для страницы.

**Структура данных:**
```json
{
  "visual": {
    "custom_css": "#page-area h1 { font-size: 2.5rem; color: #2D4191; }\n#page-area p { line-height: 1.8; }"
  }
}
```

**Frontend:**
```vue
<template>
  <BasicPageWrapper>
    <style v-if="settings?.visual?.custom_css" v-html="settings.visual.custom_css" />
    <!-- ... -->
  </BasicPageWrapper>
</template>
```

**Filament Form:**
```php
Textarea::make('visual.custom_css')
    ->label('Custom CSS')
    ->rows(10)
    ->monospace()
    ->helperText('Произвольный CSS для страницы. Используйте с осторожностью!')
```

**Плюсы:**
- ✅ **Полная гибкость** — можно сделать **что угодно**
- ✅ Минимальная реализация (одно поле + `<style>` тег)

**Минусы:**
- ❌ **Опасно** — можно сломать layout, нужны ограничения
- ❌ Требует знаний CSS у админа
- ❌ Сложно валидировать
- ❌ Потенциальные XSS (нужен sanitize)
- ❌ **Не рекомендуется** для не-технических пользователей

---

## 🏆 Рекомендация (Консенсус комитета)

**Используй комбинацию Решений 1 + 2:**

1. **Основное:** Расширение `settings.visual` с **типизированными настройками** (шрифты, отступы, цвета через select/dropdown)
2. **Дополнительное:** CSS Custom Variables для **тонкой настройки** (для продвинутых пользователей)

**Почему:**
- ✅ Покрывает 95% use cases (типичные настройки визуала)
- ✅ Безопасно (админ выбирает из пресетов, не пишет CSS)
- ✅ CSS variables дают гибкость для edge cases
- ✅ Минимальные изменения в архитектуре (используем существующий `settings`)
- ✅ Работает для **обоих типов страниц** (нужно только передавать `settings` в Inertia для hardcoded страниц)

---

## 📋 План реализации

### Шаг 1: Backend — Filament Form

**Файл:** `app/Filament/Components/Forms/PageForm.php`

Добавить в Tab `'Дополнительные настройки'` новую секцию:

```php
Section::make('Визуальные настройки')
    ->description('Настройка типографики, отступов и цветов страницы')
    ->collapsible()
    ->schema([
        // Типографика
        Select::make('visual.typography.font_family')
            ->label('Шрифт')
            ->options([
                'Inter' => 'Inter (по умолчанию)',
                'Roboto' => 'Roboto',
                'Open Sans' => 'Open Sans',
                'Montserrat' => 'Montserrat',
            ])
            ->default('Inter')
            ->columnSpan(1),
        
        Select::make('visual.typography.title_size')
            ->label('Размер заголовка H1')
            ->options([
                'xl' => 'XL (маленький)',
                '2xl' => '2XL (стандарт)',
                '3xl' => '3XL (большой)',
                '4xl' => '4XL (очень большой)',
            ])
            ->default('2xl')
            ->columnSpan(1),
        
        Select::make('visual.typography.body_size')
            ->label('Размер текста')
            ->options([
                'sm' => 'SM (маленький)',
                'base' => 'Base (стандарт)',
                'lg' => 'LG (большой)',
            ])
            ->default('base')
            ->columnSpan(1),
        
        Select::make('visual.typography.line_height')
            ->label('Межстрочный интервал')
            ->options([
                'tight' => 'Tight (плотный)',
                'normal' => 'Normal (стандарт)',
                'relaxed' => 'Relaxed (просторный)',
            ])
            ->default('normal')
            ->columnSpan(1),
        
        // Отступы
        Select::make('visual.spacing.container_padding_top')
            ->label('Отступ сверху')
            ->options([
                '0' => '0',
                '4' => '16px',
                '6' => '24px',
                '10' => '40px',
                '16' => '64px',
            ])
            ->default('10')
            ->columnSpan(1),
        
        Select::make('visual.spacing.container_padding_bottom')
            ->label('Отступ снизу')
            ->options([
                '0' => '0',
                '4' => '16px',
                '6' => '24px',
                '10' => '40px',
                '16' => '64px',
            ])
            ->default('10')
            ->columnSpan(1),
        
        Select::make('visual.spacing.content_gap')
            ->label('Расстояние между блоками')
            ->options([
                '2' => '8px',
                '5' => '20px (стандарт)',
                '8' => '32px',
                '10' => '40px',
            ])
            ->default('5')
            ->columnSpan(1),
        
        // Layout
        Select::make('visual.layout.max_width')
            ->label('Максимальная ширина контента')
            ->options([
                'screen-md' => '768px',
                'screen-lg' => '1024px',
                'screen-xl' => '1280px (стандарт)',
                'screen-2xl' => '1536px',
                'full' => '100%',
            ])
            ->default('screen-xl')
            ->columnSpan(1),
    ])
    ->columns(4),
```

### Шаг 2: Frontend — Page.vue

**Файл:** `resources/js/Pages/Page.vue`

Изменения:

1. Добавить computed properties для визуальных настроек
2. Применить классы к контейнеру
3. Передать настройки в `BasicTitle` и `Builder`

```vue
<template>
  <MetaTags :seo="seo" />
  <MainPageNavBar :sections="$page.props.navigation" />

  <BasicPageWrapper>
    <div
      :class="[
        'relative mx-auto w-full px-4 md:flex md:flex-row',
        `max-w-${visualSettings.layout?.max_width || 'screen-xl'}`,
        `pt-${visualSettings.spacing?.container_padding_top || '10'}`,
        `pb-${visualSettings.spacing?.container_padding_bottom || '10'}`,
        `bg-${visualSettings.colors?.background || 'white'}`
      ]"
      :style="typographyStyle"
    >
      <PageSubSectionLinks
        v-if="!settings?.hide_page_sub_section_links"
        :sub-section-pages="subSectionPages"
        :current-section="page.data.section"
      />
      <NavigateLinks
        v-if="!settings?.hide_page_navigate_links"
        :header-navs="headerNavs"
      />
      <div class="w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6">
        <div :class="`space-y-${visualSettings.spacing?.content_gap || '5'}`">
          <BaseBreadcrumbs
            v-if="!settings?.hide_breadcrumbs"
            :breadcrumbs="breadcrumbs"
          >
            <BreadcrumbsItem
              :title="breadcrumbs.page.data.title"
              :url="route('page.view', breadcrumbs.page.data.path)"
            />
          </BaseBreadcrumbs>
          
          <BasicTitle 
            :header="page.data.title" 
            :size="visualSettings.typography?.title_size || '2xl'"
          />
          
          <div id="page-area">
            <Builder 
              :blocks="page.data.content" 
              :settings="visualSettings"
            />
          </div>
        </div>
      </div>
    </div>

    <BasicFooter />
  </BasicPageWrapper>

  <!-- Floating form (без изменений) -->
</template>

<script>
export default {
  name: "Page",
  data() {
    return {
      headerNavs: (this.page?.data?.content || [])
        .filter((block) => block?.type === "heading")
        .map((block) => ({
          id: block?.data?.id,
          text: block?.data?.content,
        })),
      settings: this.page?.data?.settings || {},
    };
  },
  props: {
    page: { type: Object },
    subSectionPages: { type: Object },
    breadcrumbs: { type: Object },
    seo: { type: Object },
  },
  computed: {
    visualSettings() {
      return this.settings?.visual || {};
    },
    typographyStyle() {
      const t = this.visualSettings.typography || {};
      return {
        fontFamily: t.font_family || 'Inter',
        fontSize: this.bodySizeMap[t.body_size] || '16px',
        lineHeight: this.lineHeightMap[t.line_height] || 'normal',
      };
    },
  },
  data() {
    return {
      bodySizeMap: {
        'sm': '14px',
        'base': '16px',
        'lg': '18px',
      },
      lineHeightMap: {
        'tight': '1.25',
        'normal': '1.5',
        'relaxed': '1.75',
      },
    };
  },
  // ... components, methods (без изменений)
};
</script>
```

### Шаг 3: Обновить Builder-блоки

**Файл:** `resources/js/componentss/shared/builder/pageBuilder/Builder.vue`

Передать `settings` в дочерние блоки:

```vue
<template>
  <div>
    <component
      v-for="(block, index) in blocks"
      :key="index"
      :is="blockComponents[block.type]"
      :data="block.data"
      :settings="settings"
    />
  </div>
</template>

<script>
export default {
  props: {
    blocks: { type: Array, required: true },
    settings: { type: Object, default: () => ({}) },
  },
};
</script>
```

### Шаг 4: Обновить BasicTitle

**Файл:** `resources/js/componentss/ui/titles/BasicTitle.vue`

Добавить prop для размера:

```vue
<template>
  <div>
    <h1 :class="titleClass">{{ header }}</h1>
  </div>
</template>

<script>
export default {
  name: "BasicTitle",
  props: {
    header: { type: String, required: true },
    size: { type: String, default: '2xl' },
  },
  computed: {
    titleClass() {
      const sizeMap = {
        'xl': 'text-xl font-bold mb-5',
        '2xl': 'text-2xl font-bold mb-5 md:text-3xl',
        '3xl': 'text-3xl font-bold mb-5 md:text-4xl',
        '4xl': 'text-4xl font-bold mb-5 md:text-5xl',
      };
      return sizeMap[this.size] || sizeMap['2xl'];
    },
  },
};
</script>
```

### Шаг 5: Hardcoded страницы

Для страниц типа `Main.vue` — нужно передавать `settings` из `pages` записи через Inertia.

**Backend:** В контроллере главной страницы найти страницу и передать settings:
```php
$mainPage = Page::where('path', '/')->first();
return inertia('Main', [
    'posts' => $posts,
    'pageSettings' => $mainPage?->settings,
]);
```

**Frontend:** В `Main.vue` использовать аналогичный подход.

---

## 📝 Чеклист реализации

- [ ] **Шаг 1:** Расширить `PageForm.php` — секция "Визуальные настройки"
- [ ] **Шаг 2:** Обновить `Page.vue` — применение стилей из `settings.visual`
- [ ] **Шаг 3:** Обновить `Builder.vue` — передача `settings` в блоки
- [ ] **Шаг 4:** Обновить `BasicTitle.vue` — поддержка размера заголовка
- [ ] **Шаг 5:** Обновить hardcoded страницы (`Main.vue` и др.) — передача `settings`
- [ ] **Тестирование:** Проверить все комбинации настроек
- [ ] **Документация:** Описать доступные настройки для контент-менеджеров

---

## 🔮 Будущие улучшения (Phase 2)

- [ ] CSS Custom Variables для продвинутой настройки (Решение 2)
- [ ] Система тем (Решение 3) — если потребуется масштабирование
- [ ] Превью изменений в реальном времени (live preview в админке)
- [ ] Наследование настроек от родительской страницы
- [ ] Глобальные настройки сайта (дефолтная тема для всех страниц)

---

## 📚 Ссылки на ключевые файлы

| Файл | Путь |
|------|------|
| Page Model | `app/Containers/AppStructure/Models/Page.php` |
| PageForm | `app/Filament/Components/Forms/PageForm.php` |
| PageController | `app/Containers/AppStructure/UI/WEB/Controllers/PageController.php` |
| RenderPageAction | `app/Containers/AppStructure/Actions/RenderPageAction.php` |
| Page.vue | `resources/js/Pages/Page.vue` |
| Builder.vue | `resources/js/componentss/shared/builder/pageBuilder/Builder.vue` |
| BasicTitle.vue | `resources/js/componentss/ui/titles/BasicTitle.vue` |
| Main.vue | `resources/js/Pages/Main.vue` |
| Tailwind Config | `tailwind.config.js` |
