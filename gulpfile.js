var fs = require('fs'),
    gulp = require('gulp'),
    livereload = require('gulp-livereload'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    webpack = require('webpack-stream')

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

gulp.task('webpack', function() {
  return gulp.src(paths.src + '/Js/index.js')
    .pipe(webpack({
      watch: true,
      output: {
        filename: 'bundle.js'
      },
      module: {
        loaders: [{
          test: /.js?$/,
          loader: 'babel',
          exclude: /node_modules/,
        }]
      }
    }))
    .pipe(gulp.dest(paths.core + '/Js'))
    .pipe(livereload())
})

gulp.task('default', function() {
  livereload.listen()
  gulp.watch(paths.src + '/Js/*.js', ['webpack'])
  gulp.watch(paths.src + '/Layout/Sass/*.scss', ['sass']);
})
