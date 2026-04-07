import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { createSSRApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { default as Router } from '../../vendor/tightenco/ziggy/dist/index.m.js';

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

            ssrApp.config.errorHandler = () => null;
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

            // Создаем функцию route с правильной конфигурацией
            const route = (name, params, absolute, config) => {
                const ziggyConfigForRoute = {
                    ...page.props.ziggy,
                    location: ziggyConfig.location,
                };
                const router = new Router(name, params, absolute, ziggyConfigForRoute);
                return router.toString();
            };

            // Делаем route доступным глобально
            ssrApp.config.globalProperties.route = route;

            return ssrApp
                .use(plugin)
                .use(ZiggyVue, ziggyConfig);
        },
    }),
);