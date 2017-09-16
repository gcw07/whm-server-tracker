let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
    .extract(['axios', 'buefy', 'laravel-echo', 'moment', 'pusher-js', 'promise', 'vue'])
    .copy('resources/assets/js/fontawesome/fontawesome.js', 'public/js/fontawesome.js')
    .copy('resources/assets/js/fontawesome/packs/brands.js', 'public/js/fontawesome-brands.js')
    .copy('resources/assets/js/fontawesome/packs/light.js', 'public/js/fontawesome-light.js')
    .copy('resources/assets/js/fontawesome/packs/regular.js', 'public/js/fontawesome-regular.js')
    .copy('resources/assets/js/fontawesome/packs/solid.js', 'public/js/fontawesome-solid.js')
    .sass('resources/assets/sass/app.scss', 'public/css');

if (mix.config.inProduction) {
    mix.version();
}
