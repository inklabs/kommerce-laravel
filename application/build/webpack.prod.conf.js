var autoprefixer = require('autoprefixer');
var baseWebpackConfig = require('./webpack.base.conf');
var CleanWebpackPlugin = require('clean-webpack-plugin');
var merge = require('webpack-merge');
var path = require('path');
var projectRoot = path.resolve(__dirname, '../');
var webpack = require('webpack');

module.exports = merge(baseWebpackConfig, {
  // cache: false,
  debug: false,
  devtool: null,
  plugins: [
    new CleanWebpackPlugin(['public/assets'], {
      root: projectRoot,
      verbose: false
    }),
    new webpack.optimize.DedupePlugin(),
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        warnings: false
      },
      mangle: false,
      sourcemap: false
    })
  ],
  postcss: function () {
    return [
      autoprefixer({
        browsers: ['last 2 versions', 'ie >= 9', 'and_chr >= 2.3']
      })
    ];
  }
});