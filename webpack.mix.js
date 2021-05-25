const mix = require('laravel-mix');
const path = require('path');

mix.alias({
    '@js': path.join(__dirname, 'resources/js'),
    '@css': path.join(__dirname, 'resources/css'),
    '@scss': path.join(__dirname, 'resources/scss'),
    '@fonts': path.join(__dirname, 'resources/fonts'),
    '@views': path.join(__dirname, 'resources/js/views'),
    '@components': path.join(__dirname, 'resources/js/components')
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.override(webpackConfig => {
    // BUG: vue-loader doesn't handle file-loader's default esModule:true setting properly causing
    // <img src="[object module]" /> to be output from vue templates.
    // WORKAROUND: Override mixs and turn off esModule support on images.
    // FIX: When vue-loader fixes their bug AND laravel-mix updates to the fixed version
    // this can be removed
    webpackConfig.module.rules.forEach(rule => {
        if (
            rule.test.toString() ===
            '/(\\.(png|jpe?g|gif|webp)$|^((?!font).)*\\.svg$)/'
        ) {
            if (Array.isArray(rule.use)) {
                rule.use.forEach(ruleUse => {
                    if (ruleUse.loader === 'file-loader') {
                        ruleUse.options.esModule = false;
                    }
                });
            }
        }
    });
});

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .js('resources/js/app-admin.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css')
    .postCss('resources/css/app-admin.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer')
    ])
    .extract();
