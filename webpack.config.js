const webpack = require('webpack')
const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const glob = require('glob')

const publicPath = `/src/Backend/Core/build/`
const entryArray = glob.sync('./src/Backend/Modules/**/Js/Index.js')

const entryObject = entryArray.reduce((acc, item) => {
  const name = item.replace('/Js/Index.js', '')
  acc[name] = item
  return acc
}, {})

const rules = {
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
}
const plugins = [
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

module.exports = [
  {
    name: 'modules',
    mode: 'development',
    entry: entryObject,
    output: {
      filename: '[name]/build/index.js',
      path: path.resolve(__dirname, '')
    },
    module: rules,
    plugins: plugins,
    watch: true
  },
  {
    name: 'backend',
    mode: 'development',
    entry: './src/Backend/Core/Js/Backend.js',
    output: {
      path: path.resolve(__dirname, './src/Backend/Core/build'),
      filename: 'backend.bundle.js'
    },
    module: rules,
    plugins: plugins,
    watch: true
  }
]
