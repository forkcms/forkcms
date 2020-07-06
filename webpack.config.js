const webpack = require('webpack')
const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

// common config
const common = {
  mode: 'development',
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

    new webpack.ProvidePlugin({
      jQuery: 'jquery',
      $: 'jquery',
      'window.jQuery': 'jquery',
      'window.$': 'jquery'
    })
  ]
}

// frontend config
const frontend = {
  name: 'frontend',
  entry: './src/Frontend/Core/Js/Index.js',
  output: {
    path: path.resolve(__dirname, './src/Frontend/Core/build'),
    filename: 'frontend.bundle.js'
  }
}

// backend config
const backend = {
  name: 'backend',
  entry: './src/Backend/Core/Js/Index.js',
  output: {
    path: path.resolve(__dirname, './src/Backend/Core/build'),
    filename: 'backend.bundle.js'
  }
}

// export configs
module.exports = [
  Object.assign({}, common, frontend),
  Object.assign({}, common, backend)
]
