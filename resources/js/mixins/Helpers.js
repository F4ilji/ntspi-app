import slugify from "slugify";
import process from "@inertiajs/inertia-vue3/.eslintrc.js";

export const helpers = {
    methods: {
        HAS_ACTIVE_PAGE(section) {
            if (!this.$page?.props?.ziggy) return false;

            if (section.pages) {
                return section.pages.some(page => this.IS_SAME_ROUTE(page.path));
            }
            if (section.subSections) {
                return section.subSections.some(subSection => this.HAS_ACTIVE_PAGE(subSection));
            }
            return false;
        },

        IS_SAME_ROUTE(route) {
            if (!this.$page?.props?.ziggy) return false;

            const currentLocation = this.$page.props.ziggy.location;
            if (!currentLocation) return false;

            if (route === currentLocation) {
                return true;
            }

            try {
                const currentUrl = this.$page.props.ziggy.url + '/' + route;
                return currentLocation === currentUrl;
            } catch {
                return false;
            }
        },

        TEXT_LIMIT(text, symbols) {
            if (!text) return '';
            if (typeof text !== 'string') return String(text);

            return text.length > symbols
                ? text.substring(0, symbols) + "..."
                : text;
        },

        GENERATE_SLUG(text) {
            if (typeof text !== 'string') return '';

            try {
                return slugify(text, {
                    lower: true,
                    strict: true,
                    locale: 'ru'
                });
            } catch {
                return text.toLowerCase().replace(/\s+/g, '-');
            }
        },

        GET_BASE_URL() {
            if (typeof window === 'undefined') {
                return process.env.APP_URL || '/';  // Используем env-переменную на сервере
            }
            return window.location.origin + '/';
        }
    }
};