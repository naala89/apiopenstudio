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
const js_src = 'src/js/**/*.js';
const css_src = 'src/js/**/*.js';
const scss_src = 'src/js/**/*.js';
const js_dest = 'html/admin/js';
const css_dest = 'html/admin/js';

//js files
gulp.task('scripts', function() {
  return gulp.src(js_src)
    .pipe(errorHandler())
    .pipe(concat('gaterdata.min.js'))
    .pipe(striplog())
    .pipe(uglify())
    .pipe(gulp.dest(js_dest))
});

gulp.task('styles', function() {
  return gulp.src([scss_src, css_src])
    .pipe(errorHandler())
    .pipe(sass({style: 'compressed', errLogToConsole: true}))
    .pipe(concat('gaterdata.min.css'))
    .pipe(cleancss())                                         // Minify the CSS
    .pipe(gulp.dest(css_dest))                      // Set the destination to assets/css
});


gulp.task('images', function() {
    return gulp.src('src/images/*')
      .pipe(imagemin())
      .pipe(gulp.dest('html/admin/images'))
});

// Clean all builds
gulp.task('clean', function() {
  return gulp.src(['html/admin/'], {read: false})
    .pipe(errorHandler())
    .pipe(clean());
});

// web server
gulp.task('webserver', function() {
  connect.server();
});

// Default task - clean the build dir
// Then rebuild the js and css files

gulp.task('watch', function(){
  gulp
    .watch(['src/scss/**/*.scss','src/css/**/*.css'], ['styles']); // Watch and run sass on changes
  gulp
    .watch('src/js/**/*.js', ['scripts']); // Watch and run javascripts on changes
  gulp.src('src/*')
    .pipe(notify('An asset has changed'));
});

gulp.task('default', ['webserver', 'clean', 'styles', 'scripts', 'watch']);