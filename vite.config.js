import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'Modules/E-Commerce/Techforge/resources/css/liquidglass.css',
                'Modules/E-Commerce/Techforge/resources/js/app.js',
                'Modules/E-Commerce/Techforge/resources/js/bootstrap.js',
                'Modules/E-Commerce/Techforge/resources/js/Category/Category.js',
                'Modules/E-Commerce/Techforge/resources/js/Common/AmbientEffects.js',
                'Modules/E-Commerce/Techforge/resources/js/Common/Preloader.js',
                'Modules/E-Commerce/Techforge/resources/js/Common/TailwindConfig.js',
                'Modules/E-Commerce/Techforge/resources/js/Common/Navbar.js',
                'Modules/E-Commerce/Techforge/resources/js/HomePage/Homepage.js',
                'Modules/E-Commerce/Techforge/resources/js/Pages/BuildOverview/BuildOverview.js',
                'Modules/E-Commerce/Techforge/resources/js/Pages/BuildPc/BuildPc.js',
                'Modules/E-Commerce/Techforge/resources/js/Pages/Configurator/Configurator.js',
                'Modules/E-Commerce/Techforge/resources/js/Pages/Search/Search.js'
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
