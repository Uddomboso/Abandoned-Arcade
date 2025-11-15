// vite configuration file
// configures asset bundling and compilation for laravel application
// processes scss and javascript files for production

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // vite plugins
    plugins: [
        // laravel vite plugin for blade integration
        laravel({
            // entry points for asset compilation
            input: [
                'resources/sass/app.scss',  // main stylesheet
                'resources/js/app.js',        // main javascript
            ],
            // enable hot module replacement for development
            refresh: true,
        }),
    ],
});
