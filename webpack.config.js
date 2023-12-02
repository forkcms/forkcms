const Encore = require('@symfony/webpack-encore')
const LiveReloadPlugin = require('webpack-livereload-plugin')
const { execSync } = require('child_process')
const fs = require('fs')

const extensionConfig = JSON.parse(execSync('bin/console forkcms:extensions:webpack-config', (error, jsonConfig, stderr) => {
  if (error) {
    console.log(`error: ${error.message}`)
    return
  }
  if (stderr) {
    console.log(`stderr: ${stderr}`)
  }
}).toString())

const THEME_PATH = { output: 'public/assets/themes', public: '/assets/themes' }
const MODULE_PATH = { output: 'public/assets/modules', public: '/assets/modules' }
const EXPORTS = []

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'prod');
}

//
// THEMES SETUP
//
for (const THEME_CONFIG of extensionConfig.themes) {
  // Manually configure the runtime environment if not already configured yet by the "encore" command.
  // It's useful when you use tools that rely on webpack.config.js file.
  if (!Encore.isRuntimeEnvironmentConfigured()) {
    // Set the runtime environment
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
  }

  Encore
    .setOutputPath(`${THEME_PATH.output}`)
    .setPublicPath(`${THEME_PATH.public}/`)
    .configureImageRule()
    .configureFontRule()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .configureWatchOptions((watchOptions) => {
      // polling is useful when running Encore inside a Virtual Machine
      watchOptions.poll = 250
    })
    .enableBuildNotifications(true, (options) => {
      options.alwaysNotify = true
      options.title = 'Theme: ' + THEME_CONFIG.name
      options.emoji = true
      options.contentImage = 'public/apple-touch-icon.png'
    })
    .addPlugin(new LiveReloadPlugin())
    .disableSingleRuntimeChunk() // we will never load more than one team
    .enableVueLoader(() => {}, { runtimeCompilerBuild: true })
    .autoProvidejQuery()
    .autoProvideVariables({
      moment: 'moment'
    })
    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
      config.useBuiltIns = 'usage';
      config.corejs = '3.23';
    })
    .enableSassLoader((options) => {
    }, {
      resolveUrlLoader: true
    })
    .enablePostCssLoader()

  const COPY_FILES_CONFIGS = []
  for (const THEME_CONFIG of extensionConfig.themes) {
    const THEME_PUBLIC_DIR = `${THEME_CONFIG.path}/public`
    const THEME_JS_DIR = `${THEME_CONFIG.path}/webpack/js`
    const THEME_SCSS_DIR = `${THEME_CONFIG.path}/webpack/scss`
    if (fs.existsSync(THEME_PUBLIC_DIR)) {
      COPY_FILES_CONFIGS.push({
        from: THEME_PUBLIC_DIR,
        to: `./${THEME_CONFIG.name}/[path][name].[ext]`
      })
    }
    if (fs.existsSync(THEME_JS_DIR)) {
      fs.readdirSync(THEME_JS_DIR).forEach((file) => {
        if (file.endsWith('.js') && !file.startsWith('_')) {
          Encore.addEntry(`${THEME_CONFIG.name}/js/` + file.slice(0, -3), `${THEME_JS_DIR}/${file}`)
        }
      })
    }
    if (fs.existsSync(THEME_SCSS_DIR)) {
      fs.readdirSync(THEME_SCSS_DIR).forEach((file) => {
        if (file.endsWith('.scss') && !file.startsWith('_')) {
          Encore.addStyleEntry(`${THEME_CONFIG.name}/css/` + file.slice(0, -5), `${THEME_SCSS_DIR}/${file}`)
        }
      })
    }
  }
  Encore.copyFiles(COPY_FILES_CONFIGS)

  // build the second configuration
  const webpackConfig = Encore.getWebpackConfig()

  // Set a unique name for the config (needed later!)
  webpackConfig.name = 'ThemeConfig'

  EXPORTS.push(webpackConfig)
  Encore.reset()
}

//
// END THEMES SETUP
//
// ===========================
//
// START MODULES SETUP
//
for (const APPLICATION of ['Installer', 'Frontend', 'Backend']) {
  // Manually configure the runtime environment if not already configured yet by the "encore" command.
  // It's useful when you use tools that rely on webpack.config.js file.
  if (!Encore.isRuntimeEnvironmentConfigured()) {
    // Set the runtime environment
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
  }

  Encore
    .setOutputPath(`${MODULE_PATH.output}/${APPLICATION}`)
    .setPublicPath(`${MODULE_PATH.public}/${APPLICATION}/`)
    .configureImageRule()
    .configureFontRule()
    // enables @babel/preset-env polyfills
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .configureWatchOptions((watchOptions) => {
      // polling is useful when running Encore inside a Virtual Machine
      watchOptions.poll = 250
    })
    .enableBuildNotifications(true, (options) => {
      options.alwaysNotify = true
      options.title = 'Application: ' + APPLICATION
      options.emoji = true
      options.contentImage = 'public/apple-touch-icon.png'
    })
    .addPlugin(new LiveReloadPlugin())
    .enableSingleRuntimeChunk()
    .enableVueLoader(() => {}, { runtimeCompilerBuild: true })
    .autoProvidejQuery()
    .autoProvideVariables({
      moment: 'moment'
    })
    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
      config.useBuiltIns = 'usage';
      config.corejs = '3.23';
    })
    .enableSassLoader((options) => {
    }, {
      resolveUrlLoader: true
    })
    .enablePostCssLoader()

  const COPY_FILES_CONFIGS = []
  for (const MODULE_CONFIG of extensionConfig.modules) {
    const MODULE_PUBLIC_DIR = `${MODULE_CONFIG.path}/${APPLICATION}/public`
    const MODULE_APPLICATION_JS_DIR = `${MODULE_CONFIG.path}/${APPLICATION}/webpack/js`
    const MODULE_APPLICATION_SCSS_DIR = `${MODULE_CONFIG.path}/${APPLICATION}/webpack/scss`
    if (fs.existsSync(MODULE_PUBLIC_DIR)) {
      COPY_FILES_CONFIGS.push({
        from: MODULE_PUBLIC_DIR,
        to: `./${MODULE_CONFIG.name}/[path][name].[ext]`
      })
    }
    if (fs.existsSync(MODULE_APPLICATION_JS_DIR)) {
      fs.readdirSync(MODULE_APPLICATION_JS_DIR).forEach((file) => {
        if (file.endsWith('.js') && !file.startsWith('_')) {
          Encore.addEntry(`${MODULE_CONFIG.name}/js/` + file.slice(0, -3), `${MODULE_APPLICATION_JS_DIR}/${file}`)
        }
      })
    }
    if (fs.existsSync(MODULE_APPLICATION_SCSS_DIR)) {
      fs.readdirSync(MODULE_APPLICATION_SCSS_DIR).forEach((file) => {
        if (file.endsWith('.scss') && !file.startsWith('_')) {
          Encore.addStyleEntry(`${MODULE_CONFIG.name}/css/` + file.slice(0, -5), `${MODULE_APPLICATION_SCSS_DIR}/${file}`)
        }
      })
    }
  }
  Encore.copyFiles(COPY_FILES_CONFIGS)

  // build the second configuration
  const webpackConfig = Encore.getWebpackConfig()

  // Set a unique name for the config (needed later!)
  webpackConfig.name = APPLICATION + 'Config'

  EXPORTS.push(webpackConfig)
  Encore.reset()
}

//
// END MODULES SETUP

module.exports = EXPORTS
