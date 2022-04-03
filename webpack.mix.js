const mix = require('laravel-mix');

require('laravel-mix-purgecss');

mix
  .js('resources/js/app.js', 'public/js')
  .copyDirectory('resources/img', 'public/img')
  .postCss('resources/css/app.css', 'public/css', [require('tailwindcss')])
  .version()
  .sourceMaps();
