export const linksReform = {
    data() {
        return {
            excludedPaths: ['/sveden/', '/storage/']
        }
    },
    methods: {
        checkPathname(pathname) {
            return this.excludedPaths.some(path => pathname.startsWith(path));
        },
        getDefaultLinks() {
            const links = document.querySelectorAll('a:not([href^="#"]):not([download])');
            return Array.from(links);
        },
        createReactiveLinks(links, hostname) {
            return links.filter(link => link.hostname === hostname).filter(link => !this.checkPathname(link.pathname));
        },
        createStaticLinks(links, hostname) {
            return links.filter(link => link.hostname !== hostname);
        },
        clearBodyStyles() {
            const body = document.body;
            if (body.style.overflow === 'hidden') {
                body.style.overflow = ''; // Сбрасываем overflow до значения по умолчанию
            }
        },
    },
    mounted() {
        const hostname = window.location.hostname;
        const defaultLinks = Array.from(this.getDefaultLinks());

        const reactiveLinks = this.createReactiveLinks(defaultLinks, hostname);

        reactiveLinks.forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                this.clearBodyStyles();
                const url = link.getAttribute('href');
                this.$inertia.visit(url, {
                    preserveScroll: false,
                });
            });
        });
    }
};