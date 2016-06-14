var autoprefixer = require('gulp-autoprefixer'),
    clean = require('gulp-clean'),
    consolidate = require('gulp-consolidate'),
    fontgen = require('gulp-fontgen'),
    fs = require('fs'),
    gulp = require('gulp'),
    iconfont = require('gulp-iconfont'),
    imagemin = require('gulp-imagemin'),
    livereload = require('gulp-livereload'),
    plumber = require('gulp-plumber'),
    rename = require('gulp-rename'),
    runSequence = require('run-sequence'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    gulpWebpack = require('webpack-stream'),
    webpack = require('webpack');

var theme = JSON.parse(fs.readFileSync('./package.json')).theme;
var paths = {
  src:  'src/Frontend/Themes/' + theme + '/src',
  core: 'src/Frontend/Themes/' + theme + '/Core'
};

gulp.plumbedSrc = function(){
  return gulp.src.apply(gulp, arguments)
    .pipe(plumber());
};

gulp.task('clean', function() {
  return gulp.src([
    paths.core + '/Layout/Fonts/*',
    paths.core + '/Layout/Images/*',
    paths.core + '/Layout/Templates/*'
  ]).pipe(clean());
});

gulp.task('sass', function() {
  return gulp.plumbedSrc(paths.src + '/Layout/Sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: ['./node_modules/bootstrap-sass/assets/stylesheets']
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.core + '/Layout/Css'))
    .pipe(livereload());
});

gulp.task('sass:build', function() {
  return gulp.src(paths.src + '/Layout/Sass/*.scss')
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: ['./node_modules/bootstrap-sass/assets/stylesheets']
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(gulp.dest(paths.core + '/Layout/Css'));
});

gulp.task('fontgen', function() {
  return gulp.plumbedSrc(paths.src + '/Layout/Fonts/**/*.{ttf,otf}')
    .pipe(fontgen({
      options: {
        stylesheet: false
      },
      dest: paths.core + '/Layout/Fonts/'
    }))
    .pipe(livereload());
});

gulp.task('iconfont', function() {
  return gulp.plumbedSrc(paths.src + '/Layout/icon-sources/*.svg')
    .pipe(iconfont({fontName: 'icons'}))
    .on('glyphs', function(glyphs) {
      var options = {
        glyphs: glyphs,
        fontName: 'icons',
        fontPath: '../Fonts/',
        className: 'icon'
      };

      gulp.src(paths.src + '/Layout/Sass/_icons-template.scss')
        .pipe(consolidate('lodash', options))
        .pipe(rename({basename: '_icons'}))
        .pipe(gulp.dest(paths.src + '/Layout/Sass'));
    })
    .pipe(gulp.dest(paths.core + '/Layout/Fonts'))
    .pipe(livereload());
});

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
};

gulp.task('webpack', function() {
  return gulp.plumbedSrc(paths.src + '/Js/index.js')
    .pipe(gulpWebpack(Object.assign({}, commonWebpackConfig, {
      watch: true,
    })))
    .pipe(gulp.dest(paths.core + '/Js'))
    .pipe(livereload());
});

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
    .pipe(gulp.dest(paths.core + '/Js'));
});

gulp.task('copy:templates', function() {
  return gulp.plumbedSrc(paths.src + '/Layout/Templates/**/*')
    .pipe(gulp.dest(paths.core + '/Layout/Templates'))
    .pipe(livereload());
});

gulp.task('imagemin', function() {
  return gulp.plumbedSrc(paths.src + '/Layout/Images/**/*')
    .pipe(imagemin())
    .pipe(gulp.dest(paths.core + '/Layout/Images'))
    .pipe(livereload());
});

gulp.task('default', function() {
  livereload.listen();
  gulp.watch(paths.src + '/Js/**/*.js', ['webpack']);
  gulp.watch(paths.src + '/Layout/Sass/**/*.scss', ['sass']);
  gulp.watch(paths.src + '/Layout/Templates/**/*', ['copy:templates']);
  gulp.watch(paths.src + '/Layout/Images/**/*', ['imagemin']);
  gulp.watch(paths.src + '/Layout/icon-sources/*', ['iconfont']);
  gulp.watch(paths.src + '/Layout/Fonts/**/*', ['fontgen']);
});

gulp.task('serve', function() {
  gulp.start('default');
});

gulp.task('build', function() {
  runSequence('clean', ['iconfont', 'fontgen', 'sass:build', 'webpack:build', 'copy:templates', 'imagemin']);
});
