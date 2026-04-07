/**
 * SSR-safe route mixin
 * Provides the route() function to all Vue components during SSR
 */

export function createRouteMixin(pageProps, RouterClass) {
    return {
        methods: {
            route(name, params = {}, absolute = false) {
                try {
                    const ziggyConfig = {
                        ...pageProps.ziggy,
                        location: pageProps.ziggy?.location 
                            ? new URL(pageProps.ziggy.location) 
                            : new URL(process.env.APP_URL || 'http://localhost'),
                    };
                    
                    const router = new RouterClass(name, params, absolute, ziggyConfig);
                    return router.toString();
                } catch (error) {
                    console.error('Ziggy route error:', error.message);
                    return '#';
                }
            }
        }
    };
}
