var gulp = require('gulp'),
    livereload = require('gulp-livereload'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    webpack = require('webpack-stream')

gulp.task('sass', function() {
  return gulp.src('./src/Frontend/Themes/Bootstrap/src/Layout/Sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: ['./node_modules/bootstrap-sass/assets/stylesheets']
    }).on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./src/Frontend/Themes/Bootstrap/Core/Layout/Css'))
    .pipe(livereload())
})

gulp.task('sass:watch', function() {
  gulp.watch('./src/Frontend/Themes/Bootstrap/src/Layout/Sass/*.scss', ['sass']);
})

gulp.task('webpack', function() {
  return gulp.src('./src/Frontend/Themes/Bootstrap/src/Js/index.js')
    .pipe(webpack({
      watch: true,
      output: {
        filename: 'bundle.js'
      }
    }))
    .pipe(gulp.dest('./src/Frontend/Themes/Bootstrap/Core/Js'))
    .pipe(livereload())
})

gulp.task('default', function() {
  livereload.listen()
  gulp.start('webpack')
  gulp.watch('./src/Frontend/Themes/Bootstrap/src/Layout/Sass/*.scss', ['sass']);
})
