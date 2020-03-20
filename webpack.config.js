/**
 * Webpack Configuration File
 * @type {[type]}
 */

// /////////////////////////////////////////////////////////////////////////////
// Requires / Dependencies /////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////
  // CJW to remove:
const util = require('util');

const buildValidations = require('./js/build-utils/build-validations');
const commonConfig = require('./js/build-utils/webpack.common');

const webpackMerge = require('webpack-merge');

const addons = (/* string | string[] */ addonsArg) => {
  let addons = [...[addonsArg]] // Normalize array of addons (flatten)
    .filter(Boolean); // If addons is undefined, filter it out

  return addons.map(addonName => require(`./js/build-utils/addons/webpack.${addonName}.js`));
};


const path = require('path');
const webpack = require('webpack');
const autoprefixer = require('autoprefixer');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const WebpackAssetsManifest = require("webpack-assets-manifest");
const ExtraWatchWebpackPlugin = require("extra-watch-webpack-plugin");
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");

// /////////////////////////////////////////////////////////////////////////////
// Paths ///////////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

const npmPackage = 'node_modules/';
const srcDir = path.resolve(__dirname, "src");
const distDir = path.resolve(__dirname, "dist");
const srcSass = path.resolve(__dirname, process.env.npm_package_config_srcSass);
const distSass = path.resolve(__dirname, process.env.npm_package_config_distSass);
const srcJS = path.resolve(__dirname, process.env.npm_package_config_srcJS);
const distJS = path.resolve(__dirname, process.env.npm_package_config_distJS);
const srcAssets = path.resolve(__dirname, process.env.npm_package_config_srcAssets);
const distAssets = path.resolve(__dirname, process.env.npm_package_config_distAssets);

// /////////////////////////////////////////////////////////////////////////////
// Config //////////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

// Start configuring webpack.
var webpackConfig = {
   // What am i?
   name: 'react_paragraphs',
   // Allows for map files.
   devtool: 'source-map',
  // What to build?
   entry: {
     "index":         path.resolve(__dirname, srcSass, "index.scss"),
   },

  // Where to put build?
   output: {
     filename: "[name].js",
     path: path.resolve(__dirname, distJS)
   },
}

module.exports = env => {
  if (!env) {
    throw new Error(buildValidations.ERR_NO_ENV_FLAG);
  }

  console.log(util.inspect(webpackConfig, {depth: null}));

  const envConfig = require(`./js/build-utils/webpack.${env.env}.js`);
  const mergedConfig = webpackMerge(
    commonConfig,
    envConfig,
    webpackConfig,
    ...addons(env.addons)
  );
  console.log(util.inspect(mergedConfig, {depth: null}));

  return mergedConfig;
};


