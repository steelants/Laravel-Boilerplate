import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import hoverMediaFeature from 'postcss-hover-media-feature';
import prefixCustomProperties from 'postcss-prefix-custom-properties';

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
    build: {
        // Ace loads its modes and workers on demand, the biggest ones (xquery worker, php/blade mode) exceed the default 500 kB
        chunkSizeWarningLimit: 1700,
        rollupOptions: {
            onwarn(warning, warn) {
                // eval inside ace-builds' CoffeeScript worker is vendor code we cannot change
                if (warning.code === 'EVAL' && warning.id?.includes('/ace-builds/')) {
                    return;
                }
                warn(warning);
            },
        },
    },
    css: {
        postcss: {
            plugins: [
                hoverMediaFeature(),
                // Bootstrap 6 emits unprefixed tokens (--fg-body, ...) and expects the build to add `bs-`.
                // Custom properties of other libraries have to be listed here so they keep their names.
                prefixCustomProperties({
                    prefix: 'bs-',
                    ignore: [/^--bs-/, /^--_?fa-/, /^--ql-/, /^--gantt-/],
                }),
            ],
        },
    },
});
