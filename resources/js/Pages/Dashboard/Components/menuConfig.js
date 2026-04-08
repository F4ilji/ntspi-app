/**
 * Sidebar menu configuration.
 * Each item defines a navigation section with optional collapsible children.
 *
 * @typedef {Object} MenuItem
 * @property {string} key            - Unique identifier (used for expand state)
 * @property {string} label          - Display name
 * @property {string} icon           - Icon name (mapped in SidebarNavItem)
 * @property {string|null} route     - Parent route name (null for simple links)
 * @property {Array<{label: string, route: string}>} [children] - Sub-items
 * @property {string[]} [activePrefixes] - Route prefixes to detect active state
 */

/** @type {MenuItem[]} */
export const menuItems = [
  {
    key: 'home',
    label: 'Главная',
    icon: 'home',
    route: 'dashboard.index',
  },
  {
    key: 'posts',
    label: 'Новости',
    icon: 'document',
    route: null,
    activePrefixes: ['dashboard.posts', 'dashboard.sliders'],
    children: [
      { label: 'Все новости', route: 'dashboard.posts.index' },
      { label: 'AI подготовка', route: 'dashboard.posts.ai-prepared' },
      { label: 'Слайдеры', route: 'dashboard.sliders.index' },
    ],
  },
  {
    key: 'additional-education',
    label: 'Дополнительное образование',
    icon: 'academic-cap',
    route: null,
    activePrefixes: ['dashboard.additional-educations'],
    children: [
      { label: 'Все программы ДПО', route: 'dashboard.additional-educations.index' },
      { label: 'Направления', route: 'dashboard.additional-educations.directions.index' },
      { label: 'Категории', route: 'dashboard.additional-educations.categories.index' },
    ],
  },
  {
    key: 'admission-campaigns',
    label: 'Приемные кампании',
    icon: 'clipboard-document-check',
    route: null,
    activePrefixes: ['dashboard.admission-campaigns', 'dashboard.direction-studies', 'dashboard.educational-programs', 'dashboard.admission-plans'],
    children: [
      { label: 'Все кампании', route: 'dashboard.admission-campaigns.index' },
      { label: 'Направления подготовки', route: 'dashboard.direction-studies.index' },
      { label: 'Образовательные программы', route: 'dashboard.educational-programs.index' },
      { label: 'Планы приема', route: 'dashboard.admission-plans.index' },
    ],
  },
  {
    key: 'schedules',
    label: 'Расписание',
    icon: 'calendar',
    route: null,
    activePrefixes: ['dashboard.schedules', 'dashboard.schedules.upload', 'dashboard.educational-groups'],
    children: [
      { label: 'Все расписания', route: 'dashboard.schedules.index' },
      { label: 'Загрузить файл', route: 'dashboard.schedules.upload.create' },
      { label: 'Учебные группы', route: 'dashboard.educational-groups.index' },
    ],
  },
  {
    key: 'institute-structure',
    label: 'Структура института',
    icon: 'building',
    route: null,
    activePrefixes: ['dashboard.faculties', 'dashboard.divisions', 'dashboard.departments'],
    children: [
      { label: 'Факультеты', route: 'dashboard.faculties.index' },
      { label: 'Кафедры', route: 'dashboard.departments.index' },
      { label: 'Подразделения', route: 'dashboard.divisions.index' },
    ],
  },
  {
    key: 'science',
    label: 'Научные журналы',
    icon: 'beaker',
    route: 'dashboard.academic-journals.index',
    activePrefixes: ['dashboard.academic-journals'],
  },
  {
    key: 'site-structure',
    label: 'Структура сайта',
    icon: 'folder',
    route: null,
    activePrefixes: ['dashboard.main-sections', 'dashboard.sub-sections', 'dashboard.pages'],
    children: [
      { label: 'Главные разделы', route: 'dashboard.main-sections.index' },
      { label: 'Подразделы', route: 'dashboard.sub-sections.index' },
      { label: 'Страницы', route: 'dashboard.pages.index' },
    ],
  },
  {
    key: 'widgets',
    label: 'Виджеты',
    icon: 'rectangle-stack',
    route: null,
    activePrefixes: ['dashboard.contact-widgets', 'dashboard.custom-forms', 'dashboard.page-reference-lists'],
    children: [
      { label: 'Контактные виджеты', route: 'dashboard.contact-widgets.index' },
      { label: 'Пользовательские формы', route: 'dashboard.custom-forms.index' },
      { label: 'Списки ресурсов', route: 'dashboard.page-reference-lists.index' },
    ],
  },
  {
    key: 'users',
    label: 'Пользователи',
    icon: 'user-circle',
    route: null,
    activePrefixes: ['dashboard.users'],
    children: [
      { label: 'Все пользователи', route: 'dashboard.users.index' },
    ],
  },
  {
    key: 'vikon-updates',
    label: 'Обновления VIKON',
    icon: 'arrow-path',
    route: 'dashboard.vikon-updates.index',
    activePrefixes: ['dashboard.vikon-updates'],
  },
];
