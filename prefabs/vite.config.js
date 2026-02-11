import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import hoverMediaFeature from 'postcss-hover-media-feature';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [hoverMediaFeature()],
        },
    },
});
