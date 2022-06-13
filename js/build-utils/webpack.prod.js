const commonPaths = require('./common-paths');
const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const config = {
  mode: 'production',
  entry: {
    app: [`${commonPaths.appEntry}/index.js`],
    'react_paragraphs.field_widget': [`${commonPaths.drupalEntry}/field_widget.scss`],
    'react_paragraphs.field_formatter': [`${commonPaths.drupalEntry}/field_formatter.scss`]
  },
  output: {
    filename: 'react_paragraphs.[name].min.js',
    path: path.resolve(__dirname, '../build')
  },
  devtool: 'source-map',
  module: {
    rules: [
      {
        test: /scss\/.*\.s[ac]ss$/,
        use: [
          // Extract loader.
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: '/css/'
            }
          },
          // CSS Loader. Generate sourceMaps.
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
              url: true
            }
          },
          // Post CSS. Run autoprefixer plugin.
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins: () => [require('autoprefixer')({
                  browsers: ['> 1%', 'last 2 versions']
                })],
              }
            }
          },
          // SASS Loader. Add compile paths to include bourbon.
          {
            loader: 'sass-loader',
            options: {
              sassOptions: {
                includePaths: [
                  path.resolve(__dirname, 'node_modules/')
                ],
                sourceMap: true,
                lineNumbers: true,
                outputStyle: 'nested',
                precision: 10
              }
            }
          }
        ]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'css/[name].css'
    })
  ]
};

module.exports = config;
