import './bootstrap';
import '../css/app.css';
import 'preline';
import 'virtual:svg-icons-register'


import { createSSRApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import {linksReform} from "@/mixins/LinksReform.js";
import YSmartCaptcha from 'vue3-yandex-smartcaptcha'
import cookieMixin from "@/mixins/cookieMixin.js";
import store from '@/store/index.js';
import '@vuepic/vue-datepicker/dist/main.css';



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
            .use(store)
            .mixin(linksReform)
            .mixin(cookieMixin)
            .use(ZiggyVue)
            .use(YSmartCaptcha, {
                siteKey: "ysc1_jxb2IcslgBuHgHbmmKY1F7Nvb8zYHuAeBgjoaTka6886f893",
                lang: "Язык ('ru' | 'en' | 'be' | 'kk' | 'tt' | 'uk' | 'uz' | 'tr')"
            })
            .mount(el);
    },
    progress: {
        color: '#1E57A3',
        delay: 250,
    },
});


