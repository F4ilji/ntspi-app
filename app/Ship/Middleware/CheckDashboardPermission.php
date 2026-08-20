<?php

namespace App\Ship\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckDashboardPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();

        if (!$routeName || !str_starts_with($routeName, 'dashboard.')) {
            return $next($request);
        }

        $permission = $this->resolvePermission($routeName);

        if ($permission && !$request->user()?->hasPermissionTo($permission)) {
            abort(403, 'Доступ запрещён. Необходимо право: ' . $permission);
        }

        return $next($request);
    }

    private function resolvePermission(string $routeName): ?string
    {
        // dashboard.posts.index → posts
        // dashboard.additional-educations.index → additional_education
        // dashboard.admission-campaigns.store → admission_campaign
        // dashboard.departments.{faculty}/workers.index → department
        // dashboard.vikon-updates.index → null (no permission)
        // dashboard.integration-credentials.index → null (no permission)

        // Strip 'dashboard.' prefix and last segment (index/create/store/edit/update/destroy)
        $parts = explode('.', $routeName);

        if (count($parts) < 2) {
            return null;
        }

        // Skip action segments (index, create, store, edit, update, destroy, etc.)
        $actions = ['index', 'create', 'store', 'edit', 'update', 'destroy', 'show',
            'ai-prepared', 'parse-email', 'bulk-destroy', 'bulk-publish', 'bulk-verification',
            'upload-images', 'publish', 'invite', 'order', 'toggle-checked',
            'attach', 'detach', 'attach-to-main-section', 'detach-from-main-section',
            'attachPage', 'detachPage', 'reorderPages', 'upload-files'];

        // Filter out action segments and numeric/parameter segments
        $resourceParts = [];
        foreach ($parts as $i => $part) {
            if ($i === 0) continue; // skip 'dashboard'
            if (in_array($part, $actions)) continue;
            if (is_numeric($part)) continue;
            if (str_starts_with($part, '{')) continue;
            $resourceParts[] = $part;
        }

        if (empty($resourceParts)) {
            return null;
        }

        // Known resources without permission check
        $noPermission = ['vikon-updates', 'integration-credentials'];
        $resource = $resourceParts[0];
        if (in_array($resource, $noPermission)) {
            return null;
        }

        // Convert: additional-educations → additional_education
        // Convert: admission-campaigns → admission_campaign
        // Convert: main-sections → main_section
        // Convert: faculties → faculty, categories → category
        $resource = Str::singular($resource);
        $resource = str_replace('-', '_', $resource);

        return 'view_any_' . $resource;
    }
}
