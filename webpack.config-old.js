const webpack = require('webpack')
const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin')
const ManifestPlugin = require('webpack-manifest-plugin');

// common config
const common = {
  devtool: 'source-map',
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
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true
            }
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true
            }
          }
        ]
      }
    ]
  },
  optimization: {
    minimizer: [new OptimizeCSSAssetsPlugin({})]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].css',
      chunkFilename: '[id].css'
    }),

    new ManifestPlugin(),

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