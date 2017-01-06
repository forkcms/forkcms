'use strict';

const gulp = require("gulp");
const sass = require("gulp-sass");
const sourcemaps = require("gulp-sourcemaps");

gulp.task("build:backend:assets:copy-js-vendors", function() {
  return gulp.src([
    "./node_modules/jquery/dist/jquery.min.js",
    "./node_modules/jquery-migrate/dist/jquery-migrate.min.js",
    "./node_modules/jquery-ui-dist/jquery-ui.min.js",
    "./node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js",
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
      .pipe(sourcemaps.write("./", {
        includeContent: false,
        sourceRoot: "/src/Backend/Core/Layout/Sass"
      }))
      .pipe(gulp.dest("./src/Backend/Core/Layout/Css"));
});

// public tasks
gulp.task("default", function() {
  gulp.start("build");
});

gulp.task("build", function() {
  gulp.start(
      "build:backend:assets:copy-js-vendors",
      "build:backend:sass:generate-css"
  );
});
