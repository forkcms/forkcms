/* jslint node: true */
'use strict'

const gulp = require('gulp')
const sass = require('gulp-sass')
const sourcemaps = require('gulp-sourcemaps')
const autoprefixer = require('gulp-autoprefixer')
const rename = require('gulp-rename')
const livereload = require('gulp-livereload')

// backend tasks
gulp.task('build:backend:assets:copy-css-vendors', function () {
  return gulp.src([
    'node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css',
    'node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css',
    'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.standalone.min.css',
    'node_modules/cropper/dist/cropper.css'
  ])
    .pipe(gulp.dest('./css/vendors'))
})

gulp.task('build:backend:assets:copy-fonts-vendors', function () {
  return gulp.src([
    'node_modules/font-awesome/fonts/**'
  ])
    .pipe(gulp.dest('fonts/vendors'))
})

gulp.task('build:backend:assets:copy-fine-uploader-css-and-images', function () {
  return gulp.src([
    'node_modules/fine-uploader/jquery.fine-uploader/fine-uploader-new.min.css',
    'node_modules/fine-uploader/jquery.fine-uploader/continue.gif',
    'node_modules/fine-uploader/jquery.fine-uploader/edit.gif',
    'node_modules/fine-uploader/jquery.fine-uploader/loading.gif',
    'node_modules/fine-uploader/jquery.fine-uploader/pause.gif',
    'node_modules/fine-uploader/jquery.fine-uploader/processing.gif',
    'node_modules/fine-uploader/jquery.fine-uploader/retry.gif',
    'node_modules/fine-uploader/jquery.fine-uploader/trash.gif',
    'node_modules/fine-uploader/jquery.fine-uploader/placeholders/waiting-generic.png',
    'node_modules/fine-uploader/jquery.fine-uploader/placeholders/not_available-generic.png'
  ])
    .pipe(gulp.dest('./css/vendors/fine-uploader'))
})

gulp.task('build:backend:assets:copy-js-vendors', function () {
  return gulp.src([
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/jquery-migrate/dist/jquery-migrate.min.js',
    'node_modules/jquery-ui-dist/jquery-ui.min.js',
    'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
    'node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js',
    'node_modules/fine-uploader/jquery.fine-uploader/jquery.fine-uploader.min.js',
    'node_modules/simple-ajax-uploader/SimpleAjaxUploader.min.js',
    'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
    'node_modules/cropper/dist/cropper.js'
  ])
    .pipe(gulp.dest('js/vendors'))
})

gulp.task('build:backend:sass:generate-css', function () {
  return gulp.src([
    'src/Backend/Core/Layout/Sass/screen.scss',
    'src/Backend/Core/Layout/Sass/debug.scss'
  ])
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [
        'node_modules/'
      ],
      outputStyle: 'compressed',
      precision: 10
    }))
    .pipe(autoprefixer({}))
    .pipe(sourcemaps.write('./', {
      includeContent: false,
      sourceRoot: 'src/Backend/Core/Layout/Sass'
    }))
    .pipe(gulp.dest('src/Backend/Core/Layout/Css'))
    .pipe(livereload())
})

gulp.task('build:backend', function () {
  gulp.start(
    'build:backend:assets:copy-css-vendors',
    'build:backend:assets:copy-fonts-vendors',
    'build:backend:assets:copy-js-vendors',
    'build:backend:assets:copy-fine-uploader-css-and-images',
    'build:backend:sass:generate-css'
  )
})

gulp.task('serve:backend', function () {
  livereload.listen()
  gulp.watch(
    [
      'src/Backend/Core/Layout/Sass/screen.scss',
      'src/Backend/Core/Layout/Sass/debug.scss'
    ],
    ['build:backend:sass:generate-css']
  )
})

// frontend tasks
gulp.task('build:frontend:assets:copy-images-vendors', function () {
  var components = {
    'photoswipe': [
      'node_modules/photoswipe/dist/default-skin/*.{png,svg,gif,jpg}'
    ]
  }

  for (var key in components) {
    return gulp.src(components[key]).pipe(gulp.dest('./images/vendors/' + key))
  }
})

gulp.task('build:frontend:assets:copy-js-vendors', function () {
  return gulp.src([
    'node_modules/photoswipe/dist/photoswipe.min.js',
    'node_modules/photoswipe/dist/photoswipe-ui-default.min.js',
    'node_modules/slick-carousel/slick/slick.min.js'
  ])
    .pipe(gulp.dest('js/vendors'))
})

