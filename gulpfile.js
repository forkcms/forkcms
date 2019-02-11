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
    'node_modules/cropper/dist/cropper.css',
    'node_modules/bootstrap-accessibility-plugin/plugins/css/bootstrap-accessibility.css'
  ])
  .pipe(gulp.dest('./css/vendors'))
})

gulp.task('build:backend:assets:copy-fonts-vendors', function () {
  return gulp.src([
    'node_modules/font-awesome/fonts/**'
  ])
  .pipe(gulp.dest('fonts/vendors'))
})

gulp.task('build:backend:assets:copy-ckeditor', function () {
  var pluginFiles = '/**/*.@(js|png|jpg|jpeg|gif|css|html|svg)'
  return gulp.src(
    [
      'node_modules/ckeditor/adapters/*.js',
      'node_modules/ckeditor/lang/*.js',
      'node_modules/ckeditor/plugins/icons.png',
      'node_modules/ckeditor/plugins/icons_hidpi.png',
      'node_modules/ckeditor/plugins/clipboard' + pluginFiles,
      'node_modules/ckeditor/plugins/codemirror' + pluginFiles,
      'node_modules/ckeditor/plugins/colordialog' + pluginFiles,
      'node_modules/ckeditor/plugins/copyformatting' + pluginFiles,
      'node_modules/ckeditor/plugins/dialog' + pluginFiles,
      'node_modules/ckeditor/plugins/dialogadvtab' + pluginFiles,
      'node_modules/ckeditor/plugins/div' + pluginFiles,
      'node_modules/ckeditor/plugins/docprops' + pluginFiles,
      'node_modules/ckeditor/plugins/iframe' + pluginFiles,
      'node_modules/ckeditor/plugins/iframe' + pluginFiles,
      'node_modules/ckeditor/plugins/image' + pluginFiles,
      'node_modules/ckeditor/plugins/link' + pluginFiles,
      'node_modules/ckeditor/plugins/liststyle' + pluginFiles,
      'node_modules/ckeditor/plugins/pastefromword' + pluginFiles,
      'node_modules/ckeditor/plugins/specialchar' + pluginFiles,
      'node_modules/ckeditor/plugins/table' + pluginFiles,
      'node_modules/ckeditor/plugins/tabletools' + pluginFiles,
      'node_modules/ckeditor/plugins/stylesheetparser' + pluginFiles,
      'node_modules/ckeditor/plugins/templates' + pluginFiles,
      'node_modules/ckeditor/plugins/uicolor' + pluginFiles,
      'node_modules/ckeditor/plugins/widget' + pluginFiles,
      'node_modules/ckeditor/plugins/wsc' + pluginFiles,
      'node_modules/ckeditor/plugins/lineutils' + pluginFiles,
      'node_modules/ckeditor/skins/moono-lisa/**/*.@(css|png|gif)',
      'node_modules/ckeditor/ckeditor.js',
      'node_modules/ckeditor/contents.css',
      'node_modules/ckeditor/styles.js',
      'node_modules/ckeditor/LICENSE.md'
    ],
    {base: 'node_modules/ckeditor'}
  ).pipe(gulp.dest('src/Backend/Core/Js/ckeditor'))
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
    'node_modules/bootstrap/dist/js/bootstrap.bundle.js',
    'node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js',
    'node_modules/fine-uploader/jquery.fine-uploader/jquery.fine-uploader.min.js',
    'node_modules/simple-ajax-uploader/SimpleAjaxUploader.min.js',
    'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
    'node_modules/cropper/dist/cropper.js',
    'node_modules/bootstrap-accessibility-plugin/plugins/js/bootstrap-accessibility.min.js'
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
    'build:backend:sass:generate-css',
    'build:backend:assets:copy-ckeditor'
  )
})

gulp.task('serve:backend', function () {
  livereload.listen()
  gulp.watch(
    [
      'src/Backend/Core/Layout/Sass/**/*.scss'
    ],
    ['build:backend:sass:generate-css']
  )
})

// frontend tasks
gulp.task('build:frontend:assets:copy-images-vendors', function () {
  var components = {
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

gulp.task('build:frontend:assets:copy-photoswipe-css-and-images', function () {
  return gulp.src([
    'node_modules/photoswipe/dist/photoswipe.css',
    'node_modules/photoswipe/dist/default-skin/*.{png,svg,gif,jpg,css}'
  ])
    .pipe(gulp.dest('css/vendors/photoswipe'))
})

gulp.task('build:frontend:sass:generate-css', function () {
  gulp.src([
    'node_modules/bootstrap-accessibility-plugin/plugins/css/bootstrap-accessibility.css'
  ]).pipe(gulp.dest('./css/vendors'))
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
    'build:frontend:assets:copy-photoswipe-css-and-images',
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

// public tasks
gulp.task('default', function () {
  gulp.start('build')
})

gulp.task('serve', function () {
  gulp.start(
    'serve:backend',
    'serve:frontend',
    'serve:theme-fork'
  )
})

gulp.task('build', function () {
  gulp.start(
    'build:backend',
    'build:frontend',
    'build:theme-fork'
  )
})
