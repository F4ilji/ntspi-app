# Design System Specification

This document formalizes the design system extracted from the Preline CMS Dashboard. The system relies heavily on **semantic CSS variables** mapped to Tailwind CSS classes, allowing for seamless dark mode support and theme switching.

## 1. Color Palette (Semantic)
The system uses a layered approach to backgrounds and text to create visual hierarchy.

| Name | Tailwind Class | Usage |
| --- | --- | --- |
| **Primary** | `bg-primary` / `text-primary` | Main actions, active states, and brand highlights. |
| **Background (Base)** | `bg-background-2` | The lowest level background (page body). |
| **Surface/Layer** | `bg-layer` | Main content containers and cards. |
| **Surface Muted** | `bg-surface` | Secondary containers or highlighted internal areas. |
| **Navbar/Sidebar** | `bg-navbar-2` / `bg-sidebar-2` | Specialized background for navigation components. |
| **Foreground (Base)** | `text-foreground` | High-contrast primary text. |
| **Muted 1** | `text-muted-foreground-1` | Secondary text, labels, and descriptions. |
| **Muted 2** | `text-muted-foreground-2` | Placeholder text or tertiary information. |
| **Border (Subtle)** | `border-line-2` | Internal dividers and low-contrast separators. |
| **Border (Strong)** | `border-layer-line` | Main component boundaries and container edges. |

## 2. Typography
The system uses a sans-serif stack (Inter) as the default for the UI, with specific sizing for administrative density.

- **Heading 1 (Page):** `font-medium text-lg text-foreground` (Used for dashboard titles).
- **Heading 2 (Section):** `font-medium text-foreground` (Used for card titles).
- **Body Text (Standard):** `text-sm text-foreground` (The default UI scale).
- **Body Text (Condensed):** `text-[13px] text-muted-foreground-1` (Used for metadata, authors, and side-info).
- **Labels/Captions:** `font-medium text-xs uppercase text-muted-foreground-1` (Sidebar headers).
- **Navigation Items:** `text-sm font-medium` (Active) or `text-sm font-normal`.

## 3. Spacing & Layout
- **Layout Shell:**
    - Header height: `pt-13.5` (~54px offset).
    - Sidebar width: `w-60` (240px).
    - Main container: `h-[calc(100dvh-62px)]` (Full height minus header).
- **Standard Card Padding:** `p-4` (Standardizes internal white space).
- **Internal Gaps:**
    - `gap-x-1.5`: Icon + Text pairing.
    - `gap-2`: Small grid elements or button groups.
    - `gap-5`: Large content sections (e.g., Image + Text blocks).

## 4. Components Library (Tailwind Patterns)

### Buttons
- **Primary (Solid):** `bg-primary text-primary-foreground rounded-md text-xl font-semibold` (Icon-heavy).
- **Secondary (Soft):** `bg-secondary text-secondary-foreground rounded-lg hover:bg-secondary-hover` (Profile actions).
- **Ghost/Outline:** `border border-layer-line text-layer-foreground rounded-lg hover:bg-primary-50` (Tools/Refresh).
- **Soft Badge Link:** `bg-primary-500/10 border border-primary-200 text-primary-700 rounded-full` (Upsell/Badges).

### Navigation (Sidebar)
- **Container:** `flex flex-col gap-y-1`
- **Active Item:** `bg-sidebar-2-nav-active font-medium text-sidebar-2-nav-foreground`
- **Inactive Item:** `text-sidebar-2-nav-foreground hover:bg-sidebar-2-nav-hover`
- **Group Divider:** `pt-3 mt-3 border-t border-sidebar-2-divider`

### Card / Container
- **Main Wrapper:** `bg-layer border border-layer-line shadow-xs rounded-lg`
- **Header:** `py-3 px-4 border-b border-card-line`
- **Nested Card:** `p-4 bg-surface rounded-lg` (Used for CTAs inside sidebars).

### Input / Select (Pseudo)
- **Search Bar:** `p-1.5 ps-2.5 w-full inline-flex items-center rounded-lg bg-layer border border-layer-line shadow-xs`
- **KBD Shortcut:** `py-px px-1.5 border border-line-2 rounded-md text-[11px]`

## 5. Global Styles
- **Border Radius:**
    - `rounded-lg`: Standard for cards, buttons, and inputs.
    - `rounded-xl`: Used for floating dropdowns and modals.
    - `rounded-full`: Used for avatars and pill-style badges.
- **Shadows:**
    - `shadow-xs`: Default for persistent surface elements.
    - `shadow-xl`: Elevated states for dropdown menus.
- **Transitions:** `transition-all duration-300` (Applied to sidebar and interactive overlays).
- **Interactive States:** Uses custom semantic hover/focus classes: `hover:bg-muted-hover`, `focus:bg-muted-focus`.