<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
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

@if(config('services.yandex_metrika.id') && app()->environment('production'))
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document,'script','https://mc.yandex.ru/metrika/tag.js', 'ym');

        ym({{ config('services.yandex_metrika.id') }}, 'init', {
            defer: true,
            webvisor:true,
            clickmap:true,
            ecommerce:"dataLayer",
            accurateTrackBounce:true,
            trackLinks:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/{{ config('services.yandex_metrika.id') }}" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
@endif

</body>
</html>