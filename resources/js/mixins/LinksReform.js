// linksReform.js

export const linksReform = {
    data() {
        return {
            excludedPaths: ['/sveden/', '/storage/', '/upload/', '/panorama/', '/abitur/']
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
                body.style.overflow = '';
            }
        },
        async removeBackdropElement() {
            const backdrop = document.querySelector('div[data-hs-overlay-backdrop-template]');
            if (backdrop) {
                backdrop.style.transition = 'opacity 0.5s ease';
                backdrop.style.opacity = '0';

                await new Promise(resolve => {
                    backdrop.addEventListener('transitionend', () => {
                        backdrop.remove();
                        resolve();
                    }, { once: true });
                });
            }
        }
    },
    updated() {
        const hostname = window.location.hostname;
        const defaultLinks = Array.from(this.getDefaultLinks());

        const reactiveLinks = this.createReactiveLinks(defaultLinks, hostname);

        reactiveLinks.forEach(link => {
            link.addEventListener('click', async (event) => {

                if (this.checkPathname(link.pathname)) return;

                event.preventDefault();
                const url = link.getAttribute('href');

                try {
                    await this.removeBackdropElement();
                    this.clearBodyStyles();

                    this.$inertia.visit(url, {
                        preserveScroll: false,
                        onBefore: () => {
                            this.removeBackdropElement().catch(() => {});
                            this.clearBodyStyles();
                            return true;
                        }
                    });
                } catch (e) {
                    console.error('Error during navigation:', e);
                    this.$inertia.visit(url);
                }
            });
        });
    }
};