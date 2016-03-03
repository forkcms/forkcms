var fs = require('fs'),
    gulp = require('gulp'),
    livereload = require('gulp-livereload'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    gulpWebpack = require('webpack-stream'),
    webpack = require('webpack')

var theme = JSON.parse(fs.readFileSync('./package.json')).theme;
var paths = {
  src:  './src/Frontend/Themes/' + theme + '/src',
  core: './src/Frontend/Themes/' + theme + '/Core',
}

gulp.task('sass', function() {
  return gulp.src(paths.src + '/Layout/Sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: ['./node_modules/bootstrap-sass/assets/stylesheets']
    }).on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.core + '/Layout/Css'))
    .pipe(livereload())
})

gulp.task('sass:build', function() {
  return gulp.src(paths.src + '/Layout/Sass/*.scss')
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: ['./node_modules/bootstrap-sass/assets/stylesheets']
    }).on('error', sass.logError))
    .pipe(gulp.dest(paths.core + '/Layout/Css'))
})

var commonWebpackConfig = {
  output: {
    filename: 'bundle.js',
  },
  module: {
    loaders: [{
      test: /.js?$/,
      loader: 'babel',
      exclude: /node_modules/,
    }]
  }
}

var commonWebpackConfig = {
  output: {
    filename: 'bundle.js'
  },
  module: {
    loaders: [{
      test: /.js?$/,
      loader: 'babel',
      exclude: /node_modules/,
    }]
  },
}

gulp.task('webpack', function() {
  return gulp.src(paths.src + '/Js/index.js')
    .pipe(gulpWebpack(Object.assign({}, commonWebpackConfig, {
      watch: true,
    })))
    .pipe(gulp.dest(paths.core + '/Js'))
    .pipe(livereload())
})

gulp.task('webpack:build', function() {
  return gulp.src(paths.src + '/Js/index.js')
    .pipe(gulpWebpack(Object.assign({}, commonWebpackConfig, {
      plugins: [
        new webpack.optimize.UglifyJsPlugin({
          compress: {
            warnings: false
          }
        }),
        new webpack.DefinePlugin({
          'process.env.NODE_ENV': '"production"'
        })
      ]
    }, webpack)))
    .pipe(gulp.dest(paths.core + '/Js'))
})

gulp.task('copy:templates', function() {
  return gulp
    .src(paths.src + '/Layout/Templates/*')
    .pipe(gulp.dest(paths.core + '/Layout/Templates'))
    .pipe(livereload())
})

gulp.task('default', function() {
  livereload.listen()
  gulp.watch(paths.src + '/Js/*.js', ['webpack'])
  gulp.watch(paths.src + '/Layout/Sass/*.scss', ['sass']);
  gulp.watch(paths.src + '/Layout/Templates/*', ['copy:templates'])
})

gulp.task('build', function() {
  gulp.start('sass:build', 'webpack:build', 'copy:templates')
})
