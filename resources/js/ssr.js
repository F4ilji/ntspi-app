import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { createSSRApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
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

            const ssrErrorHandler = createSsrErrorHandler();
            ssrApp.config.errorHandler = ssrErrorHandler;
            ssrApp.config.warnHandler = () => null;

            const ziggyConfig = {
                ...page.props.ziggy,
            };

            if (page.props.ziggy?.location) {
                ziggyConfig.location = new URL(page.props.ziggy.location);
            } else {
                ziggyConfig.location = new URL(process.env.APP_URL || 'http://localhost');
            }

            return ssrApp
                .use(plugin)
                .use(ZiggyVue, ziggyConfig);
        },
    }).catch(error => {
        logSsrError(error);
        throw error;
    })
);