gulp.task('build:frontend:sass:generate-css', function () {
  return gulp.src([
    'src/Frontend/Core/Layout/Sass/debug.scss',
    'src/Frontend/Core/Layout/Sass/editor_content.scss',
    'src/Frontend/Core/Layout/Sass/screen.scss'
  ])
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [
        'node_modules/'
      ],
      outputStyle: 'compressed',
      precision: 10
    }))
    .pipe(autoprefixer({}))
    .pipe(sourcemaps.write('./', {
      includeContent: false,
      sourceRoot: 'src/Frontend/Core/Layout/Sass'
    }))
    .pipe(gulp.dest('src/Frontend/Core/Layout/Css'))
    .pipe(livereload())
})

gulp.task('build:frontend:sass:generate-module-css', function () {
  return gulp.src([
    'src/Frontend/Modules/**/Layout/Sass/*.scss'
  ])
    .pipe(sass({
      includePaths: [
        'node_modules/'
      ],
      outputStyle: 'compressed',
      precision: 10
    }))
    .pipe(autoprefixer({}))
    .pipe(rename(function (path) {
      path.dirname = path.dirname.replace('/Sass', '/Css')
    }))
    .pipe(gulp.dest('src/Frontend/Modules/'))
    .pipe(livereload())
})

gulp.task('build:frontend', function () {
  gulp.start(
    'build:frontend:assets:copy-images-vendors',
    'build:frontend:assets:copy-js-vendors',
    'build:frontend:sass:generate-css',
    'build:frontend:sass:generate-module-css'
  )
})

gulp.task('serve:frontend', function () {
  livereload.listen()
  gulp.watch(
    [
      'src/Frontend/Modules/**/Layout/Sass/*.scss'
    ],
    ['build:frontend:sass:generate-module-css']
  )
  gulp.watch(
    [
      'src/Frontend/Core/Layout/Sass/debug.scss',
      'src/Frontend/Core/Layout/Sass/editor_content.scss',
      'src/Frontend/Core/Layout/Sass/screen.scss'
    ],
    ['build:frontend:sass:generate-css']
  )
})

// Fork-theme tasks
gulp.task('build:theme-fork:sass:generate-css', function () {
  return gulp.src([
    'src/Frontend/Themes/Fork/Core/Layout/Sass/screen.scss'
  ])
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [
        'node_modules/'
      ],
      outputStyle: 'compressed',
      precision: 10
    }))
    .pipe(autoprefixer({}))
    .pipe(sourcemaps.write('./', {
      includeContent: false,
      sourceRoot: 'src/Frontend/Themes/Fork/Core/Layout/Sass'
    }))
    .pipe(gulp.dest('src/Frontend/Themes/Fork/Core/Layout/Css'))
    .pipe(livereload())
})

gulp.task('build:theme-fork', function () {
  gulp.start(
    'build:theme-fork:sass:generate-css'
  )
})

gulp.task('serve:theme-fork', function () {
  livereload.listen()
  gulp.watch(
    [
      'src/Frontend/Themes/Fork/Core/Layout/Sass/**/*.scss'
    ],
    ['build:theme-fork:sass:generate-css']
  )
})

// @remark: custom for SumoCoders
const fs = require('fs')
const del = require('del')
const plumber = require('gulp-plumber')
const iconfont = require('gulp-iconfont')
const consolidate = require('gulp-consolidate')
const fontgen = require('gulp-fontgen')
const webpackStream = require('webpack-stream')
const webpack = require('webpack')
const imagemin = require('gulp-imagemin')

const theme = JSON.parse(fs.readFileSync('./package.json')).theme
const paths = {
  src: `src/Frontend/Themes/${theme}/src`,
  core: `src/Frontend/Themes/${theme}/Core`
}

gulp.plumbedSrc = function () {
  return gulp.src.apply(gulp, arguments)
    .pipe(plumber())
}

gulp.task('build:assets:copy-images-vendors', function () {
  return gulp.src([
    './node_modules/fancybox/dist/img/*'
  ])
    .pipe(gulp.dest('./images/vendors/fancybox'))
})

gulp.task('build:theme:empty-destination-folders', function () {
  return del([
    `${paths.core}/Layout/Fonts/**/*`,
    `${paths.core}/Layout/Images/**/*`,
    `${paths.core}/Layout/Templates/**/*`
  ])
})

