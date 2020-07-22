/*
To set up dev:
1. install npm and gulp globally.
2. $ npm install
3. $ gulp
4. make changes.
5. $ gulp all
 */

// Directories
const vendor_src = 'src/vendor/';
const js_src = 'src/js/**/*.js';
const scss_src = 'src/scss/**/*.scss';
const img_src = 'src/images/*';
const js_dest = 'public/admin/js/';
const css_dest = 'public/admin/css/';
const img_dest = 'public/admin/images/';

// Dependencies.
const gulp = require('gulp');
const uglify = require('gulp-uglify');
const clean = require('gulp-clean');
const concat = require('gulp-concat');
const striplog = require('gulp-strip-debug');
const cleancss = require('gulp-clean-css');
const sass = require('gulp-sass');
const notify = require("gulp-notify");
const imagemin = require('gulp-imagemin');
const errorHandler = require('gulp-error-handle');
const rename = require('gulp-rename');

// Clean js destination dir.
gulp.task('clean.js', function() {
  return gulp.src([js_dest + '*.js'], {read: false})
    .pipe(errorHandler())
    .pipe(clean());
});

// Clean css destination dir.
gulp.task('clean.css', function() {
  return gulp.src([css_dest + '*.css'], {read: false})
    .pipe(errorHandler())
    .pipe(clean());
});

// Clean img destination dir.
gulp.task('clean.img', function() {
  return gulp.src([img_dest + '*'], {read: false})
    .pipe(errorHandler())
    .pipe(clean());
});

// Scripts.
gulp.task('js', ['clean.js'], function() {
  // Copy minified vendor js.
  gulp.src([vendor_src + '**/*.min.js'])
    .pipe(rename({dirname: ''}))
    .pipe(gulp.dest(js_dest));
  // Minify Gaterdata js and copy.
  gulp.src([js_src])
    .pipe(errorHandler())
    .pipe(concat('gaterdata.min.js'))
    .pipe(striplog())
    .pipe(uglify())
    .pipe(gulp.dest(js_dest))
});

// Styles.
gulp.task('css', ['clean.css'], function() {
  // Copy minified vendor css.
  gulp.src([vendor_src + '**/*.min.css'])
    .pipe(rename({dirname: ''}))
    .pipe(gulp.dest(css_dest));
  // Minify Gaterdata sass, minify and copy.
  gulp.src([scss_src])
    .pipe(errorHandler())
    .pipe(sass({style: 'compressed', errLogToConsole: true}))
    .pipe(concat('gaterdata.min.css'))
    .pipe(cleancss())
    .pipe(gulp.dest(css_dest))
});

// Images.
gulp.task('img', ['clean.img'], function() {
  // Minify images and copy.
  gulp.src(img_src)
      .pipe(errorHandler())
      .pipe(imagemin())
      .pipe(gulp.dest(img_dest))
});

// Default task, setup watch.
gulp.task('watch', function(){
  gulp.watch(vendor_src, ['css', 'js']);
  gulp.watch(scss_src, ['css']);
  gulp.watch(js_src, ['js']);
  gulp.watch(img_src, ['img']);
  gulp.src('src/*').pipe(notify('An asset has changed'));
});

gulp.task('all', ['css', 'js', 'img']);

gulp.task('default', ['all', 'watch']);
