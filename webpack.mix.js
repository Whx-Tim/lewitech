const { mix } = require('laravel-mix');

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

// mix.js('resources/assets/js/app.js', 'public/js')
//    .sass('resources/assets/sass/app.scss', 'public/css');
// mix.sass('resources/assets/sass/active/list.scss', 'public/css/active')
    // .sass('resources/assets/sass/active/detail.scss', 'public/css/active')
    // .sass('resources/assets/sass/active/publish.scss', 'public/css/active')
    // .sass('resources/assets/sass/active/apply.scss', 'public/css/active')
    // .sass('resources/assets/sass/admin/app.scss', 'public/css/admin.css')

    // mix.sass('resources/assets/sass/active/detail.scss', 'public/css/active');
    // mix.sass('resources/assets/sass/active/publish.scss', 'public/css/active');
    // mix.sass('resources/assets/sass/active/apply.scss', 'public/css/active');
mix.sass('resources/assets/sass/palette/main.scss', 'public/css/palette')
    // .sass('resources/assets/sass/palette/page-file.scss', 'public/css/palette')
    // .sass('resources/assets/sass/palette/page-clip.scss', 'public/css/palette')
    // .sass('resources/assets/sass/palette/page-card.scss', 'public/css/palette')
    // .sass('resources/assets/sass/palette/page-sky.scss', 'public/css/palette')
    // .sass('resources/assets/sass/palette/page-palette.scss', 'public/css/palette')
    // .sass('resources/assets/sass/palette/page.scss', 'public/css/palette')
    .options({
        processCssUrls: false
    });