gulp.task('build:theme:fonts:generate-iconfont', function () {
  return gulp.plumbedSrc(`${paths.src}/Layout/icon-sources/*.svg`)
    .pipe(iconfont({fontName: 'icons'}))
    .on('glyphs', function (glyphs) {
      var options = {
        glyphs: glyphs,
        fontName: 'icons',
        fontPath: '../Fonts/',
        className: 'icon'
      }

      gulp.src(`${paths.src}/Layout/Sass/_icons-template.scss`)
        .pipe(consolidate('lodash', options))
        .pipe(rename({basename: '_icons'}))
        .pipe(gulp.dest(`${paths.src}/Layout/Sass`))
    })
    .pipe(gulp.dest(`${paths.core}/Layout/Fonts`))
    .pipe(livereload())
})

gulp.task('build:theme:fonts:generate-webfonts', function () {
  return gulp.plumbedSrc(`${paths.src}/Layout/Fonts/**/*.{ttf,otf}`)
    .pipe(fontgen({
      options: {
        stylesheet: false
      },
      dest: `${paths.core}/Layout/Fonts/`
    }))
    .pipe(livereload())
})

gulp.task('build:theme:sass:generate-development-css', function () {
  return gulp.plumbedSrc(`${paths.src}/Layout/Sass/*.scss`)
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [
        './node_modules'
      ]
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('./', {
      includeContent: false,
      sourceRoot: `/src/Frontend/Themes/${theme}/src/Layout/Sass`
    }))
    .pipe(gulp.dest(`${paths.core}/Layout/Css`))
    .pipe(livereload())
})

gulp.task('build:theme:sass:generate-production-css', function () {
  return gulp.src(`${paths.src}/Layout/Sass/*.scss`)
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: [
        './node_modules'
      ]
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('./', {
      includeContent: false,
      sourceRoot: `/src/Frontend/Themes/${theme}/src/Layout/Sass`
    }))
    .pipe(gulp.dest(`${paths.core}/Layout/Css`))
})

var commonWebpackConfig = require('./webpack.config')

gulp.task('build:theme:webpack:generate-development-js', function () {
  return gulp.plumbedSrc(`${paths.src}/Js/Index.js`)
    .pipe(webpackStream(Object.assign({}, commonWebpackConfig, {
      watch: true
    })))
    .pipe(gulp.dest(`${paths.core}/Js`))
    .pipe(livereload())
})

gulp.task('build:theme:webpack:generate-production-js', function () {
  return gulp.src(`${paths.src}/Js/Index.js`)
    .pipe(webpackStream(commonWebpackConfig, webpack))
    .pipe(gulp.dest(`${paths.core}/Js`))
})

gulp.task('build:theme:assets:copy-templates', function () {
  return gulp.plumbedSrc(`${paths.src}/Layout/Templates/**/*`)
    .pipe(gulp.dest(`${paths.core}/Layout/Templates`))
    .pipe(livereload())
})

gulp.task('build:theme:images:minify-images', function () {
  return gulp.plumbedSrc(`${paths.src}/Layout/Images/**/*`)
    .pipe(imagemin())
    .pipe(gulp.dest(`${paths.core}/Layout/Images`))
    .pipe(livereload())
})

gulp.task('build:theme', ['build:theme:empty-destination-folders'], function () {
  gulp.start(
    'build:assets:copy-images-vendors',
    'build:theme:fonts:generate-iconfont',
    'build:theme:fonts:generate-webfonts',
    'build:theme:sass:generate-production-css',
    'build:theme:webpack:generate-production-js',
    'build:theme:assets:copy-templates',
    'build:theme:images:minify-images'
  )
})

gulp.task('serve:theme', function () {
  livereload.listen()
  gulp.watch(`${paths.src}/Js/**/*.js`, ['build:theme:webpack:generate-development-js'])
  gulp.watch(`${paths.src}/Layout/Sass/**/*.scss`, ['build:theme:sass:generate-development-css'])
  gulp.watch(`${paths.src}/Layout/Templates/**/*`, ['build:theme:assets:copy-templates'])
  gulp.watch(`${paths.src}/Layout/Images/**/*`, ['build:theme:images:minify-images'])
  gulp.watch(`${paths.src}/Layout/icon-sources/*`, ['build:theme:fonts:generate-iconfont'])
  gulp.watch(`${paths.src}/Layout/Fonts/**/*`, ['build:theme:fonts:generate-webfonts'])
})

// public tasks
gulp.task('default', function () {
  gulp.start('build')
})

gulp.task('serve', function () {
  gulp.start(
    'serve:backend',
    'serve:frontend',
    'serve:theme-fork',
    'serve:theme' // @remark custom for SumoCoders
  )
})

gulp.task('build', function () {
  gulp.start(
    'build:backend',
    'build:frontend',
    'build:theme-fork',
    'build:theme' // @remark custom for SumoCoders
  )
})
