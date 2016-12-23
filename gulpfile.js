var gulp = require("gulp"),
    gutil = require("gulp-util");

gulp.task("assets:copy_js_assets_from_node_modules", function() {
  return gulp.src([
    "./node_modules/jquery/dist/jquery.min.*",
    "./node_modules/jquery-migrate/dist/jquery-migrate.min.*"
  ])
      .pipe(gulp.dest("./js/vendors"));
});

// public tasks
gulp.task("default", function() {
  gulp.start("build");
});

gulp.task("build", function() {
  gulp.start("assets:copy_js_assets_from_node_modules");
});
