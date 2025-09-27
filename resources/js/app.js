import './bootstrap';
import '../css/app.css';
import 'preline';
import 'virtual:svg-icons-register'


import { createSSRApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import {linksReform} from "@/mixins/LinksReform.js";
import cookieMixin from "@/mixins/cookieMixin.js";
import store from '@/store/index.js';
import '@vuepic/vue-datepicker/dist/main.css';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue')
        return pages[`./Pages/${name}.vue`]()
    },
    setup({ el, App, props, plugin }) {
        return createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(store)
            .mixin(linksReform)
            .mixin(cookieMixin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#1E57A3',
        delay: 250,
    },
});

router.on('navigate', (event) => {
    const metrikaId = event.detail.page.props.yandex_metrika_id;

    if (metrikaId && typeof ym === 'function') {
        ym(metrikaId, 'hit', window.location.href, {
            title: document.title,
            referer: event.detail.page.props.ziggy?.previous_url || document.referrer,
        });
    }
});