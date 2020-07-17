const Encore = require('@symfony/webpack-encore')

Encore
  .setOutputPath('src/Backend/Core/build/')
  .setPublicPath('/src/Backend/Core/build')
  .addEntry('backend', './src/Backend/Core/Js/Backend.js')
  .addStyleEntry('screen', './src/Backend/Core/Layout/Sass/screen.scss')
  .enableSassLoader((options) => {}, {
    resolveUrlLoader: false
  })
  .enablePostCssLoader()
  // enables @babel/preset-env polyfills
  .autoProvidejQuery()
  .copyFiles({
    from: '/src/Backend/Core/images',
    to: '/src/Backend/Core/Build/images/[path][name].[ext]'
  })
  .enableSourceMaps(!Encore.isProduction())

// build the first configuration
const backendConfig = Encore.getWebpackConfig()

// Set a unique name for the config (needed later!)
backendConfig.name = 'backendConfig'

// reset Encore to build the second config
Encore.reset()

// define the second configuration
Encore
  .setOutputPath('src/Frontend/Core/build/')
  .setPublicPath('/src/Frontend/Core/build')
  .addEntry('frontend', './src/Frontend/Core/Js/Index.js')
  .autoProvidejQuery()
  .enableSourceMaps(!Encore.isProduction())

// build the second configuration
const frontendConfig = Encore.getWebpackConfig()

// Set a unique name for the config (needed later!)
frontendConfig.name = 'frontendConfig'

module.exports = [backendConfig, frontendConfig]
