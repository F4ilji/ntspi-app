/**
 * Quick actions configuration.
 * These actions appear in the user profile area (below the user info).
 *
 * @typedef {Object} QuickAction
 * @property {string} label        - Display name
 * @property {string} route        - Route name for internal links
 * @property {string} icon         - Icon name (mapped in SidebarUser)
 * @property {boolean} [external]  - If true, uses href instead of route
 * @property {string} [href]       - External URL (if external is true)
 */

/** @type {QuickAction[]} */
export const quickActions = [
  {
    label: 'Админ-панель',
    route: null,
    href: '/admin',
    icon: 'cog',
    external: true,
  },
  {
    label: 'Быстрая загрузка',
    route: 'dashboard.quick-upload.create',
    href: null,
    icon: 'upload',
  },
];
