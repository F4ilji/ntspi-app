---
feature: dashboard-error-page
status: delivered
specs: []
plans:
  - docs/compose/plans/2026-07-05-dashboard-error-page.md
branch: master
commits: a4fef0b..df6110a
---

# Dashboard Error Page — Final Report

## What Was Built

A dedicated error page for the admin dashboard that displays full error context when HTTP errors (403, 404, 500, 503) occur on dashboard routes. Previously, dashboard errors redirected to a generic error page using the public site layout (navbar + footer), which was confusing for admin users.

The new `DashboardError.vue` component renders within the Dashboard layout (sidebar + header) and shows:
- Error status code and description
- Request URL and HTTP method
- Current user info (if authenticated)
- Timestamp of the error
- Stack trace and request parameters (visible only to super_admin users in non-production environments)

## Architecture

### Components

| File | Role |
|------|------|
| `resources/js/Pages/Dashboard/DashboardError.vue` | Vue error page with full context display |
| `app/Exceptions/Handler.php` | Detects dashboard routes, renders DashboardError |
| `app/Ship/Exceptions/Handler.php` | Duplicate handler (Porto architecture) |

### Data Flow

```
Exception thrown
    ↓
Handler.php render()
    ↓
isDashboardRequest()? ──Yes──→ renderDashboardError()
    │                              ↓
    │                         Check user role
    │                              ↓
    │                         Build props (status, message, url, etc.)
    │                              ↓
    │                         Inertia::render('Dashboard/DashboardError', $props)
    │
    └──No──→ Original Error.vue (public layout)
```

### Access Control

- **All users**: See error status, description, URL, method, user info, timestamp
- **Super_admin only** (non-production): Additionally see stack trace and request parameters
- **Production**: Stack trace and params never shown (regardless of role)

## Usage

When a dashboard route returns an HTTP error (403, 404, 500, 503), the user is automatically redirected to the DashboardError page with full context. No configuration required.

**Example scenarios:**
- User navigates to `/dashboard/nonexistent-page` → 404 with URL info
- User accesses restricted route without permission → 403 with user info
- Server error occurs → 500 with timestamp

**Navigation:**
- "Назад" button: Returns to previous page (or dashboard index if no history)
- "На главную" button: Navigates to dashboard index

## Verification

1. **404 test**: `curl -s -o /dev/null -w "%{http_code}" http://localhost/dashboard/nonexistent-page` → Returns `404`
2. **Props verification**: Confirmed `status=404`, `message`, `url`, `method`, `timestamp` all present in Inertia page data
3. **Stack trace hidden**: Confirmed stack trace and params not included for unauthenticated users
4. **Public site unchanged**: Public site errors still render with original `Error.vue` layout
5. **PHP syntax**: Both Handler files pass `php -l` syntax check

## Journey Log

- [lesson] Vite manifest requires rebuild after adding new Vue components — `npm run build` needed before testing
- [lesson] Porto architecture has duplicate exception handlers (`app/Exceptions/` and `app/Ship/Exceptions/`) — both must be updated identically
