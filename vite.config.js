import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    build: {
        manifest: "manifest.json",
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            content: [
                "./vendor/vormkracht10/filament-2fa/resources/**.*.blade.php",
            ],
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),

        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: 'localhost',
        hmr: {
            clientPort: 5173,
            host: 'localhost',
            protocol: 'ws'
        },
        port: 5173,
        watch: {
            usePolling: true
        }
    },
    ssr: {
        noExternal: ['lodash', 'bvi', 'preline'],
    },
});

