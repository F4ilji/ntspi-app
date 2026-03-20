import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { createSvgIconsPlugin } from 'vite-plugin-svg-icons'
import path from 'path'



export default defineConfig({
    build: {
        manifest: "manifest.json",
        minify: 'terser', // Включаем минификатор Terser
        terserOptions: {
            compress: {
                drop_console: true, // Удаляем все console.log
            },
        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
        createSvgIconsPlugin({
            // Specify the icon folder to be cached
            iconDirs: [path.resolve(process.cwd(), 'resources/js/assets/icons/svg')],

            symbolId: 'icon-[name]',

            customDomId: '__svg__icons__dom__',
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
        noExternal: ['lodash', 'bvi', 'preline', 'vue3-yandex-smartcaptcha', 'vuex'],
    },
});

