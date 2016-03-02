const path = require('path');

const PATHS = {
  src: path.join(__dirname, 'src/Frontend/Themes/Bootstrap/src'),
  core: path.join(__dirname, 'src/Frontend/Themes/Bootstrap/Core')
}

module.exports = {
  entry: path.join(PATHS.src, 'Js'),
  output: {
    path: path.join(PATHS.core, 'Js'),
    filename: 'bundle.js'
  }
};
