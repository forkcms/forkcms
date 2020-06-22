const webpack = require('webpack')
const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')

const publicPath = `/src/Backend/Core/build/`

module.exports = {
  mode: 'development',
  entry: './src/Backend/Core/Js/main.js',
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, './src/Backend/Core/build'),
    publicPath: publicPath
  },
  watch: true,
  module: {
    rules: [
      {
        test: /\.(png|svg|jpg|gif)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              outputPath: 'images'
            }
          }
        ]
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      },
      {
        test: /\.(css|scss)$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: publicPath
            }
          },
          'css-loader', // translates CSS into CommonJS
          'postcss-loader', // Apply PostCSS plugins defined in postcss.config.js
          'resolve-url-loader',
          'sass-loader' // compiles Sass to CSS, using Node Sass by default
        ]
      }
    ]
  },
  plugins: [
    new CleanWebpackPlugin(),
    // Lightweight CSS extraction plugin built on top of features available in Webpack v4 (performance!).
    new MiniCssExtractPlugin({
      filename: '[name].css',
      chunkFilename: '[id].css'
    }),

    new webpack.ProvidePlugin({
      jQuery: 'jquery',
      $: 'jquery',
      'window.jQuery': 'jquery',
      'window.$': 'jquery'
    })
  ]
}
