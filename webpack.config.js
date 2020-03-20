/**
 * Webpack Configuration File
 * @type {[type]}
 */

// /////////////////////////////////////////////////////////////////////////////
// Requires / Dependencies /////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

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
     "index": path.resolve(__dirname, srcSass, "index.scss"),
   },

  // Where to put build?
   output: {
     filename: "[name].js",
     path: path.resolve(__dirname, distJS)
   },

  // Additional module rules.
  module: {
    rules: [
      // Drupal behaviors need special handling with webpack.
      // https://www.npmjs.com/package/drupal-behaviors-loader
      {
        test: /\.behavior.js$/,
        exclude: /node_modules/,
        options: {
          enableHmr: false
        },
        loader: 'drupal-behaviors-loader'
      },
      // Good ol' Babel.
      {
        test: /\.js$/,
        loader: 'babel-loader',
        query: {
          presets: ['@babel/preset-env']
        }
      },
      // Apply Plugins to SCSS/SASS files.
      {
        test: /\.s[ac]ss$/,
        // use: [
        //   // Extract loader.
        //   MiniCssExtractPlugin.loader,
        //   // CSS Loader. Generate sourceMaps.
        //   {
        //     loader: 'css-loader',
        //     options: {
        //       sourceMap: true,
        //       url: true
        //     }
        //   },
        //   // Post CSS. Run autoprefixer plugin.
        //   {
        //     loader: 'postcss-loader',
        //     options: {
        //       sourceMap: true,
        //       plugins: () => [
        //         autoprefixer()
        //       ]
        //     }
        //   },
        //   // SASS Loader. Add compile paths to include bourbon.
        //   {
        //     loader: 'sass-loader',
        //     options: {
        //       includePaths: [
        //         path.resolve(__dirname, npmPackage, "bourbon/core"),
        //         path.resolve(__dirname, srcSass),
        //         path.resolve(__dirname, npmPackage)
        //       ],
        //       sourceMap: true,
        //       lineNumbers: true,
        //       outputStyle: 'nested',
        //       precision: 10
        //     }
        //   }
        // ]
      },
      // Apply plugin to font assets.
      {
        test: /\.(woff2?|ttf|otf|eot)$/,
        loader: 'file-loader',
        options: {
          name: "[name].[ext]",
          publicPath: "../assets/fonts",
          outputPath: "../assets/fonts"
        }
      },
      // Apply plugins to image assets.
      {
        test: /\.(png|jpg|gif)$/i,
        use: [
          // A loader for webpack which transforms files into base64 URIs.
          // https://github.com/webpack-contrib/url-loader
          {
            loader: "file-loader",
            options: {
              name: "[name].[ext]",
              publicPath: "../../assets/img",
              outputPath: "../../assets/img"
            }
          }
        ]
      },
      // Apply plugins to svg assets.
      {
        test: /\.(svg)$/i,
        use: [
          {
            loader: "file-loader",
            options: {
              name: "[name].[ext]",
              publicPath: "../../assets/svg",
              outputPath: "../../assets/svg"
            }
          }
        ]
      },
    ]
  },


}

module.exports = env => {
  if (!env) {
    throw new Error(buildValidations.ERR_NO_ENV_FLAG);
  }

  const envConfig = require(`./js/build-utils/webpack.${env.env}.js`);
  const mergedConfig = webpackMerge(
    commonConfig,
    envConfig,
    webpackConfig,
    ...addons(env.addons)
  );

  return mergedConfig;
};


