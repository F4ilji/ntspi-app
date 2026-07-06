if (typeof globalThis.IntersectionObserver === 'undefined') {
    globalThis.IntersectionObserver = class {
        constructor() {}
        observe() {}
        unobserve() {}
        disconnect() {}
    };
}

if (typeof globalThis.localStorage === 'undefined') {
    const noop = () => '';
    globalThis.localStorage = { getItem: noop, setItem: noop, removeEventListener: noop, removeItem: noop, clear: noop, get length() { return 0; }, key: noop };
}

if (typeof globalThis.sessionStorage === 'undefined') {
    const noop = () => '';
    globalThis.sessionStorage = { getItem: noop, setItem: noop, removeItem: noop, clear: noop, get length() { return 0; }, key: noop };
}

// NOTE: Do NOT polyfill globalThis.window — Inertia checks `typeof window === "undefined"` to detect SSR

if (typeof globalThis.document === 'undefined') {
    const el = { style: {}, appendChild: () => {}, removeChild: () => {}, addEventListener: () => {}, removeEventListener: () => {} };
    globalThis.document = {
        createElement: () => el,
        querySelector: () => null,
        querySelectorAll: () => [],
        getElementById: () => null,
        createTextNode: () => el,
        createComment: () => el,
        createDocumentFragment: () => el,
        addEventListener: () => {},
        removeEventListener: () => {},
        head: { appendChild: () => {}, removeChild: () => {} },
        body: { appendChild: () => {}, removeChild: () => {}, addEventListener: () => {}, removeEventListener: () => {} },
    };
}

import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { createSSRApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { Ziggy } from './ziggy';
import { createSsrErrorHandler, logSsrError } from './ssr/errorHandler.js';

const EMPTY_RESPONSE = { head: [], body: '' };

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
                ...Ziggy,
                ...page.props.ziggy,
                location: page.props.ziggy?.location
                    ? new URL(page.props.ziggy.location)
                    : new URL(process.env.APP_URL || 'http://localhost'),
            };

            return ssrApp
                .use(plugin)
                .use(ZiggyVue, ziggyConfig);
        },
    }).then(result => result ?? EMPTY_RESPONSE).catch(error => {
        logSsrError(error);
        return EMPTY_RESPONSE;
    })
);