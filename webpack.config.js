const webpack = require('webpack')
const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')

// common config
const common = {
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
            loader: MiniCssExtractPlugin.loader
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
    new MiniCssExtractPlugin({
      filename: '[name].css',
      chunkFilename: '[id].css'
    }),

    new CleanWebpackPlugin(),

    new webpack.ProvidePlugin({
      jQuery: 'jquery',
      $: 'jquery',
      'window.jQuery': 'jquery',
      'window.$': 'jquery'
    })
  ]
}

// export configs
module.exports = (env, argv) => {
  let isDevelopment = false
  if (argv.mode === 'development') isDevelopment = true

  // frontend config
  const frontend = {
    name: 'frontend',
    entry: './src/Frontend/Core/Js/Index.js',
    output: {
      path: path.resolve(__dirname, './src/Frontend/Core/build'),
      filename: isDevelopment ? 'frontend.bundle.js' : 'frontend.bundle.[contenthash].js'
    }
  }

// backend config
  const backend = {
    name: 'backend',
    entry: './src/Backend/Core/Js/Backend.js',
    output: {
      path: path.resolve(__dirname, './src/Backend/Core/build'),
      filename: isDevelopment ? 'backend.bundle.js' : 'backend.bundle.[contenthash].js'
    }
  }

  return [
    Object.assign({}, common, frontend),
    Object.assign({}, common, backend)
  ]
}
