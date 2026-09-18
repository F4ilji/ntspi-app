import './bootstrap';
import '../css/app.css';
import 'preline';
import 'virtual:svg-icons-register'


import { createSSRApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import {linksReform} from "@/mixins/LinksReform.js";
import cookieMixin from "@/mixins/cookieMixin.js";
import {helpers} from "@/mixins/Helpers.js";
import store from '@/store/index.js';
import '@vuepic/vue-datepicker/dist/main.css';
import { initAnalytics } from '@/services/analytics.js';

// Load TinyMCE globally before app initialization
const loadTinyMCE = () => {
    return new Promise((resolve) => {
        if (typeof tinymce !== 'undefined') {
            resolve();
            return;
        }
        const script = document.createElement('script');
        script.src = '/vendor/tinymce/tinymce.min.js';
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => {
            console.error('Failed to load TinyMCE');
            resolve();
        };
        document.head.appendChild(script);
    });
};

loadTinyMCE().then(() => {
    createInertiaApp({
        resolve: name => {
            const pages = import.meta.glob('./Pages/**/*.vue')
            const page = pages[`./Pages/${name}.vue`]
            if (!page) {
                console.error(`Page not found: ${name}`)
                return pages['./Pages/Error.vue']()
            }
            return page()
        },
        setup({ el, App, props, plugin }) {
            const app = createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(store)
                .mixin(linksReform)
                .mixin(cookieMixin)
                .mixin(helpers)
                .use(ZiggyVue)
                .mount(el);

            initAnalytics();

            return app;
        },
        progress: {
            color: '#1E57A3',
            delay: 250,
        },
    });
});