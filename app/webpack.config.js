const Encore = require('@symfony/webpack-encore');
const TsconfigPathsPlugin = require("tsconfig-paths-webpack-plugin");

// @see https://symfony.com/doc/current/frontend.html
Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    //.setManifestKeyPrefix('build/') // For CDN (useless now)
    .addEntry('app', './assets/index.ts')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .enableTypeScriptLoader()
;

const config = Encore.getWebpackConfig();

// Allow --profile to work
delete config.stats;

// const util = require('util');
// console.log(util.inspect(config, false, null, true /* enable colors */));

config.resolve.plugins = [new TsconfigPathsPlugin()];

module.exports = config;
