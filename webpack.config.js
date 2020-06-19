const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

const publicPath = `/src/Backend/Core/build/`

module.exports = {
  mode: 'development',
  entry: './src/Backend/Core/Js/backend.js',
  output: {
    filename: 'bundle.js',
    path: path.resolve(__dirname, './src/Backend/Core/build'),
    publicPath: publicPath
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: 'babel-loader'
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
          'sass-loader' // compiles Sass to CSS, using Node Sass by default
        ]
      },
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
      }
    ]
  },
  plugins: [
    // Lightweight CSS extraction plugin built on top of features available in Webpack v4 (performance!).
    new MiniCssExtractPlugin({
      filename: '[name].[contenthash].css',
      chunkFilename: '[id].css',
    })
  ]
}
