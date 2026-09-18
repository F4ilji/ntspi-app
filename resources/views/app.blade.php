<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (env('NGINX_ENV') === 'prod')
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif
    {{--    <title inertia>{{ config('app.name', 'Laravel') }}</title>--}}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.ico">
    <link rel="stylesheet" href="/css/bvi.min.css">

    <!-- Scripts -->
    @routes

    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<style>
    a, button {
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0) !important;
        -webkit-tap-highlight-color: transparent !important;
    }

    #nprogress .bar {
        z-index: 10000000!important;
    }

    .bvi-active {
        padding: 0;
        margin: 0;
    }

    .ce-header {
        font-weight: bold;
        font-size: 20px;
    }

    body .bvi-body {
        padding: 0;
    }

    *, html {
        scroll-padding: 6rem;
        scroll-behavior: smooth !important;
    }
</style>
<body class="">
@inertia

<div id="consent-banner" style="display:none; position:fixed; bottom:0; inset-x:0; z-index:9999; padding:1rem;">
  <div style="max-width:48rem; margin:0 auto; background:#fff; border:1px solid #e5e7eb; border-radius:0.5rem; box-shadow:0 4px 6px -1px rgb(0 0 0 / 0.1); padding:1.25rem; display:flex; flex-direction:column; gap:1rem; align-items:flex-start;" class="sm:flex-row sm:items-center sm:justify-between">
    <div style="flex:1;">
      <h3 style="font-size:0.875rem; font-weight:600; color:#111827; margin:0 0 0.25rem;">Cookie и аналитика</h3>
      <p style="font-size:0.75rem; color:#6b7280; line-height:1.5; margin:0;">
        Мы собираем анонимную статистику посещений для улучшения качества сайта. Данные не передаются третьим лицам.
      </p>
    </div>
    <div style="display:flex; gap:0.5rem; flex-shrink:0;">
      <button onclick="declineConsent()" style="padding:0.375rem 0.75rem; font-size:0.75rem; font-weight:500; color:#6b7280; background:transparent; border:1px solid #e5e7eb; border-radius:0.375rem; cursor:pointer;">Отклонить</button>
      <button onclick="acceptConsent()" style="padding:0.375rem 0.75rem; font-size:0.75rem; font-weight:500; color:#fff; background:#1E57A3; border:none; border-radius:0.375rem; cursor:pointer;">Принять</button>
    </div>
  </div>
</div>
<script>
(function() {
    var KEY = 'analytics_consent';
    var banner = document.getElementById('consent-banner');
    if (!banner) return;
    var path = window.location.pathname;
    if (path.startsWith('/dashboard') || path.startsWith('/admin')) return;
    if (!localStorage.getItem(KEY)) {
        banner.style.display = 'block';
    }
})();
function acceptConsent() {
    localStorage.setItem('analytics_consent', 'granted');
    document.getElementById('consent-banner').style.display = 'none';
    if (typeof window._ntspiTrackHit === 'function') {
        window._ntspiTrackHit();
    }
}
function declineConsent() {
    localStorage.setItem('analytics_consent', 'denied');
    document.getElementById('consent-banner').style.display = 'none';
}
</script>

</body>
</html>