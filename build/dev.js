require('shelljs/global');

module.exports = {
  NODE_ENV: 'development'
};

var ora = require('ora');
var webpack = require('webpack');
var webpackConfig = require('./webpack.dev.conf');

var spinner = ora('Building for development...');

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