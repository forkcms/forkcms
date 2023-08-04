module.exports = {
  plugins: {
    'postcss-import': {},
    'postcss-preset-env': {
      browsers: ['last 2 versions'],
      features: {
        'custom-properties': {
          warnings: false
        }
      }
    }
  }
}
