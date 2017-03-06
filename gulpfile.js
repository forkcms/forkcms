'use strict';

const gulp = require("gulp");
const sass = require("gulp-sass");
const sourcemaps = require("gulp-sourcemaps");
const autoprefixer = require("gulp-autoprefixer");

gulp.task("build:backend:assets:copy-css-vendors", function() {
  return gulp.src([
    "./node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css",
    "./node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css",
  ])
      .pipe(gulp.dest("./css/vendors"));
});

gulp.task("build:backend:assets:copy-fonts-vendors", function() {
  return gulp.src([
    "./node_modules/font-awesome/fonts/**",
  ])
      .pipe(gulp.dest("./fonts/vendors"));
});

gulp.task("build:backend:assets:copy-fine-uploader-css-and-images", function() {
  return gulp.src([
    "./node_modules/fine-uploader/jquery.fine-uploader/fine-uploader-new.min.css",
    "./node_modules/fine-uploader/jquery.fine-uploader/continue.gif",
    "./node_modules/fine-uploader/jquery.fine-uploader/edit.gif",
    "./node_modules/fine-uploader/jquery.fine-uploader/loading.gif",
    "./node_modules/fine-uploader/jquery.fine-uploader/pause.gif",
    "./node_modules/fine-uploader/jquery.fine-uploader/processing.gif",
    "./node_modules/fine-uploader/jquery.fine-uploader/retry.gif",
    "./node_modules/fine-uploader/jquery.fine-uploader/trash.gif",
  ])
      .pipe(gulp.dest("./css/vendors/fine-uploader"));
});

gulp.task("build:backend:assets:copy-js-vendors", function() {
  return gulp.src([
    "./node_modules/jquery/dist/jquery.min.js",
    "./node_modules/jquery-migrate/dist/jquery-migrate.min.js",
    "./node_modules/jquery-ui-dist/jquery-ui.min.js",
    "./node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js",
    "./node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js",
    "./node_modules/fine-uploader/jquery.fine-uploader/jquery.fine-uploader.min.js",
  ])
      .pipe(gulp.dest("./js/vendors"));
});

gulp.task("build:backend:sass:generate-css", function() {
  return gulp.src([
    "./src/Backend/Core/Layout/Sass/screen.scss",
    "./src/Backend/Core/Layout/Sass/debug.scss",
  ])
      .pipe(sourcemaps.init())
      .pipe(sass({
        includePaths: [
          "./node_modules/"
        ],
        outputStyle:  "compressed",
        precision:    10
      }))
      .pipe(autoprefixer({}))
      .pipe(sourcemaps.write("./", {
        includeContent: false,
        sourceRoot:     "/src/Backend/Core/Layout/Sass"
      }))
      .pipe(gulp.dest("./src/Backend/Core/Layout/Css"));
});

gulp.task("build:backend", function() {
  gulp.start(
      "build:backend:assets:copy-css-vendors",
      "build:backend:assets:copy-fonts-vendors",
      "build:backend:assets:copy-js-vendors",
      "build:backend:assets:copy-fine-uploader-css-and-images",
      "build:backend:sass:generate-css"
  );
});

// public tasks
gulp.task("default", function() {
  gulp.start("build");
});

gulp.task("build", function() {
  gulp.start(
      "build:backend"
  );
});
