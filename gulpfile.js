var gulp = require("gulp");

gulp.task("build:backend:assets:copy-js-vendors", function() {
  return gulp.src([
    "./node_modules/jquery/dist/jquery.min.js",
    "./node_modules/jquery-migrate/dist/jquery-migrate.min.js",
    "./node_modules/jquery-ui-dist/jquery-ui.min.js",
    "./node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js",
  ])
      .pipe(gulp.dest("./js/vendors"));
});

// public tasks
gulp.task("default", function() {
  gulp.start("build");
});

gulp.task("build", function() {
  gulp.start("build:backend:assets:copy-js-vendors");
});
