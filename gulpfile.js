// Dependencies.
const gulp = require('gulp');
const uglify = require('gulp-uglify');
const clean = require('gulp-clean');
const concat = require('gulp-concat');
const striplog = require('gulp-strip-debug');
const cleancss = require('gulp-clean-css');
const sass = require('gulp-sass');
const notify = require("gulp-notify");
const connect = require('gulp-connect');
const imagemin = require('gulp-imagemin');
const errorHandler = require('gulp-error-handle');
// Directories
const js_src = 'src/js/';
const css_src = 'src/css/';
const scss_src = 'src/scss/**/*.scss';
const img_src = 'src/images/*';
const js_dest = 'html/admin/js';
const css_dest = 'html/admin/css';
const img_dest = 'html/admin/images';

// Js files.
gulp.task('scripts', function() {
  return gulp.src([js_src + 'jquery.min.js', js_src + 'materialize.min.js', js_src + '**/*.js'])
    .pipe(errorHandler())
    .pipe(concat('gaterdata.min.js'))
    .pipe(striplog())
    .pipe(uglify())
    .pipe(gulp.dest(js_dest))
});

// CSS and SCSS files.
gulp.task('styles', function() {
  return gulp.src([css_src + 'materialize.min.css', css_src + '**/*.js', scss_src])
    .pipe(errorHandler())
    .pipe(sass({style: 'compressed', errLogToConsole: true}))
    .pipe(concat('gaterdata.min.css'))
    .pipe(cleancss())
    .pipe(gulp.dest(css_dest))
});

// Images.
gulp.task('images', function() {
    return gulp.src(img_src)
      .pipe(imagemin())
      .pipe(gulp.dest(img_dest))
});

// Clean all builds.
gulp.task('clean', function() {
  return gulp.src(['html/admin/js', 'html/admin/css', 'html/admin/images', ], {read: false})
    .pipe(errorHandler())
    .pipe(clean());
});

// Web server.
gulp.task('webserver', function() {
  connect.server();
});

// Default task - clean the build dir
// Then rebuild the js and css files

gulp.task('watch', function(){
  gulp.watch([scss_src, css_src], ['styles']);
  gulp.watch(js_src, ['scripts']);
  gulp.watch(img_src, ['images']);
  gulp.src('src/*').pipe(notify('An asset has changed'));
});

gulp.task('default', ['webserver', 'clean', 'styles', 'scripts', 'images', 'watch']);
