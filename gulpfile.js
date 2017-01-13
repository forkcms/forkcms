/*jslint node: true */
"use strict";

const gulp = require("gulp");
const sass = require("gulp-sass");
const sourcemaps = require("gulp-sourcemaps");
const autoprefixer = require("gulp-autoprefixer");
const rename = require("gulp-rename");
const livereload = require("gulp-livereload");

// backend tasks
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

gulp.task("build:backend:assets:copy-js-vendors", function() {
  return gulp.src([
    "./node_modules/jquery/dist/jquery.min.js",
    "./node_modules/jquery-migrate/dist/jquery-migrate.min.js",
    "./node_modules/jquery-ui-dist/jquery-ui.min.js",
    "./node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js",
    "./node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js",
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
      .pipe(gulp.dest("./src/Backend/Core/Layout/Css"))
      .pipe(livereload());
});

gulp.task("build:backend", function() {
  gulp.start(
      "build:backend:assets:copy-css-vendors",
      "build:backend:assets:copy-fonts-vendors",
      "build:backend:assets:copy-js-vendors",
      "build:backend:sass:generate-css"
  );
});

gulp.task("serve:backend", function() {
  livereload.listen();
  gulp.watch([
        "./src/Backend/Core/Layout/Sass/screen.scss",
        "./src/Backend/Core/Layout/Sass/debug.scss",
      ],
      ["build:backend:sass:generate-css"]
  );
});

// frontend tasks
gulp.task("build:frontend:sass:generate-css", function() {
  return gulp.src([
    "./src/Frontend/Core/Layout/Sass/debug.scss",
    "./src/Frontend/Core/Layout/Sass/editor_content.scss",
    "./src/Frontend/Core/Layout/Sass/screen.scss",
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
        sourceRoot:     "/src/Frontend/Core/Layout/Sass"
      }))
      .pipe(gulp.dest("./src/Frontend/Core/Layout/Css"))
      .pipe(livereload());
});

gulp.task("build:frontend:sass:generate-module-css", function() {
  return gulp.src([
    "./src/Frontend/Modules/**/Layout/Sass/*.scss",
  ])
      .pipe(sass({
        includePaths: [
          "./node_modules/"
        ],
        outputStyle:  "compressed",
        precision:    10
      }))
      .pipe(autoprefixer({}))
      .pipe(rename(function(path) {
        path.dirname = path.dirname.replace("/Sass", "/Css");
      }))
      .pipe(gulp.dest("./src/Frontend/Modules/"))
      .pipe(livereload());
});

gulp.task("build:frontend", function() {
  gulp.start(
      "build:frontend:sass:generate-css",
      "build:frontend:sass:generate-module-css"
  );
});

gulp.task("serve:frontend", function() {
  livereload.listen();
  gulp.watch([
        "./src/Frontend/Modules/**/Layout/Sass/*.scss"
      ],
      ["build:frontend:sass:generate-module-css"]
  );
  gulp.watch([
        "./src/Frontend/Core/Layout/Sass/debug.scss",
        "./src/Frontend/Core/Layout/Sass/editor_content.scss",
        "./src/Frontend/Core/Layout/Sass/screen.scss",
      ],
      ["build:frontend:sass:generate-css"]
  );
});

// public tasks
gulp.task("default", function() {
  gulp.start("build");
});

gulp.task("serve", function() {
  gulp.start("serve:backend");
  gulp.start("serve:frontend");
});

gulp.task("build", function() {
  gulp.start(
      "build:backend",
      "build:frontend"
  );
});
