import './bootstrap';
import '../css/app.css';
import 'preline';

import { createSSRApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import {linksReform} from "@/mixins/LinksReform.js";

// const appName = import.meta.env.VITE_APP_NAME || 'НТГСПИ';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue')
        return pages[`./Pages/${name}.vue`]()
        // resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))
    },
    setup({ el, App, props, plugin }) {
        return createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .mixin(linksReform)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#1E57A3',
        delay: 250,
    },
});


