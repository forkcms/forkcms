const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const extractCSS = new ExtractTextPlugin('../Layout/Css/[name].css');

const PATHS = {
  src: path.join(__dirname, 'src/Frontend/Themes/Bootstrap/src'),
  core: path.join(__dirname, 'src/Frontend/Themes/Bootstrap/Core')
}

module.exports = {
  entry: path.join(PATHS.src, 'Js'),
  output: {
    path: path.join(PATHS.core, 'Js'),
    filename: 'bundle.js'
  },
  module: {
    loaders: [
      {
        test: /\.scss$/,
        loader: extractCSS.extract(['css', 'sass'])
      }
    ]
  },
  plugins: [
    extractCSS
  ],
  sassLoader: {
    includePaths: [path.resolve(__dirname, './node_modules/bootstrap-sass/assets/stylesheets')]
  }
};
