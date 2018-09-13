// Supported browsers.
const AUTOPREFIXER_BROWSERS = [
  'ie >= 10',
  'ie_mob >= 10',
  'ff >= 30',
  'chrome >= 34',
  'safari >= 7',
  'opera >= 23',
  'ios >= 7',
  'android >= 4.4',
  'bb >= 10'
];

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
const copy = require('gulp-copy');

// Directories
const vendor_js = 'src/vendor/js/';
const vendor_css = 'src/vendor/css/';
const js_src = 'src/js/';
const scss_src = 'src/scss/**/*.scss';
const img_src = 'src/images/*';
const js_dest = 'html/admin/js';
const css_dest = 'html/admin/css';
const img_dest = 'html/admin/images';

// Clean all builds.
gulp.task('clean', function() {
  return gulp.src([js_dest, css_dest, img_dest], {read: false})
    .pipe(errorHandler())
    .pipe(clean());
});

// Vendor JS.
gulp.task('copy.js', function () {
  return gulp.src([vendor_js + '**/*.js'])
    .pipe(gulp.dest(js_dest));
});

// Custom Js files.
gulp.task('scripts', function() {
  return gulp.src([js_src + '**/*.js'])
    .pipe(errorHandler())
    .pipe(concat('gaterdata.min.js'))
    .pipe(striplog())
    .pipe(uglify())
    .pipe(gulp.dest(js_dest))
});

// Vendor CSS.
gulp.task('copy.css', function () {
  return gulp.src([vendor_css + '**/*.css'])
    .pipe(gulp.dest(css_dest));
});

// Custom SCSS files.
gulp.task('styles', function() {
  return gulp.src([scss_src])
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

// Web server.
gulp.task('webserver', function() {
  connect.server();
});

// Default task - clean the build dir
// Then rebuild the js and css files

gulp.task('watch', function(){
  gulp.watch(vendor_css, ['copy.css']);
  gulp.watch([scss_src], ['styles']);
  gulp.watch(vendor_js, ['copy.js']);
  gulp.watch(js_src, ['scripts']);
  gulp.watch(img_src, ['images']);
  gulp.src('src/*').pipe(notify('An asset has changed'));
});

gulp.task('default', ['webserver', 'clean', 'copy.css', 'styles', 'copy.js', 'scripts', 'images', 'watch']);
