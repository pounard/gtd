const common = require('./webpack.common.js');
const merge = require('webpack-merge');
const webpack = require('webpack');

module.exports = merge(common, {
  devtool: 'inline-source-map',
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'NODE_ENV': JSON.stringify('development')
      }
    }),
  ]
});
