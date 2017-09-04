const CleanWebpackPlugin = require('clean-webpack-plugin');
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const path = require('path');
const webpack = require('webpack');

const distDirectory = path.resolve(__dirname, 'web/dist');

// Extract CSS into their own files
const extractSass = new ExtractTextPlugin({
  filename: "css/style.min.css",
  disable: process.env.NODE_ENV !== "production"
});

module.exports = {
  entry: {
    main: [
      './app/Resources/public/js/app.js',
      './app/Resources/public/scss/app.scss'
    ]
  },
  output: {
    filename: 'js/script.min.js',
    path: distDirectory
  },
  plugins: [
    new CleanWebpackPlugin(['dist']),
    // Bootstrap 4 needs this
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
      Popper: ['popper.js', 'default']
    }),
    extractSass
  ],
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader?cacheDirectory=true',
          options: {
            presets: ['env'],
            plugins: ['transform-runtime']
          }
        }
      },
      {
        test: /\.js$/,
        enforce: "pre", // preload the jshint loader
        exclude: /node_modules/,
        use: [{
          loader: "jshint-loader",
          options: {
            esversion: 6
          }
        }]
      },
      {
        test: /\.scss$/,
        use: extractSass.extract({
          use: [{
            loader: "css-loader"
          }, {
            loader: "sass-loader"
          }],
          fallback: "style-loader" // use style-loader in development
        })
      },
      {
        test: /.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
        use: [{
          loader: 'file-loader',
          options: {
            name: 'fonts/[name].[ext]',
            path: distDirectory,
            publicPath: (process.env.NODE_ENV === "production" ? '../fonts' : 'dist/')
          }
        }]
      }
    ]
  }
};
