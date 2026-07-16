/**
 * Sidebar menu configuration.
 * Each item defines a navigation section with optional collapsible children.
 *
 * @typedef {Object} MenuItem
 * @property {string} key            - Unique identifier (used for expand state)
 * @property {string} label          - Display name
 * @property {string} icon           - Icon name (mapped in SidebarNavItem)
 * @property {string|null} route     - Parent route name (null for simple links)
 * @property {string|null} permission - Required permission to view (null = always visible)
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
    permission: null,
  },
  {
    key: 'posts',
    label: 'Новости',
    icon: 'document',
    route: null,
    activePrefixes: ['dashboard.posts', 'dashboard.sliders'],
    permission: 'view_any_post',
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
    permission: 'view_any_additional_education',
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
    permission: 'view_any_admission_campaign',
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
    permission: 'view_any_schedule',
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
    permission: 'view_any_faculty',
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
    permission: 'view_any_academic_journal',
  },
  {
    key: 'site-structure',
    label: 'Структура сайта',
    icon: 'folder',
    route: null,
    activePrefixes: ['dashboard.main-sections', 'dashboard.sub-sections', 'dashboard.pages'],
    permission: 'view_any_main_section',
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
    permission: 'view_any_contact_widget',
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
    permission: 'view_any_user',
    children: [
      { label: 'Все пользователи', route: 'dashboard.users.index' },
    ],
  },
  {
    key: 'roles',
    label: 'Роли',
    icon: 'shield-check',
    route: null,
    activePrefixes: ['dashboard.roles'],
    permission: 'view_any_role',
    children: [
      { label: 'Все роли', route: 'dashboard.roles.index' },
    ],
  },
  {
    key: 'vikon-updates',
    label: 'Обновления VIKON',
    icon: 'arrow-path',
    route: 'dashboard.vikon-updates.index',
    activePrefixes: ['dashboard.vikon-updates'],
    permission: null,
  },
  {
    key: 'integration-credentials',
    label: 'Интеграционные ключи',
    icon: 'key',
    route: 'dashboard.integration-credentials.index',
    activePrefixes: ['dashboard.integration-credentials'],
    permission: null,
  },
];
