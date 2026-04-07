import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { createSSRApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { default as Router } from '../../vendor/tightenco/ziggy/dist/index.m.js';
import { createRouteMixin } from './mixins/RouteMixin.js';
import { createSsrErrorHandler, logSsrError } from './ssr/errorHandler.js';

createServer(page =>
    createInertiaApp({
        page,
        render: renderToString,
        resolve: name => {
            const pages = import.meta.glob('./Pages/**/*.vue')
            return pages[`./Pages/${name}.vue`]()
        },
        setup({ App, props, plugin }) {
            const ssrApp = createSSRApp({
                render: () => h(App, props),
            });

            // SSR error handler - logs to separate file
            const ssrErrorHandler = createSsrErrorHandler();
            ssrApp.config.errorHandler = ssrErrorHandler;
            ssrApp.config.warnHandler = () => null;

            const ziggyConfig = {
                ...page.props.ziggy,
            };

            // SSR-safe location handling
            if (page.props.ziggy?.location) {
                ziggyConfig.location = new URL(page.props.ziggy.location);
            } else {
                ziggyConfig.location = new URL(process.env.APP_URL || 'http://localhost');
            }

            // Добавляем route mixin для всех компонентов
            const routeMixin = createRouteMixin(page, Router);
            ssrApp.mixin(routeMixin);

            return ssrApp
                .use(plugin)
                .use(ZiggyVue, ziggyConfig);
        },
    }).catch(error => {
        // Catch Inertia SSR errors and log them separately
        logSsrError(error);
        // Re-throw to maintain normal error flow
        throw error;
    })
);