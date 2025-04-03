import slugify from "slugify";

export const helpers = {
    data() {

    },
    methods: {
        HAS_ACTIVE_PAGE(section) {
            if (section.pages) {
                return section.pages.some(page => this.IS_SAME_ROUTE(page.path));
            }
            if (section.subSections) {
                return section.subSections.some(subSection => this.HAS_ACTIVE_PAGE(subSection));
            }
        },
        IS_SAME_ROUTE(route) {
            if (route === this.$page.props.ziggy.location) {
                return true;
            }
            const currentLocation = this.$page.props.ziggy.location;
            const currentUrl = this.$page.props.ziggy.url + '/' + route;
            return currentLocation === currentUrl;
        },
        TEXT_LIMIT(text, symbols) {
            if (text.length > symbols) {
                let LimitedText
                LimitedText = text.substring(0, symbols)
                return LimitedText + "..."
            }
            return text
        },
        GENERATE_SLUG: function (text) {
            return slugify(text, {
                lower: true,
                strict: true,
                locale: 'ru'
            });
        },
        GET_BASE_URL() {
            return window.location.origin + '/';
        }
    },
};