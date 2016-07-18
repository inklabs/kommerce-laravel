var argv = require('argv');
var baseWebpackConfig = require('./webpack.base.conf');
var merge = require('webpack-merge');
var webpack = require('webpack');

module.exports = merge(baseWebpackConfig, {
  // cache: false,
  // watch: true,
  debug: true,
  devtool: '#eval-source-map' // eval-source-map is faster for development
});
