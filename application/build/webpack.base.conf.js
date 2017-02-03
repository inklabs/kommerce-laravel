var path = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var projectRoot = path.resolve(__dirname, '../');
var webpack = require('webpack');

module.exports = {
  path: projectRoot,
  entry: {
    'main': './resources/assets/js/main.js',
  },
  output: {
    path: projectRoot + '/public/assets/',
    filename: '[name].js'
  },
  resolve: {
    extensions: ['', '.js', '.json', '.vue'],
    fallback: [path.join(__dirname, '../node_modules')]

  },
  resolveLoader: {
    fallback: [path.join(__dirname, '../node_modules')]
  },
  module: {
    loaders: [
      {
        test: /\.vue$/,
        loader: 'vue'
      },
      {
        test: /\.js$/,
        loader: 'babel',
        exclude: /node_modules/,
        query: {
          presets: ['es2015'],
          compact: false
        }
      },
      {
        test: /\.scss$/,
        loader: ExtractTextPlugin.extract('style-loader', ['css-loader', 'postcss-loader', 'sass-loader'])
      },
      {
        test: /\.woff$/,
        loader: 'url-loader?limit=10000&mimetype=application/font-woff&name=[path][name].[ext]'
      },
      {
        test: /\.woff2$/,
        loader: 'url-loader?limit=10000&mimetype=application/font-woff2&name=[path][name].[ext]'
      },
      {
        test: /\.(eot|ttf|svg|gif|png)$/,
        loader: 'file-loader'
      }
    ]
  },
  plugins: [
    // new webpack.optimize.CommonsChunkPlugin('common.js'),
    new webpack.optimize.OccurenceOrderPlugin(),
    new webpack.ProvidePlugin({$: 'jquery', jQuery: 'jquery'}),
    new ExtractTextPlugin('css/[name].css', {allChunks: true})
  ]
};