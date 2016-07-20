var paths = {
  'SOURCE': './resources/assets/',
  'DESTINATION': './public/assets/',
  'NODE': './node_modules/'
};

var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {

  mix.sass('app.scss', paths.DESTINATION + 'css', {
    includePaths: [
      paths.NODE + 'foundation-sites/scss',
    ]
  });

  mix.scripts([
        paths.NODE + 'jquery/dist/jquery.js',
        paths.NODE + 'foundation-sites/dist/foundation.js'
  ], paths.DESTINATION + 'js/vendor.js', './');

});
