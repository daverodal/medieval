let mix = require('laravel-mix');
let webpack = require('webpack');

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
mix.webpackConfig({
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            'window.$': 'jquery',
            Popper: ['popper.js', 'default']
        })
    ]});

mix.copyDirectory('../wargaming/Wargame/wargame-helpers', 'wargame-helpers/imported');



// mix.copy("../wargaming/Wargame/wargame-helpers/fix-header.js", "wargame-helpers/imported/fix-header.js");//js('Medieval/medieval.js', 'javascripts/medieval/medieval.js');
// mix.copy("../wargaming/Wargame/wargame-helpers/global-funcs.js", "wargame-helpers/imported/global-funcs.js");
// mix.copy("../wargaming/Wargame/wargame-helpers/jquery.panzoom.js", "wargame-helpers/imported/jquery.panzoom.js");
// mix.copy("../wargaming/Wargame/wargame-helpers/Sync.js", "wargame-helpers/imported/Sync.js");
//
mix.setPublicPath("../game-dispatcher/public/vendor/");
mix.setResourceRoot("/vendor/");
mix.js('Medieval/medieval.js', 'javascripts/medieval/medieval.js');
mix.sass('Medieval/Civitate1053/all.scss', 'css/medieval/civitate1053.css')
mix.sass('Medieval/Grunwald1410/all.scss', 'css/medieval/grunwald1410.css')
mix.sass('Medieval/Lewes1264/all.scss', 'css/medieval/lewes1264.css')
mix.sass('Medieval/Arsouf1191/all.scss', 'css/medieval/arsouf1191.css')
mix.sass('Medieval/Plowce1331/all.scss', 'css/medieval/plowce1331.css')
mix.sass('Medieval/WayBack/all.scss', 'css/medieval/wayback.css')
