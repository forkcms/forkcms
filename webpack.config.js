var webpack = require('webpack')

module.exports = {
  output: {
    filename: 'bundle.js'
  },
  resolve: {
    modules: ['node_modules'],
  },
  plugins: [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      Popper: ['popper.js', 'default']
    })
  ],
  module: {
    loaders: [
      {
        test: /\.js$|jsx/,
        loader: 'babel-loader',
        exclude: /node_modules/,
        query: {
          cacheDirectory: true,
          presets: ['@babel/preset-env']
        }
      },
      {
        // separate loader for Bootstrap because it needs to be compiled
        test: /bootstrap\.js$/,
        loader: 'babel-loader',
        query: {
          cacheDirectory: true,
          presets: ['@babel/preset-env']
        }
      }
    ]
  }
}
