require('shelljs/global');

module.exports = {
  NODE_ENV: 'production'
};

var ora = require('ora');
var webpack = require('webpack');
var webpackConfig = require('./webpack.prod.conf');

var spinner = ora('Building for production...');

spinner.start();

webpack(webpackConfig, function (err, stats) {
  spinner.stop();
  if (err) throw err;
  process.stdout.write(stats.toString({
        colors: true,
        modules: false,
        children: false,
        chunks: false,
        chunkModules: false
      }) + '\n');
});