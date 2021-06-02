module.exports = {
  plugins: {
    'postcss-import': {},
    'postcss-cssnext': {
      browsers: ['last 2 versions'],
      features: {
        customProperties: {
          warnings: false
        }
      }
    },
    'postcss-clean': {
      level: 2
    }
  }
}
