var gulp = require("gulp");

gulp.task("assets:copy_js_assets_from_node_modules", function() {
  return gulp.src([
    "./node_modules/jquery/dist/jquery.min.js",
    "./node_modules/jquery-migrate/dist/jquery-migrate.min.js",
    "./node_modules/jquery-ui-dist/jquery-ui.min.js"
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
