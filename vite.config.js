import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/style.scss', 'resources/js/main.js'],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ['legacy-js-api', 'color-functions', 'global-builtin', 'import'],
            },
        },
    },
});
